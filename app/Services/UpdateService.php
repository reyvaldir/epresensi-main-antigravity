<?php

namespace App\Services;

use App\Models\Update;
use App\Models\UpdateLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class UpdateService
{
    /**
     * Get current application version
     */
    public function getCurrentVersion(): string
    {
        // Cek dari file version atau database
        $versionFile = base_path('VERSION');
        if (File::exists($versionFile)) {
            return trim(File::get($versionFile));
        }

        // Default version jika belum ada
        return '1.0.0';
    }

    /**
     * Set current application version
     */
    public function setCurrentVersion(string $version): void
    {
        File::put(base_path('VERSION'), $version);
    }

    /**
     * Check for updates from server
     */
    public function checkUpdate(string $updateServerUrl = null): array
    {
        try {
            $currentVersion = $this->getCurrentVersion();
            
            // Jika tidak ada URL server, cek dari database lokal
            if (!$updateServerUrl) {
                // Bandingkan versi menggunakan versi semver
                $latestUpdate = Update::active()
                    ->orderByRaw("CAST(SUBSTRING_INDEX(version, '.', 1) AS UNSIGNED) DESC, 
                                  CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 2), '.', -1) AS UNSIGNED) DESC,
                                  CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC")
                    ->first();
                
                // Cek apakah versi lebih baru
                if ($latestUpdate && version_compare($latestUpdate->version, $currentVersion, '>')) {
                    return [
                        'has_update' => true,
                        'current_version' => $currentVersion,
                        'latest_version' => $latestUpdate->version,
                        'update' => $latestUpdate,
                    ];
                }

                return [
                    'has_update' => false,
                    'current_version' => $currentVersion,
                    'latest_version' => $currentVersion,
                ];
            }

            // Check dari server eksternal
            $response = Http::timeout(30)->get($updateServerUrl . '/api/check-update', [
                'current_version' => $currentVersion,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'has_update' => $data['has_update'] ?? false,
                    'current_version' => $currentVersion,
                    'latest_version' => $data['latest_version'] ?? $currentVersion,
                    'update' => $data['update'] ?? null,
                ];
            }

            return [
                'has_update' => false,
                'current_version' => $currentVersion,
                'latest_version' => $currentVersion,
                'error' => 'Gagal menghubungi server update',
            ];
        } catch (\Exception $e) {
            Log::error('Error checking update: ' . $e->getMessage());
            return [
                'has_update' => false,
                'current_version' => $this->getCurrentVersion(),
                'latest_version' => $this->getCurrentVersion(),
                'error' => 'Terjadi kesalahan saat mengecek update: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Download update file
     */
    public function downloadUpdate(Update $update, UpdateLog $updateLog): bool
    {
        try {
            $updateLog->update([
                'status' => 'downloading',
                'started_at' => now(),
            ]);

            if (!$update->file_url) {
                throw new \Exception('URL file update tidak tersedia');
            }

            // Buat direktori untuk menyimpan file update
            $updateDir = storage_path('app/updates');
            if (!File::exists($updateDir)) {
                File::makeDirectory($updateDir, 0755, true);
            }

            $zipPath = $updateDir . '/update_' . $update->version . '.zip';

            // Download file
            $response = Http::timeout(300)->get($update->file_url);
            
            if (!$response->successful()) {
                throw new \Exception('Gagal mengunduh file update');
            }

            File::put($zipPath, $response->body());

            // Validasi checksum jika ada
            if ($update->checksum) {
                $fileChecksum = md5_file($zipPath);
                if ($fileChecksum !== $update->checksum) {
                    File::delete($zipPath);
                    throw new \Exception('Checksum file tidak valid. File mungkin rusak.');
                }
            }

            // Simpan path file ke update log
            $updateLog->update([
                'message' => 'File berhasil diunduh',
            ]);

            return true;
        } catch (\Exception $e) {
            $updateLog->update([
                'status' => 'failed',
                'message' => 'Gagal mengunduh update: ' . $e->getMessage(),
                'error_log' => $e->getTraceAsString(),
                'completed_at' => now(),
            ]);
            return false;
        }
    }

    /**
     * Install update
     */
    public function installUpdate(Update $update, UpdateLog $updateLog, $userId = null): bool
    {
        try {
            $updateLog->update([
                'status' => 'installing',
            ]);

            $currentVersion = $this->getCurrentVersion();
            $zipPath = storage_path('app/updates/update_' . $update->version . '.zip');

            if (!File::exists($zipPath)) {
                throw new \Exception('File update tidak ditemukan');
            }

            // 1. Backup database
            $this->backupDatabase($updateLog);

            // 2. Extract file update
            $extractPath = storage_path('app/updates/extract_' . $update->version);
            if (File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
            File::makeDirectory($extractPath, 0755, true);

            $zip = new Zip();
            if ($zip->open($zipPath) === true) {
                $zip->extractTo($extractPath);
                $zip->close();
            } else {
                throw new \Exception('Gagal mengekstrak file update');
            }

            // 3. Copy files ke aplikasi
            $this->copyUpdateFiles($extractPath, $updateLog);

            // 4. Run migrations
            if ($update->migrations && count($update->migrations) > 0) {
                $this->runMigrations($update->migrations, $updateLog);
            } else {
                // Run semua pending migrations
                Artisan::call('migrate', ['--force' => true]);
            }

            // 5. Run seeders jika ada
            if ($update->seeders && count($update->seeders) > 0) {
                $this->runSeeders($update->seeders, $updateLog);
            }

            // 6. Update version
            $this->setCurrentVersion($update->version);

            // 7. Clear cache
            Artisan::call('optimize:clear');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            // 8. Cleanup
            File::delete($zipPath);
            File::deleteDirectory($extractPath);

            $updateLog->update([
                'status' => 'success',
                'previous_version' => $currentVersion,
                'message' => 'Update berhasil diinstall',
                'completed_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            $updateLog->update([
                'status' => 'failed',
                'message' => 'Gagal menginstall update: ' . $e->getMessage(),
                'error_log' => $e->getTraceAsString(),
                'completed_at' => now(),
            ]);

            // Rollback jika ada backup
            $this->rollbackUpdate($updateLog);

            return false;
        }
    }

    /**
     * Backup database
     */
    protected function backupDatabase(UpdateLog $updateLog): void
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $backupFile = $backupDir . '/backup_' . date('Y-m-d_His') . '_' . $updateLog->version . '.sql';
            
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');

            $command = "mysqldump -h {$dbHost} -u {$dbUser} -p{$dbPass} {$dbName} > {$backupFile}";
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                Log::warning('Database backup mungkin gagal, lanjutkan update');
            }
        } catch (\Exception $e) {
            Log::warning('Gagal backup database: ' . $e->getMessage());
        }
    }

    /**
     * Copy update files to application
     */
    protected function copyUpdateFiles(string $extractPath, UpdateLog $updateLog): void
    {
        $sourceDirs = [
            'app' => base_path('app'),
            'database' => base_path('database'),
            'resources' => base_path('resources'),
            'routes' => base_path('routes'),
            'public' => base_path('public'),
            'config' => base_path('config'),
        ];

        foreach ($sourceDirs as $dir => $targetPath) {
            $sourcePath = $extractPath . '/' . $dir;
            if (File::exists($sourcePath)) {
                File::copyDirectory($sourcePath, $targetPath);
            }
        }

        // Copy file individual jika ada
        $filesToCopy = [
            'composer.json',
            'package.json',
            '.env.example',
        ];

        foreach ($filesToCopy as $file) {
            $sourceFile = $extractPath . '/' . $file;
            if (File::exists($sourceFile)) {
                File::copy($sourceFile, base_path($file));
            }
        }
    }

    /**
     * Run migrations
     */
    protected function runMigrations(array $migrations, UpdateLog $updateLog): void
    {
        foreach ($migrations as $migration) {
            try {
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/' . $migration,
                    '--force' => true,
                ]);
            } catch (\Exception $e) {
                Log::error('Migration failed: ' . $migration . ' - ' . $e->getMessage());
            }
        }
    }

    /**
     * Run seeders
     */
    protected function runSeeders(array $seeders, UpdateLog $updateLog): void
    {
        foreach ($seeders as $seeder) {
            try {
                Artisan::call('db:seed', [
                    '--class' => $seeder,
                    '--force' => true,
                ]);
            } catch (\Exception $e) {
                Log::error('Seeder failed: ' . $seeder . ' - ' . $e->getMessage());
            }
        }
    }

    /**
     * Rollback update
     */
    protected function rollbackUpdate(UpdateLog $updateLog): void
    {
        // Implementasi rollback jika diperlukan
        // Bisa restore dari backup database
        Log::info('Rollback update untuk versi: ' . $updateLog->version);
    }
}


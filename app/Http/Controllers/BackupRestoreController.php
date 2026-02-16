<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class BackupRestoreController extends Controller
{
    /**
     * Path to store backup files
     */
    private function backupPath(): string
    {
        $path = storage_path('app/backups');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        return $path;
    }

    /**
     * Path to storage/app/public (files to backup)
     */
    private function storagePath(): string
    {
        return storage_path('app/public');
    }

    /**
     * Find MySQL binary (mysqldump or mysql) from Laragon or system PATH
     */
    private function findMysqlBinary(string $binary): string
    {
        // Check Laragon's MySQL bin directory first
        $laragonMysqlDir = 'D:/laragon/bin/mysql';
        if (is_dir($laragonMysqlDir)) {
            $dirs = glob($laragonMysqlDir . '/mysql-*', GLOB_ONLYDIR);
            if (!empty($dirs)) {
                // Use the latest version found
                rsort($dirs);
                $binPath = $dirs[0] . '/bin/' . $binary . '.exe';
                if (file_exists($binPath)) {
                    return '"' . $binPath . '"';
                }
            }
        }

        // Fallback: try system PATH
        return $binary;
    }

    /**
     * Display listing of backups.
     */
    public function index()
    {
        $backups = [];
        $backupPath = $this->backupPath();

        if (File::exists($backupPath)) {
            $files = File::files($backupPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'zip') {
                    $backups[] = [
                        'filename' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'size_raw' => $file->getSize(),
                        'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    ];
                }
            }
            // Sort by date descending (newest first)
            usort($backups, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }

        return view('utilities.backuprestore.index', compact('backups'));
    }

    /**
     * Create a new backup (SQL + files) and auto-download.
     */
    public function create()
    {
        try {
            set_time_limit(600); // 10 minutes max

            $timestamp = date('Y-m-d_H-i-s');
            $zipFilename = "backup_{$timestamp}.zip";
            $backupPath = $this->backupPath();
            $zipPath = $backupPath . DIRECTORY_SEPARATOR . $zipFilename;
            $tempSqlPath = $backupPath . DIRECTORY_SEPARATOR . "database_{$timestamp}.sql";

            // Step 1: MySQL dump
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');

            $mysqldump = $this->findMysqlBinary('mysqldump');

            // Build command with all connection args
            $connArgs = sprintf('--host=%s --port=%s --user=%s',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser)
            );
            if (!empty($dbPass)) {
                $connArgs .= ' --password=' . escapeshellarg($dbPass);
            }

            $tempErrPath = $backupPath . DIRECTORY_SEPARATOR . "dump_error_{$timestamp}.log";

            $dumpCommand = sprintf(
                '%s %s --routines --triggers --single-transaction %s > "%s" 2> "%s"',
                $mysqldump,
                $connArgs,
                escapeshellarg($dbName),
                str_replace('/', DIRECTORY_SEPARATOR, $tempSqlPath),
                str_replace('/', DIRECTORY_SEPARATOR, $tempErrPath)
            );

            exec($dumpCommand, $output, $returnCode);

            $errorMsg = file_exists($tempErrPath) ? trim(file_get_contents($tempErrPath)) : '';
            @unlink($tempErrPath);

            if ($returnCode !== 0 || !file_exists($tempSqlPath) || filesize($tempSqlPath) === 0) {
                Log::error('mysqldump failed: ' . $errorMsg);
                @unlink($tempSqlPath);
                return response()->json([
                    'error' => 'Gagal membuat database dump: ' . $errorMsg
                ], 500);
            }

            // Step 2: Create ZIP (SQL + storage files)
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                @unlink($tempSqlPath);
                return response()->json(['error' => 'Gagal membuat file ZIP'], 500);
            }

            // Add SQL file
            $zip->addFile($tempSqlPath, 'database.sql');

            // Add storage/app/public files (exclude temp/)
            $storagePath = $this->storagePath();
            if (File::exists($storagePath)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($storagePath, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $item) {
                    $relativePath = str_replace($storagePath . DIRECTORY_SEPARATOR, '', $item->getPathname());
                    $relativePath = str_replace('\\', '/', $relativePath);

                    // Exclude temp/ folder
                    if (str_starts_with($relativePath, 'temp/') || $relativePath === 'temp') {
                        continue;
                    }

                    if ($item->isDir()) {
                        $zip->addEmptyDir('storage/' . $relativePath);
                    } else {
                        $zip->addFile($item->getPathname(), 'storage/' . $relativePath);
                    }
                }
            }

            $zip->close();

            // Remove temp SQL file
            @unlink($tempSqlPath);

            // Return JSON with filename (JS will redirect to download)
            return response()->json([
                'success' => 'Backup berhasil dibuat.',
                'filename' => $zipFilename,
            ]);

        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat membuat backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download an existing backup file.
     */
    public function download($filename)
    {
        // Sanitize filename to prevent directory traversal
        $filename = basename($filename);
        $filePath = $this->backupPath() . DIRECTORY_SEPARATOR . $filename;

        if (!File::exists($filePath)) {
            return redirect()->route('backuprestore.index')
                ->with('error', 'File backup tidak ditemukan.');
        }

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /**
     * Restore from an existing backup or uploaded file.
     */
    public function restore(Request $request)
    {
        try {
            set_time_limit(600);

            $zipPath = null;
            $isUpload = false;

            // Determine source: upload or existing file
            if ($request->hasFile('backup_file')) {
                $request->validate([
                    'backup_file' => 'required|file|mimes:zip|max:512000', // 500MB max
                ]);

                $uploadedFile = $request->file('backup_file');
                $zipPath = $this->backupPath() . DIRECTORY_SEPARATOR . 'upload_' . time() . '.zip';
                $uploadedFile->move($this->backupPath(), basename($zipPath));
                $isUpload = true;

            } elseif ($request->filled('backup_filename')) {
                $filename = basename($request->backup_filename);
                $zipPath = $this->backupPath() . DIRECTORY_SEPARATOR . $filename;

                if (!File::exists($zipPath)) {
                    return response()->json(['error' => 'File backup tidak ditemukan.'], 404);
                }
            } else {
                return response()->json(['error' => 'Pilih file backup atau upload file ZIP.'], 400);
            }

            // Validate ZIP contents
            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                if ($isUpload) @unlink($zipPath);
                return response()->json(['error' => 'File ZIP tidak valid.'], 400);
            }

            // Check if database.sql exists in ZIP
            if ($zip->locateName('database.sql') === false) {
                $zip->close();
                if ($isUpload) @unlink($zipPath);
                return response()->json([
                    'error' => 'File ZIP tidak valid. Tidak ditemukan database.sql di dalam arsip.'
                ], 400);
            }

            // Extract to temp directory
            $tempDir = $this->backupPath() . DIRECTORY_SEPARATOR . 'restore_temp_' . time();
            File::makeDirectory($tempDir, 0755, true);
            $zip->extractTo($tempDir);
            $zip->close();

            // Step 1: Restore database
            $sqlPath = $tempDir . DIRECTORY_SEPARATOR . 'database.sql';
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');

            $mysqlBin = $this->findMysqlBinary('mysql');

            // Build command with all connection args
            $connArgs = sprintf('--host=%s --port=%s --user=%s',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser)
            );
            if (!empty($dbPass)) {
                $connArgs .= ' --password=' . escapeshellarg($dbPass);
            }

            $restoreCommand = sprintf(
                '%s %s %s < "%s" 2>&1',
                $mysqlBin,
                $connArgs,
                escapeshellarg($dbName),
                $sqlPath
            );

            exec($restoreCommand, $restoreOutput, $restoreReturnCode);

            if ($restoreReturnCode !== 0) {
                $errorMsg = implode("\n", $restoreOutput);
                File::deleteDirectory($tempDir);
                if ($isUpload) @unlink($zipPath);
                Log::error('mysql restore failed: ' . $errorMsg);
                return response()->json([
                    'error' => 'Gagal restore database: ' . $errorMsg
                ], 500);
            }

            // Step 2: Restore storage files
            $tempStoragePath = $tempDir . DIRECTORY_SEPARATOR . 'storage';
            if (File::exists($tempStoragePath)) {
                $storagePath = $this->storagePath();

                // Delete existing storage contents (except temp/)
                if (File::exists($storagePath)) {
                    $dirs = File::directories($storagePath);
                    foreach ($dirs as $dir) {
                        if (basename($dir) !== 'temp') {
                            File::deleteDirectory($dir);
                        }
                    }
                    // Delete files at root level
                    $files = File::files($storagePath);
                    foreach ($files as $file) {
                        File::delete($file);
                    }
                }

                // Copy restored files
                $this->copyDirectory($tempStoragePath, $storagePath);
            }

            // Cleanup
            File::deleteDirectory($tempDir);
            if ($isUpload) @unlink($zipPath);

            return response()->json([
                'success' => 'Backup berhasil di-restore. Database dan file telah dikembalikan.'
            ]);

        } catch (\Exception $e) {
            Log::error('Restore failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat restore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a backup file.
     */
    public function destroy($filename)
    {
        $filename = basename($filename);
        $filePath = $this->backupPath() . DIRECTORY_SEPARATOR . $filename;

        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File backup tidak ditemukan.'], 404);
        }

        File::delete($filePath);

        return response()->json([
            'success' => "File backup {$filename} berhasil dihapus."
        ]);
    }

    /**
     * Recursively copy a directory.
     */
    private function copyDirectory(string $source, string $destination): void
    {
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $destPath = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            if ($item->isDir()) {
                if (!File::exists($destPath)) {
                    File::makeDirectory($destPath, 0755, true);
                }
            } else {
                File::copy($item->getPathname(), $destPath);
            }
        }
    }

    /**
     * Format bytes to human-readable size.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;

class DummyDataCleaner extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("Cleaning ALL Dummy Data...");

        // 1. Delete Child Tables explicitly (to be safe even if Cascade is missing)

        // Lembur
        DB::table('lembur')->truncate();
        $this->command->info("- Truncated 'lembur'");

        // Aktivitas
        DB::table('aktivitas_karyawan')->truncate();
        $this->command->info("- Truncated 'aktivitas_karyawan'");

        // Kunjungan
        DB::table('kunjungan')->truncate();
        $this->command->info("- Truncated 'kunjungan'");

        // Transactions (Presensi, Izin)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('presensi')->truncate();
        $this->command->info("- Truncated 'presensi'");

        DB::table('presensi_izinsakit')->truncate();
        DB::table('presensi_izincuti')->truncate();
        DB::table('presensi_izinabsen')->truncate();
        DB::table('presensi_izindinas')->truncate();
        $this->command->info("- Truncated all Izin tables");

        // Payroll Data
        DB::table('karyawan_gaji_pokok')->truncate();
        $this->command->info("- Truncated 'karyawan_gaji_pokok'");

        // Explicitly delete details to be safe
        // Logic: Delete details where header is about to be deleted? 
        // Or better: Delete where nik is in the header table with that NIK.
        // But simpler: just rely on the main tables or use join delete if supported. 
        // Since we don't carry NIK in detail, we skip detailed specific delete unless we query first.
        // Actually, let's trust cascade for details OR query IDs.
        // Given complexity, let's stick to adding 'izindinas' which matches the NIK pattern directly.

        DB::table('karyawan_tunjangan')->truncate();
        DB::table('karyawan_bpjstenagakerja')->truncate();
        $this->command->info("- Truncated payroll details");

        // 1.5. Delete Users & UserKaryawan
        $userIds = DB::table('users_karyawan')->pluck('id_user')->toArray();
        if (!empty($userIds)) {
            DB::table('users')->whereIn('id', $userIds)->delete();
            $this->command->info("- Deleted " . count($userIds) . " records from 'users'");
        }
        DB::table('users_karyawan')->truncate();


        // 2. Delete Karyawan (Parent)
        DB::table('karyawan')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info("- Truncated 'karyawan'");

        $this->command->info("Cleanup Complete! Database is clean from dummy data.");
    }
}

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
        $prefix = '2401%';
        $this->command->info("Cleaning Dummy Data (NIK: $prefix)...");

        // 1. Delete Child Tables explicitly (to be safe even if Cascade is missing)

        // Lembur (Confirmed NO Foreign Key in migration)
        $deletedLembur = DB::table('lembur')->where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedLembur records from 'lembur'");

        // Aktivitas
        $deletedAkt = DB::table('aktivitas_karyawan')->where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedAkt records from 'aktivitas_karyawan'");

        // Kunjungan
        $deletedKunj = DB::table('kunjungan')->where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedKunj records from 'kunjungan'");

        // Transactions (Presensi, Izin)
        // Note: Presensi has FK cascade usually, but manual delete is safer for clean output
        $deletedPresensi = DB::table('presensi')->where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedPresensi records from 'presensi'");

        $deletedSakit = DB::table('presensi_izinsakit')->where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedSakit records from 'presensi_izinsakit'");

        $deletedCuti = DB::table('presensi_izincuti')->where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedCuti records from 'presensi_izincuti'");

        $deletedIzin = DB::table('presensi_izinabsen')->where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedIzin records from 'presensi_izinabsen'");

        // Payroll Data (Cascades usually handle this, but explicit delete helps verification)
        $deletedGaji = DB::table('karyawan_gaji_pokok')->where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedGaji records from 'karyawan_gaji_pokok'");

        $deletedTunj = DB::table('karyawan_tunjangan')->where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedTunj records from 'karyawan_tunjangan'");
        // Detail tunjangan deletes via cascade of header usually, but good to know.

        $deletedBpjs = DB::table('karyawan_bpjstenagakerja')->where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedBpjs records from 'karyawan_bpjstenagakerja'");


        // 2. Delete Karyawan (Parent)
        $deletedKaryawan = Karyawan::where('nik', 'like', $prefix)->delete();
        $this->command->info("- Deleted $deletedKaryawan records from 'karyawan'");

        $this->command->info("Cleanup Complete! Database is clean from dummy data.");
    }
}

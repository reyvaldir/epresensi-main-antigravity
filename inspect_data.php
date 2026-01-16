<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "--- MASTER DATA ---\n";
    $depts = DB::table('departemen')->pluck('kode_dept')->toArray();
    echo "Departemen (" . count($depts) . "): " . implode(', ', $depts) . "\n";

    $cabangs = DB::table('cabang')->pluck('kode_cabang')->toArray();
    echo "Cabang (" . count($cabangs) . "): " . implode(', ', $cabangs) . "\n";

    $jabatans = DB::table('jabatan')->pluck('kode_jabatan')->toArray();
    echo "Jabatan (" . count($jabatans) . "): " . implode(', ', $jabatans) . "\n";

    $jams = DB::table('jam_kerja')->pluck('kode_jam_kerja')->toArray();
    echo "Jam Kerja (" . count($jams) . "): " . implode(', ', $jams) . "\n";

    $cuti = DB::table('master_cuti')->pluck('kode_cuti')->toArray();
    echo "Master Cuti (" . count($cuti) . "): " . implode(', ', $cuti) . "\n";

    echo "\n--- EXISTING TRANSACTIONS ---\n";
    echo "Karyawan Count: " . DB::table('karyawan')->count() . "\n";
    echo "Presensi Records: " . DB::table('presensi')->count() . "\n";
    echo "Izin Absen: " . DB::table('pengajuan_izin')->where('status', 'i')->count() . "\n"; // Checking general table if exists or specific
    // Based on migrations, separate tables exist:
    echo "Izin Absen (Table): " . (Schema::hasTable('izinabsen') ? DB::table('izinabsen')->count() : 'Table not found') . "\n";
    echo "Izin Sakit (Table): " . (Schema::hasTable('izinsakit') ? DB::table('izinsakit')->count() : 'Table not found') . "\n";
    echo "Izin Cuti (Table): " . (Schema::hasTable('izincuti') ? DB::table('izincuti')->count() : 'Table not found') . "\n";
    echo "Lembur Recs: " . (Schema::hasTable('lembur') ? DB::table('lembur')->count() : 'Table not found') . "\n";

    echo "\n--- PAYROLL COMPONENTS ---\n";
    echo "Gaji Pokok Configured: " . (Schema::hasTable('gaji_pokok') ? DB::table('gaji_pokok')->count() : 'Table not found') . "\n";
    echo "Tunjangan Configured: " . (Schema::hasTable('tunjangan') ? DB::table('tunjangan')->count() : 'Table not found') . "\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DummyIzinLemburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Get Dummy Employees
        $karyawans = Karyawan::where('nik', 'like', '2401%')->get();
        if ($karyawans->isEmpty()) {
            $this->command->error('No dummy employees found! Run DummyKaryawanSeeder first.');
            return;
        }

        $this->command->info('Generating Leave & Overtime Requests...');

        foreach ($karyawans as $karyawan) {

            // 1. Generate IZIN ABSEN (Leave)
            // Create 1-2 Approved Izin in the past
            for ($i = 0; $i < rand(0, 2); $i++) {
                $tgl = Carbon::now()->subDays(rand(5, 30));
                $kode = 'IZ' . $tgl->format('mY') . rand(100, 999);

                // Cek unique
                if (DB::table('presensi_izinabsen')->where('kode_izin', $kode)->exists())
                    continue;

                DB::table('presensi_izinabsen')->insert([
                    'kode_izin' => $kode,
                    'tanggal' => $tgl->toDateString(),
                    'dari' => $tgl->toDateString(),
                    'sampai' => $tgl->addDays(rand(0, 1))->toDateString(),
                    'nik' => $karyawan->nik,
                    'keterangan' => $faker->sentence(3),
                    'keterangan_hrd' => 'Disetujui untuk Demo',
                    'status' => 'a', // Approved
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Create 1 Pending Izin (Future)
            $tgl = Carbon::now()->addDays(rand(1, 10));
            $kode = 'IZ' . $tgl->format('mY') . rand(100, 999);

            if (!DB::table('presensi_izinabsen')->where('kode_izin', $kode)->exists()) {
                DB::table('presensi_izinabsen')->insert([
                    'kode_izin' => $kode,
                    'tanggal' => now()->toDateString(), // Pengajuan hari ini
                    'dari' => $tgl->toDateString(),
                    'sampai' => $tgl->toDateString(),
                    'nik' => $karyawan->nik,
                    'keterangan' => 'Izin keperluan keluarga (Demo Pending)',
                    'status' => 'p', // Pending
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }


            // 2. Generate LEMBUR (Overtime)
            // Create 2-3 Approved Lembur in the past (linked to Attendance)
            for ($j = 0; $j < rand(1, 3); $j++) {
                $tgl = Carbon::now()->subDays(rand(1, 25));
                if ($tgl->isSunday())
                    continue;

                // Lembur 18:00 - 20:00
                $lemburMulai = $tgl->format('Y-m-d') . ' 18:00:00';
                $lemburSelesai = $tgl->format('Y-m-d') . ' 20:00:00';

                DB::table('lembur')->insert([
                    'tanggal' => $tgl->toDateString(),
                    'nik' => $karyawan->nik,
                    'lembur_mulai' => $lemburMulai,
                    'lembur_selesai' => $lemburSelesai,
                    'lembur_in' => $lemburMulai, // Simulated Clock In
                    'lembur_out' => $lemburSelesai, // Simulated Clock Out
                    'foto_lembur_in' => 'dummy_lembur_in.jpg',
                    'foto_lembur_out' => null,
                    'lokasi_lembur_in' => '-6.200000,106.816666',
                    'lokasi_lembur_out' => null,
                    'status' => 'a', // Approved (or whatever status code '1' / 'a')
                    // Checking migration: char('status', 1). Convention: a=approved, p=pending.
                    'keterangan' => 'Lembur Project Demo',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $this->command->info('Dummy Izin & Lembur Generated!');
    }
}

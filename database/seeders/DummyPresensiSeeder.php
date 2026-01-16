<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DummyPresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Get Dummy Employees (those created by DummyKaryawanSeeder start with 2401)
        // Or just all employees for simplicity
        $karyawans = Karyawan::where('nik', 'like', '2401%')->get();

        if ($karyawans->count() == 0) {
            $this->command->error('No dummy employees found! Please run DummyKaryawanSeeder first.');
            return;
        }

        $startDate = Carbon::now()->subDays(30);
        // Fetch valid Shift Codes
        $shiftCodes = DB::table('jam_kerja')->pluck('kode_jam_kerja')->toArray();
        $defaultShift = !empty($shiftCodes) ? reset($shiftCodes) : 'JK01'; // Fallback JK01 if empty

        if (empty($shiftCodes)) {
            $this->command->error("Data Jam Kerja Kosong! Menggunakan fallback 'JK01'.");
        }

        $endDate = Carbon::now();

        $this->command->info('Generating Attendance Data from ' . $startDate->toDateString() . ' to ' . $endDate->toDateString());

        foreach ($karyawans as $karyawan) {
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                // Skip Sunday
                if ($currentDate->isSunday()) {
                    $currentDate->addDay();
                    continue;
                }

                $jamMasukStandar = '08:00:00'; // Default logic

                // Random Attendance Status
                $rand = rand(1, 100);

                if ($rand <= 85) {
                    // HADIR - Randomize Times
                    $tgl = $currentDate->format('Y-m-d');

                    if (rand(1, 100) <= 80) { // On Time
                        $jamIn = $tgl . ' ' . rand(7, 7) . ':' . str_pad(rand(30, 55), 2, '0', STR_PAD_LEFT) . ':00';
                    } else { // Late
                        $jamIn = $tgl . ' ' . rand(8, 8) . ':' . str_pad(rand(5, 30), 2, '0', STR_PAD_LEFT) . ':00';
                    }

                    if (rand(1, 100) <= 80) { // Regular Out
                        $jamOut = $tgl . ' ' . rand(17, 17) . ':' . str_pad(rand(1, 30), 2, '0', STR_PAD_LEFT) . ':00';
                    } else { // Overtime/Late Out
                        $jamOut = $tgl . ' ' . rand(18, 19) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    }

                    $lat = -6.175392 + ($faker->randomFloat(6, -0.001, 0.001));
                    $long = 106.827153 + ($faker->randomFloat(6, -0.001, 0.001));
                    $lokasi = $lat . ',' . $long;

                    // Assign random shift from available ones
                    $shift = !empty($shiftCodes) ? $faker->randomElement($shiftCodes) : $defaultShift;

                    Presensi::create([
                        'nik' => $karyawan->nik,
                        'tanggal' => $tgl,
                        'jam_in' => $jamIn,
                        'jam_out' => $jamOut,
                        'foto_in' => 'dummy_in.jpg', // Placeholder image
                        'foto_out' => 'dummy_out.jpg',
                        'lokasi_in' => $lokasi,
                        'lokasi_out' => $lokasi,
                        'kode_jam_kerja' => $shift,
                        'status' => 'h'
                    ]);

                } elseif ($rand <= 90) {
                    // SAKIT (s)
                    // Biasanya masuk ke tabel pengajuan izin sakit, lalu presensi di-set 's'
                    // Untuk simplifikasi, kita insert presensi 's' saja jika tabel presensi support
                    // Tapi di struktur awal, presensi table store 'h'.
                    // S/I/A biasanya derived from izin table OR inserted as special row.
                    // Let's assume standard 'h' for strict presensi table, others are empty row in presensi but exist in izin.
                    // BUT, some systems insert 's' into presensi table directly.
                    // Checking migration: $table->char('status', 1); --> likely h,i,s,a

                    Presensi::create([
                        'nik' => $karyawan->nik,
                        'tanggal' => $currentDate->format('Y-m-d'),
                        'jam_in' => $currentDate->format('Y-m-d') . ' 08:00:00', // Dummy time for simplicity
                        'jam_out' => null,
                        'foto_in' => null,
                        'foto_out' => null,
                        'lokasi_in' => null,
                        'lokasi_out' => null,
                        'kode_jam_kerja' => 'JK01',
                        'status' => 's'
                    ]);
                } elseif ($rand <= 95) {
                    // IZIN (i)
                    Presensi::create([
                        'nik' => $karyawan->nik,
                        'tanggal' => $currentDate->format('Y-m-d'),
                        'jam_in' => $currentDate->format('Y-m-d') . ' 08:00:00',
                        'jam_out' => null,
                        'foto_in' => null,
                        'foto_out' => null,
                        'lokasi_in' => null,
                        'lokasi_out' => null,
                        'kode_jam_kerja' => 'JK01',
                        'status' => 'i'
                    ]);
                }
                // Alpha (A) = No Record (Empty)

                $currentDate->addDay();
            }
        }

        $this->command->info('Attendance Data Generated!');
    }
}

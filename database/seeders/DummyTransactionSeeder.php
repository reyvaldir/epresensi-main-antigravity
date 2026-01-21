<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use App\Models\Presensi;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DummyTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Get Dummy Employees
        $karyawans = Karyawan::where('nik', 'like', '2401%')->get();
        if ($karyawans->isEmpty()) {
            $this->command->error('No dummy employees found! Run DummyKaryawanSeeder first.');
            return;
        }

        // 2. Fetch Shift Data
        $shiftCodes = DB::table('presensi_jamkerja')->pluck('kode_jam_kerja')->toArray();
        $defaultShift = !empty($shiftCodes) ? reset($shiftCodes) : 'JK01';

        // 3. Define Period (Thesis: Nov 2025 - Jan 2026)
        $startDate = Carbon::create(2025, 11, 1);
        $endDate = Carbon::create(2026, 1, 31);

        $this->command->info("Generating Unified Transactions from {$startDate->toDateString()} to {$endDate->toDateString()}...");

        foreach ($karyawans as $karyawan) {
            $currentDate = $startDate->copy();

            // Check Department for Location Logic
            $isFieldWorker = in_array($karyawan->kode_dept, ['TKL', 'OTK']); // Teknisi Lapangan / Operasional Toko

            while ($currentDate <= $endDate) {
                // Variables for this day
                $tgl = $currentDate->format('Y-m-d');
                $isWeekend = $currentDate->isSunday();

                // --- DECISION TREE ---
                // 1. Weekend Logic
                if ($isWeekend) {
                    // 5% chance of Overtime even on Sunday
                    if (rand(1, 100) <= 5) {
                        $this->createLembur($karyawan->nik, $tgl, true, $isFieldWorker);
                    }
                    $currentDate->addDay();
                    continue;
                }

                // 2. Weekday Status - 92% Attendance Rate
                $rand = rand(1, 100);

                if ($rand <= 92) {
                    // HADIR (92%)
                    $this->createHadir($karyawan, $tgl, $shiftCodes, $defaultShift, $faker, $isFieldWorker);
                } else {
                    // TIDAK HADIR (8%)
                    $typeRand = rand(1, 5);

                    if ($typeRand == 1) {
                        $this->createIzinSakit($karyawan->nik, $tgl);
                    } elseif ($typeRand == 2) {
                        $this->createCuti($karyawan->nik, $tgl);
                    } elseif ($typeRand == 3) {
                        $this->createIzinAbsen($karyawan->nik, $tgl);
                    } elseif ($typeRand == 4) {
                        $this->createIzinDinas($karyawan->nik, $tgl);
                    }
                }

                $currentDate->addDay();
            }
        }
        $this->command->info('Unified Dummy Transactions Generated!');
    }

    private function getStatus($tgl)
    {
        // Permission Status Logic:
        // Nov-Dec 2025: Processed (85% Approved '1', 15% Rejected '2')
        // Jan 2026: Pending '0'
        $date = Carbon::parse($tgl);
        if ($date->year == 2026)
            return '0';
        return rand(1, 100) <= 85 ? '1' : '2';
    }

    private function getLemburStatus($tgl)
    {
        // Lembur Status Logic:
        // Nov-Dec 2025: Approved '1'
        // Jan 2026: Pending '0'
        $date = Carbon::parse($tgl);
        if ($date->year == 2026)
            return '0';
        return '1';
    }

    private function getLocation($isFieldWorker)
    {
        if ($isFieldWorker) {
            // Balikpapan Spread Logic
            // Base: -1.265386, 116.831200
            // Jitter: +/- 0.05 to 0.08
            $latBase = -1.265386;
            $lonBase = 116.831200;

            $lat = $latBase + (rand(-8000, 8000) / 100000); // +/- 0.08
            $lon = $lonBase + (rand(-8000, 8000) / 100000);
            return number_format($lat, 6) . ',' . number_format($lon, 6);
        }
        // Default Office
        return '-6.175392,106.827153';
    }

    private function createHadir($karyawan, $tgl, $shiftCodes, $defaultShift, $faker, $isFieldWorker)
    {
        $shift = !empty($shiftCodes) ? $faker->randomElement($shiftCodes) : $defaultShift;

        // Time Logic
        $isLate = rand(1, 100) <= 20; // 20% Late
        // Overtime check (Weekday) - 5% chance
        $isOvertime = rand(1, 100) <= 5;

        if ($isLate) {
            $jamIn = $tgl . ' ' . rand(8, 8) . ':' . str_pad(rand(5, 30), 2, '0', STR_PAD_LEFT) . ':00';
        } else {
            $jamIn = $tgl . ' ' . rand(7, 7) . ':' . str_pad(rand(30, 55), 2, '0', STR_PAD_LEFT) . ':00';
        }

        // Jam Out Logic
        $jamOut = $tgl . ' ' . rand(17, 17) . ':' . str_pad(rand(0, 45), 2, '0', STR_PAD_LEFT) . ':00';

        // Lokasi Logic
        $lokasi = $this->getLocation($isFieldWorker);

        DB::table('presensi')->insert([
            'nik' => $karyawan->nik,
            'tanggal' => $tgl,
            'jam_in' => $jamIn,
            'jam_out' => $isOvertime ? null : $jamOut, // If overtime, jam_out might be overwritten or updated? 
            // Usually presensi table stores the actual scan out. If overtime, scan out is later.
            // Let's make scan out later if overtime.
            'jam_out' => $isOvertime ? ($tgl . ' ' . rand(19, 21) . ':00:00') : $jamOut,
            'foto_in' => 'dummy_in.jpg',
            'foto_out' => 'dummy_out.jpg',
            'lokasi_in' => $lokasi,
            'lokasi_out' => $lokasi,
            'kode_jam_kerja' => $shift,
            'status' => 'h',
            'istirahat_in' => $tgl . ' 12:00:00',
            'istirahat_out' => $tgl . ' 13:00:00',
            'foto_istirahat_in' => 'dummy_break_in.jpg',
            'foto_istirahat_out' => 'dummy_break_out.jpg',
            'lokasi_istirahat_in' => $lokasi,
            'lokasi_istirahat_out' => $lokasi,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Activities & Visits (Only if Field Worker)
        if ($isFieldWorker) {
            // Activity (80% chance)
            if (rand(1, 100) <= 80) {
                DB::table('aktivitas_karyawan')->insert([
                    'nik' => $karyawan->nik,
                    'aktivitas' => $faker->randomElement(['Kunjungan Outlet', 'Maintenance Alat', 'Kanvasing Area', 'Meeting Client']),
                    'foto' => 'dummy_activity.jpg',
                    'lokasi' => $this->getLocation(true), // Always randomized field location
                    'created_at' => $jamIn,
                    'updated_at' => $jamIn
                ]);
            }
            // Visit (20% chance)
            if (rand(1, 100) <= 20) {
                DB::table('kunjungan')->insert([
                    'nik' => $karyawan->nik,
                    'deskripsi' => 'Visit Customer ' . $faker->company,
                    'foto' => 'dummy_visit.jpg',
                    'lokasi' => $this->getLocation(true),
                    'tanggal_kunjungan' => $tgl,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } else {
            // Office Worker Activity
            if (rand(1, 100) <= 60) {
                DB::table('aktivitas_karyawan')->insert([
                    'nik' => $karyawan->nik,
                    'aktivitas' => $faker->randomElement(['Meeting Internal', 'Rekap Laporan', 'Coding', 'Admin Work']),
                    'foto' => 'dummy_activity.jpg',
                    'lokasi' => $this->getLocation(false), // Office
                    'created_at' => $jamIn,
                    'updated_at' => $jamIn
                ]);
            }
        }

        // Lembur (Weekdays)
        if ($isOvertime) {
            $this->createLembur($karyawan->nik, $tgl, false, $isFieldWorker);
        }
    }

    private function createLembur($nik, $tgl, $isWeekend, $isFieldWorker)
    {
        $status = $this->getLemburStatus($tgl);
        $lokasi = $this->getLocation($isFieldWorker);

        if ($isWeekend) {
            $start = $tgl . ' 09:00:00';
            $end = $tgl . ' 14:00:00';
        } else {
            $start = $tgl . ' 17:00:00';
            $end = $tgl . ' 20:00:00';
        }

        DB::table('lembur')->insert([
            'tanggal' => $tgl,
            'nik' => $nik,
            'lembur_mulai' => $start,
            'lembur_selesai' => $end,
            'lembur_in' => $start,
            'lembur_out' => $end, // Assuming auto-checkout style or diligent user
            'foto_lembur_in' => 'dummy_lembur_in.jpg',
            'foto_lembur_out' => 'dummy_lembur_out.jpg',
            'lokasi_lembur_in' => $lokasi,
            'lokasi_lembur_out' => $lokasi,
            'status' => $status,
            'keterangan' => $isWeekend ? 'Lembur Weekend Support' : 'Lembur Project Deadline',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function createIzinSakit($nik, $tgl)
    {
        // ... (Keep existing logic, ensure no conflicts)
        // Re-implementing essentially the same but clean
        $kode = 'IZ' . date('ymd', strtotime($tgl)) . rand(100, 999);
        if ($this->checkConflict($nik, $tgl))
            return;
        $status = $this->getStatus($tgl);

        DB::table('presensi_izinsakit')->insert([
            'kode_izin_sakit' => $kode,
            'tanggal' => $tgl,
            'dari' => $tgl,
            'sampai' => $tgl,
            'nik' => $nik,
            'keterangan' => 'Sakit Demam',
            'status' => $status,
            'id_user' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($status == '1')
            $this->createPresensiStatus($nik, $tgl, 's');
    }

    private function createIzinAbsen($nik, $tgl)
    {
        $kode = 'IZ' . date('ymd', strtotime($tgl)) . rand(100, 999);
        if ($this->checkConflict($nik, $tgl))
            return;
        $status = $this->getStatus($tgl);

        DB::table('presensi_izinabsen')->insert([
            'kode_izin' => $kode,
            'tanggal' => $tgl,
            'dari' => $tgl,
            'sampai' => $tgl,
            'nik' => $nik,
            'keterangan' => 'Urusan Keluarga',
            'status' => $status,
            'keterangan_hrd' => ($status == '1') ? 'ACC' : null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($status == '1')
            $this->createPresensiStatus($nik, $tgl, 'i');
    }

    private function createIzinDinas($nik, $tgl)
    {
        $kode = 'DN' . date('ymd', strtotime($tgl)) . rand(100, 999);
        if ($this->checkConflict($nik, $tgl))
            return;
        $status = $this->getStatus($tgl);

        DB::table('presensi_izindinas')->insert([
            'kode_izin_dinas' => $kode,
            'tanggal' => $tgl,
            'dari' => $tgl,
            'sampai' => $tgl,
            'nik' => $nik,
            'keterangan' => 'Dinas Luar Kota',
            'status' => $status,
            'keterangan_hrd' => ($status == '1') ? 'ACC' : null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($status == '1')
            $this->createPresensiStatus($nik, $tgl, 'i');
    }

    private function createCuti($nik, $tgl)
    {
        $kode = 'CT' . date('ymd', strtotime($tgl)) . rand(100, 999);
        if ($this->checkConflict($nik, $tgl))
            return;
        $status = $this->getStatus($tgl);

        // Fetch kode_cuti safely
        $kodeCuti = DB::table('cuti')->value('kode_cuti') ?? 'C01';

        DB::table('presensi_izincuti')->insert([
            'kode_izin_cuti' => $kode,
            'tanggal' => $tgl,
            'dari' => $tgl,
            'sampai' => $tgl,
            'kode_cuti' => $kodeCuti,
            'nik' => $nik,
            'keterangan' => 'Cuti Tahunan',
            'status' => $status,
            'keterangan_hrd' => ($status == '1') ? 'ACC' : null,
            'id_user' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($status == '1')
            $this->createPresensiStatus($nik, $tgl, 'c');
    }

    private function createPresensiStatus($nik, $tgl, $status)
    {
        // Logic: Insert into presensi if approved ('1').
        // Note: The caller methods check status == '1' before calling this.
        // However, check if presensi exists?
        if (DB::table('presensi')->where('nik', $nik)->where('tanggal', $tgl)->exists())
            return;

        DB::table('presensi')->insert([
            'nik' => $nik,
            'tanggal' => $tgl,
            'jam_in' => null,
            'jam_out' => null,
            'foto_in' => null,
            'foto_out' => null,
            'lokasi_in' => null,
            'lokasi_out' => null,
            'kode_jam_kerja' => 'JK01',
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function checkConflict($nik, $tgl)
    {
        if (DB::table('presensi_izinabsen')->where('tanggal', $tgl)->where('nik', $nik)->exists())
            return true;
        if (DB::table('presensi_izinsakit')->where('tanggal', $tgl)->where('nik', $nik)->exists())
            return true;
        if (DB::table('presensi_izincuti')->where('tanggal', $tgl)->where('nik', $nik)->exists())
            return true;
        if (DB::table('presensi_izindinas')->where('tanggal', $tgl)->where('nik', $nik)->exists())
            return true;
        return false;
    }
}

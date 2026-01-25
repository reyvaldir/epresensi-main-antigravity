<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DummyTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Initial Config
        $faker = Faker::create('id_ID');

        // Master Data Cache
        $cabangs = DB::table('cabang')->get()->keyBy('kode_cabang');

        // Anchor Point for OPL Visits (Balikpapan Center)
        $anchorLat = -1.245053;
        $anchorLon = 116.857903;

        // 3. Define Period
        $startDate = Carbon::create(2025, 11, 1);
        $endDate = Carbon::create(2026, 1, 31);

        $this->command->info("Generating Unified Transactions (Nov 25 - Jan 26)...");

        $karyawans = Karyawan::where('nik', 'like', '2401%')->get();
        if ($karyawans->isEmpty()) {
            $this->command->error("No Dummy Employees Found!");
            return;
        }

        foreach ($karyawans as $karyawan) {
            $currentDate = $startDate->copy();
            $cabang = $cabangs[$karyawan->kode_cabang];

            // Parse Cabang Location
            $locParts = explode(',', $cabang->lokasi_cabang);
            $cabangLat = trim($locParts[0]);
            $cabangLon = trim($locParts[1]);

            while ($currentDate <= $endDate) {
                // Determine Status (Sunday is Holiday, Saturday is Workday)
                $isSunday = $currentDate->isSunday();
                $tgl = $currentDate->toDateString();

                // 92% Presence on Workdays (Mon-Sat), 0% on Sunday
                if (!$isSunday) {
                    $dice = rand(1, 100);
                    if ($dice <= 92) {
                        $status = 'h';
                    } else {
                        // 8% Absen (S/I/C/D)
                        $status = $faker->randomElement(['s', 'i', 'c', 'd']);

                        // Determine Approval Status
                        // Rule: Nov 1, 2025 - Jan 19, 2026 -> Processed (80% ACC, 20% TOLAK)
                        // Jan 20, 2026 onwards -> Pending
                        $cutoffDate = Carbon::create(2026, 1, 19);

                        if ($currentDate->lte($cutoffDate)) {
                            // 80% Approved ('1'), 20% Rejected ('2')
                            $approvalStatus = (rand(1, 100) <= 80) ? '1' : '2';
                        } else {
                            $approvalStatus = '0'; // Pending
                        }

                        $codeDate = $currentDate->format('ym');

                        // Common Fields
                        $baseData = [
                            'nik' => $karyawan->nik,
                            'tanggal' => $tgl,
                            'dari' => $tgl,
                            'sampai' => $tgl,
                            'keterangan' => 'Simulated Request ' . strtoupper($status),
                            'status' => $approvalStatus,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        if ($status == 's') { // Sakit
                            // PK: IS + YearMonth + 6 Digit Rand -> 12 Chars
                            $kode = 'IS' . $codeDate . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                            DB::table('presensi_izinsakit')->insert(array_merge($baseData, [
                                'kode_izin_sakit' => $kode,
                                'doc_sid' => null,
                                'id_user' => 1 // Default Admin or User ID
                            ]));
                        } elseif ($status == 'i') { // Izin
                            // PK: IA? No standard length in migration, but let's match patterns
                            // Migration: char(kode_izin) PK. 
                            $kode = 'IA' . $codeDate . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                            DB::table('presensi_izinabsen')->insert(array_merge($baseData, [
                                'kode_izin' => $kode
                            ]));
                        } elseif ($status == 'c') { // Cuti
                            $kode = 'IC' . $codeDate . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                            // Table: presensi_izincuti
                            DB::table('presensi_izincuti')->insert(array_merge($baseData, [
                                'kode_izin_cuti' => $kode,
                                'kode_cuti' => 'C01',
                                'id_user' => 1,
                                'keterangan_hrd' => null
                            ]));
                        } elseif ($status == 'd') { // Dinas
                            $kode = 'ID' . $codeDate . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                            DB::table('presensi_izindinas')->insert(array_merge($baseData, [
                                'kode_izin_dinas' => $kode
                            ]));
                        }
                    }
                } else {
                    $status = 'libur';
                }

                // Insert Presensi if Hadir
                if ($status == 'h') {
                    // Logic Location: < 70m from Cabang
                    $myLat = $cabangLat + ($faker->randomFloat(6, -0.0006, 0.0006));
                    $myLon = $cabangLon + ($faker->randomFloat(6, -0.0006, 0.0006));
                    $lokasiPresensi = "{$myLat},{$myLon}";

                    // Jam Masuk Logic (Start 08:00)
                    // 90% On Time (07:00 - 07:55)
                    // 10% Late (08:05 - 09:30)
                    if (rand(1, 100) <= 90) {
                        $jamIn = $tgl . ' 07:' . str_pad(rand(0, 55), 2, '0', STR_PAD_LEFT) . ':00';
                    } else {
                        // Late
                        $hour = rand(8, 9);
                        $minute = ($hour == 9) ? rand(0, 30) : rand(5, 59);
                        $jamIn = $tgl . ' ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ':00';
                    }

                    // Jam Pulang Logic (End 17:00)
                    // 85% On Time (17:01 - 18:00)
                    // 5% Early (16:00 - 16:59) - Pulang Cepat
                    // 10% Overtime (handled by $isOvertime check below, usually > 17:00 or null if pending)

                    $isOvertime = rand(1, 100) <= 10;
                    $isEarly = rand(1, 100) <= 5;

                    if ($isEarly && !$isOvertime) {
                        $jamOut = $tgl . ' 16:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    } else {
                        // Normal or Overtime Base
                        $jamOut = $tgl . ' ' . rand(17, 19) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    }

                    // Insert Presensi
                    DB::table('presensi')->insert([
                        'nik' => $karyawan->nik,
                        'tanggal' => $tgl,
                        'jam_in' => $jamIn,
                        'jam_out' => $isOvertime ? null : $jamOut,
                        'foto_in' => 'dummy_in.jpg',
                        'foto_out' => 'dummy_out.jpg',
                        'lokasi_in' => $lokasiPresensi,
                        'lokasi_out' => $lokasiPresensi,
                        'kode_jam_kerja' => $karyawan->kode_jadwal ?? 'JK01',
                        'status' => $status,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Generate Kunjungan (OPL Only)
                    if ($karyawan->kode_dept == 'OPL' && rand(1, 100) <= 60) {
                        $deltaLat = $faker->randomFloat(6, 0.009, 0.027) * ($faker->boolean ? 1 : -1);
                        $deltaLon = $faker->randomFloat(6, 0.009, 0.027) * ($faker->boolean ? 1 : -1);

                        $visitLat = $anchorLat + $deltaLat;
                        $visitLon = $anchorLon + $deltaLon;

                        DB::table('kunjungan')->insert([
                            'nik' => $karyawan->nik,
                            'tanggal_kunjungan' => $tgl,
                            'deskripsi' => 'Visit Customer ' . $faker->company . ' - Maintenance Routine',
                            'lokasi' => "{$visitLat},{$visitLon}",
                            'foto' => 'visit_dummy.jpg',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                    // Generate Aktivitas (OTK Only)
                    if ($karyawan->kode_dept == 'OTK' && rand(1, 100) <= 70) {
                        DB::table('aktivitas_karyawan')->insert([
                            'nik' => $karyawan->nik,
                            // 'tanggal' removed from schema
                            'aktivitas' => 'Shop Operations ' . $faker->word,
                            'lokasi' => $lokasiPresensi,
                            'foto' => 'activity_dummy.jpg',
                            'created_at' => $tgl . ' 09:00:00',
                            'updated_at' => $tgl . ' 09:00:00'
                        ]);
                    }

                    // Generate Lembur (Overtime)
                    if ($isOvertime) {
                        // Determine Approval Status for Lembur
                        // Rule: Nov 1, 2025 - Jan 19, 2026 -> Processed (80% ACC, 20% TOLAK)
                        // Jan 20, 2026 onwards -> Pending
                        $cutoffDate = Carbon::create(2026, 1, 19);

                        if ($currentDate->lte($cutoffDate)) {
                            // 80% Approved ('1'), 20% Rejected ('2')
                            $statusLembur = (rand(1, 100) <= 80) ? '1' : '2';
                        } else {
                            $statusLembur = '0';
                        }

                        $overtimeData = [
                            'nik' => $karyawan->nik,
                            'tanggal' => $tgl,
                            'lembur_mulai' => $tgl . ' ' . ($isSunday ? '09:00:00' : '17:00:00'),
                            'lembur_selesai' => $tgl . ' ' . ($isSunday ? '14:00:00' : '20:00:00'),
                            'keterangan' => 'Project Deadline',
                            'status' => $statusLembur,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        // If Approved (1), simulate they actually did the overtime
                        if ($statusLembur == '1') {
                            // Actual IN: 0-15 mins relative to start
                            $oStart = Carbon::parse($overtimeData['lembur_mulai']);
                            $oEnd = Carbon::parse($overtimeData['lembur_selesai']);

                            $actualIn = $oStart->copy()->addMinutes(rand(0, 15));
                            $actualOut = $oEnd->copy()->addMinutes(rand(0, 20));

                            $overtimeData = array_merge($overtimeData, [
                                'lembur_in' => $actualIn->toDateTimeString(),
                                'lembur_out' => $actualOut->toDateTimeString(),
                                'foto_lembur_in' => 'dummy_lembur_in.jpg',
                                'foto_lembur_out' => 'dummy_lembur_out.jpg',
                                'lokasi_lembur_in' => $lokasiPresensi,
                                'lokasi_lembur_out' => $lokasiPresensi,
                            ]);
                        } else {
                            $overtimeData = array_merge($overtimeData, [
                                'lembur_in' => null,
                                'lembur_out' => null,
                                'foto_lembur_in' => null,
                                'foto_lembur_out' => null,
                                'lokasi_lembur_in' => null,
                                'lokasi_lembur_out' => null,
                            ]);
                        }

                        DB::table('lembur')->insert($overtimeData);
                    }
                }

                // Next Day
                $currentDate->addDay();
            }
        }
        $this->command->info("Transaction Data Generated!");
    }
}

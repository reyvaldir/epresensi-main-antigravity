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
        $validJamKerja = DB::table('presensi_jamkerja')->pluck('kode_jam_kerja')->toArray();
        $defaultJamKerja = $validJamKerja[0] ?? 'JK01';

        // Anchor Point for OPL Visits (Balikpapan Center)
        $anchorLat = -1.245053;
        $anchorLon = 116.857903;

        // 3. Define Period
        $startDate = Carbon::create(2025, 11, 1);
        $endDate = Carbon::create(2026, 1, 31);

        $this->command->info("Generating Unified Transactions (Nov 25 - Jan 26)...");

        // REVERTED TO ALL EMPLOYEES
        $karyawans = Karyawan::where('nik', 'like', '2401%')->get();

        if ($karyawans->isEmpty()) {
            $this->command->error("No Dummy Employees Found!");
            return;
        }
        $this->command->info("Found " . $karyawans->count() . " employees to process.");

        foreach ($karyawans as $karyawan) {
            $this->command->info("Processing " . $karyawan->nik . "...");
            try {
                // Batches for this employee
                $batchPresensi = [];
                $batchLembur = [];
                $batchKunjungan = [];
                $batchAktivitas = [];

                $batchIzinSakit = [];
                $batchIzinAbsen = [];
                $batchIzinCuti = [];
                $batchIzinDinas = [];

                $currentDate = $startDate->copy();
                $cabang = $cabangs[$karyawan->kode_cabang] ?? null;

                // Validate Schedule
                $scheduleCode = $karyawan->kode_jadwal;
                if (!in_array($scheduleCode, $validJamKerja)) {
                    $scheduleCode = $defaultJamKerja;
                }

                if (!$cabang) {
                    $this->command->warn("Cabang not found for " . $karyawan->nik);
                    continue;
                }

                // Parse Cabang Location
                $locParts = explode(',', $cabang->lokasi_cabang);
                if (count($locParts) < 2) {
                    $cabangLat = -1.26;
                    $cabangLon = 116.88;
                } else {
                    $cabangLat = trim($locParts[0]);
                    $cabangLon = trim($locParts[1]);
                }

                // Loop Dates
                while ($currentDate <= $endDate) {
                    // Determine Status (Sunday is Holiday, Saturday is Workday)
                    $isSunday = $currentDate->isSunday();
                    $tgl = $currentDate->toDateString();

                    // 96% Presence on Workdays (Mon-Sat), 0% on Sunday
                    if (!$isSunday) {
                        $dice = rand(1, 100);
                        if ($dice <= 96) {
                            $status = 'h';
                            $approvalStatus = null;
                        } else {
                            // 4% Non-Hadir
                            // Split: 10% Truant (Alpha), 90% Permission Request
                            $isTruant = rand(1, 100) <= 10;

                            if ($isTruant) {
                                $status = 'a'; // Alpha (Tanpa Keterangan)
                                $approvalStatus = null; // No Request record needed for simple Alpha? 
                                // Actually, system usually needs 'presensi' record with status 'a'.
                            } else {
                                // 8% Absen (S/I/C/D)
                                $status = $faker->randomElement(['s', 'i', 'c', 'd']);

                                // Determine Approval Status
                                // Rule: Nov 1, 2025 - Jan 19, 2026 -> Processed (80% ACC, 20% TOLAK)
                                // Jan 20, 2026 onwards -> Pending
                                $cutoffDate = Carbon::create(2026, 1, 19);

                                if ($currentDate->lte($cutoffDate)) {
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
                                    $kode = 'IS' . $codeDate . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                                    $batchIzinSakit[] = array_merge($baseData, [
                                        'kode_izin_sakit' => $kode,
                                        'doc_sid' => null,
                                        'id_user' => 1
                                    ]);
                                } elseif ($status == 'i') { // Izin
                                    $kode = 'IA' . $codeDate . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                                    $batchIzinAbsen[] = array_merge($baseData, [
                                        'kode_izin' => $kode
                                    ]);
                                } elseif ($status == 'c') { // Cuti
                                    $kode = 'IC' . $codeDate . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                                    $batchIzinCuti[] = array_merge($baseData, [
                                        'kode_izin_cuti' => $kode,
                                        'kode_cuti' => 'C01',
                                        'id_user' => 1,
                                        'keterangan_hrd' => null
                                    ]);
                                } elseif ($status == 'd') { // Dinas
                                    $kode = 'ID' . $codeDate . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                                    $batchIzinDinas[] = array_merge($baseData, [
                                        'kode_izin_dinas' => $kode
                                    ]);
                                }
                            }
                        }

                        // LOGIC GAP HANDLING REVISED:
                        // 1. Truant -> Insert 'a'
                        // 2. Rejected Request ('2') -> Insert 'a' (User Rule)
                        // 3. Approved Request ('1') -> Insert 's'/'i'/'c'
                        // 4. Pending Request ('0') -> Do NOT insert (Results in NA)
                        // 5. Hadir -> Insert 'h'

                        $finalStatus = null;
                        if ($status == 'h') {
                            $finalStatus = 'h';
                        } elseif ($status == 'a') {
                            $finalStatus = 'a';
                        } else {
                            // Permission Request Logic
                            if ($approvalStatus == '1') {
                                $finalStatus = $status; // Approved
                            } elseif ($approvalStatus == '2') {
                                // REJECTED:
                                // Scenario A: Employee attends anyway -> 'h'
                                // Scenario B: Employee skips -> 'a' (Alpha)
                                $decidesToAttend = rand(1, 100) <= 50;
                                $finalStatus = $decidesToAttend ? 'h' : 'a';
                            }
                            // Pending ('0') -> null
                        }

                        if ($finalStatus) {
                            // Base Presensi Data
                            $presensiData = [
                                'nik' => $karyawan->nik,
                                'tanggal' => $tgl,
                                'jam_in' => null,
                                'jam_out' => null,
                                'foto_in' => null,
                                'foto_out' => null,
                                'lokasi_in' => null,
                                'lokasi_out' => null,
                                'kode_jam_kerja' => $scheduleCode,
                                'status' => $finalStatus,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];

                            // Add Check-in/out logic ONLY for 'h'
                            if ($finalStatus == 'h') {
                                // Logic Location: < 70m from Cabang
                                $myLat = $cabangLat + ($faker->randomFloat(6, -0.0006, 0.0006));
                                $myLon = $cabangLon + ($faker->randomFloat(6, -0.0006, 0.0006));
                                $lokasiPresensi = "{$myLat},{$myLon}";

                                // Jam Masuk Logic (Start 08:00)
                                if (rand(1, 100) <= 90) {
                                    $jamIn = $tgl . ' 07:' . str_pad(rand(0, 55), 2, '0', STR_PAD_LEFT) . ':00';
                                } else {
                                    // Late
                                    $hour = rand(8, 9);
                                    $minute = ($hour == 9) ? rand(0, 30) : rand(5, 59);
                                    $jamIn = $tgl . ' ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ':00';
                                }

                                // Jam Pulang Logic (End 17:00)
                                $isOvertime = rand(1, 100) <= 6;
                                $isEarly = rand(1, 100) <= 5;

                                if ($isEarly && !$isOvertime) {
                                    $jamOut = $tgl . ' 16:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00';
                                } else {
                                    // Normal or Overtime Base
                                    $jamOut = $tgl . ' ' . rand(17, 19) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00';
                                }

                                $presensiData['jam_in'] = $jamIn;
                                $presensiData['jam_out'] = $isOvertime ? null : $jamOut;
                                $presensiData['foto_in'] = 'dummy_in.jpg';
                                $presensiData['foto_out'] = 'dummy_out.jpg';
                                $presensiData['lokasi_in'] = $lokasiPresensi;
                                $presensiData['lokasi_out'] = $lokasiPresensi;

                                // Generate Kunjungan (OPL Only)
                                if ($karyawan->kode_dept == 'OPL' && rand(1, 100) <= 60) {
                                    $deltaLat = $faker->randomFloat(6, 0.009, 0.027) * ($faker->boolean ? 1 : -1);
                                    $deltaLon = $faker->randomFloat(6, 0.009, 0.027) * ($faker->boolean ? 1 : -1);
                                    $visitLat = $anchorLat + $deltaLat;
                                    $visitLon = $anchorLon + $deltaLon;

                                    $batchKunjungan[] = [
                                        'nik' => $karyawan->nik,
                                        'tanggal_kunjungan' => $tgl,
                                        'deskripsi' => 'Visit Customer ' . $faker->company . ' - Maintenance Routine',
                                        'lokasi' => "{$visitLat},{$visitLon}",
                                        'foto' => 'visit_dummy.jpg',
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ];
                                }

                                // Generate Aktivitas (OTK Only)
                                if ($karyawan->kode_dept == 'OTK' && rand(1, 100) <= 70) {
                                    $batchAktivitas[] = [
                                        'nik' => $karyawan->nik,
                                        'aktivitas' => 'Shop Operations ' . $faker->word,
                                        'lokasi' => $lokasiPresensi,
                                        'foto' => 'activity_dummy.jpg',
                                        'created_at' => $tgl . ' 09:00:00',
                                        'updated_at' => $tgl . ' 09:00:00'
                                    ];
                                }

                                // Generate Lembur (Overtime)
                                if ($isOvertime) {
                                    $cutoffDate = Carbon::create(2026, 1, 19);
                                    if ($currentDate->lte($cutoffDate)) {
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

                                    if ($statusLembur == '1') {
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
                                            'lokasi_lembur_in' => null
                                        ]);
                                    }
                                    $batchLembur[] = $overtimeData;
                                } // End Overtime

                            } // End Status 'h' logic

                            $batchPresensi[] = $presensiData;
                        }

                    } else {
                        // Sunday / Holiday
                        // Usually no presensi, or status 'libur'?
                        // For now, no insert.
                    }


                    $currentDate->addDay();
                } // End Date Loop

                // --- BATCH INSERT PER EMPLOYEE ---
                if (!empty($batchPresensi))
                    DB::table('presensi')->insert($batchPresensi);
                if (!empty($batchLembur))
                    DB::table('lembur')->insert($batchLembur);
                if (!empty($batchKunjungan))
                    DB::table('kunjungan')->insert($batchKunjungan);
                if (!empty($batchAktivitas))
                    DB::table('aktivitas_karyawan')->insert($batchAktivitas);

                if (!empty($batchIzinSakit))
                    DB::table('presensi_izinsakit')->insert($batchIzinSakit);
                if (!empty($batchIzinAbsen))
                    DB::table('presensi_izinabsen')->insert($batchIzinAbsen);
                if (!empty($batchIzinCuti))
                    DB::table('presensi_izincuti')->insert($batchIzinCuti);
                if (!empty($batchIzinDinas))
                    DB::table('presensi_izindinas')->insert($batchIzinDinas);

            } catch (\Exception $e) {
                // LOG THE ERROR
                $this->command->error("ERROR Processing " . $karyawan->nik . ": " . $e->getMessage());
            }

        } // End Employee Loop

        $this->command->info("Transaction Data Generated Successfully!");
    }
}

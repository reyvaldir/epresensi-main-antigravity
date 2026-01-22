<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use Carbon\Carbon;

class DummyPayrollSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating Payroll Data (Gaji, Tunjangan, BPJS)...');

        $karyawans = Karyawan::where('nik', 'like', '2401%')->get();
        if ($karyawans->isEmpty()) {
            $this->command->error('No dummy employees found!');
            return;
        }

        // 1. Ensure Master Tunjangan Exists
        $tunjangans = [
            ['kode_jenis_tunjangan' => 'TJ01', 'jenis_tunjangan' => 'Makan'],
            ['kode_jenis_tunjangan' => 'TJ02', 'jenis_tunjangan' => 'Transport'],
            ['kode_jenis_tunjangan' => 'TJ03', 'jenis_tunjangan' => 'Jabatan'],
            ['kode_jenis_tunjangan' => 'TJ04', 'jenis_tunjangan' => 'Tanggung Jawab'], // New
        ];

        foreach ($tunjangans as $tj) {
            DB::table('jenis_tunjangan')->updateOrInsert(
                ['kode_jenis_tunjangan' => $tj['kode_jenis_tunjangan']],
                ['jenis_tunjangan' => $tj['jenis_tunjangan'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 3. Define Salary Tiers per Jabatan
        // Fetch all positions used by dummy employees
        $jabatans = DB::table('jabatan')->pluck('kode_jabatan')->toArray();
        $gajiMap = [];

        foreach ($jabatans as $jab) {
            // Assign a random fixed base for this position to keep it realistic
            // Range: 2.3jt - 3.5jt
            $gajiMap[$jab] = rand(2300, 3500) * 1000;
        }

        // 4. Loop Employees
        foreach ($karyawans as $k) {
            // Get base salary for this employee's position
            $jabatanSalary = $gajiMap[$k->kode_jabatan] ?? 2300000;
            // Add small variation (e.g. +- 100k) for seniority simulation
            $fixSalary = $jabatanSalary + (rand(-1, 5) * 50000);
            // Ensure bounds
            $fixSalary = max(2300000, min(3500000, $fixSalary));

            // A. Gaji Pokok
            $kodeGaji = 'GJ' . substr($k->nik, -5); // Simply use last NIK digits
            DB::table('karyawan_gaji_pokok')->updateOrInsert(
                ['nik' => $k->nik],
                [
                    'kode_gaji' => $kodeGaji,
                    'jumlah' => $fixSalary,
                    'tanggal_berlaku' => '2023-01-01',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Calculate Position-Based Allowances
            // Heads (J01, J03, J05) get more than Staff (J04, J06, others)
            $isHead = in_array($k->kode_jabatan, ['J01', 'J03', 'J05']);

            $uJabatan = $isHead ? 1000000 : 500000;
            $uTanggungJawab = $isHead ? 750000 : 250000;

            // B. Tunjangan Header
            $kodeTunjRecord = 'TR' . substr($k->nik, -5);
            DB::table('karyawan_tunjangan')->updateOrInsert(
                ['nik' => $k->nik],
                [
                    'kode_tunjangan' => $kodeTunjRecord,
                    'tanggal_berlaku' => '2023-01-01',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // C. Tunjangan Detail (Makan, Transport, Jabatan, Tanggung Jawab)
            $details = [
                ['kode_jenis_tunjangan' => 'TJ01', 'jumlah' => 500000], // Makan
                ['kode_jenis_tunjangan' => 'TJ02', 'jumlah' => 300000], // Transport
                ['kode_jenis_tunjangan' => 'TJ03', 'jumlah' => $uJabatan], // Jabatan
                ['kode_jenis_tunjangan' => 'TJ04', 'jumlah' => $uTanggungJawab], // Tanggung Jawab
            ];

            foreach ($details as $d) {
                // ... (Insert Logic)
                DB::table('karyawan_tunjangan_detail')
                    ->where('kode_tunjangan', $kodeTunjRecord)
                    ->where('kode_jenis_tunjangan', $d['kode_jenis_tunjangan'])
                    ->delete();

                DB::table('karyawan_tunjangan_detail')->insert([
                    'kode_tunjangan' => $kodeTunjRecord,
                    'kode_jenis_tunjangan' => $d['kode_jenis_tunjangan'],
                    'jumlah' => $d['jumlah'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // D. BPJS TK (2% JHT)
            $kodeBpjs = 'BP' . substr($k->nik, -5);
            $bpjsAmount = $fixSalary * 0.02;

            DB::table('karyawan_bpjstenagakerja')->updateOrInsert(
                ['nik' => $k->nik],
                [
                    'kode_bpjs_tk' => $kodeBpjs,
                    'jumlah' => $bpjsAmount,
                    'tanggal_berlaku' => '2023-01-01',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            // E. BPJS Kesehatan (1% Employee)
            // Assumed Table: karyawan_bpjskesehatan
            $kodeBpjsKes = 'KS' . substr($k->nik, -5);
            $bpjsKesAmount = $fixSalary * 0.01;

            DB::table('karyawan_bpjskesehatan')->updateOrInsert(
                ['nik' => $k->nik],
                [
                    'kode_bpjs_kesehatan' => $kodeBpjsKes,
                    'jumlah' => $bpjsKesAmount,
                    'tanggal_berlaku' => '2023-01-01',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        $this->command->info('Payroll Dummy Data Generated Successfully!');
    }
}

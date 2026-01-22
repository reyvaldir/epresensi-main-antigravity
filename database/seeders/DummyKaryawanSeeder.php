<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\Karyawan;

class DummyKaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating 30 Dummy Employees (Rule-Based)...');

        $faker = Faker::create('id_ID');

        // Master Data Check
        $branches = ['KRJ', 'CRB', 'SPG']; // User Constraint
        $depts = ['ADM', 'OPL', 'OTK'];     // User Constraint

        // 1. Administration (6 Pax) -> KRJ ONLY
        // 1 Head (J03) + 5 Staff (J04)
        $this->createAdminTeam($faker);

        // 2. Leadership & Field Distribution (Remaining 24 Pax)
        $this->createFieldAndOpsTeam($faker);

        $this->command->info('Dummy Employees Created Successfully!');
    }

    private function createAdminTeam($faker)
    {
        // 1 Head Admin (J03) at KRJ
        $this->insertKaryawan(1, 'KRJ', 'ADM', 'J03', $faker);

        // 5 Staff Admin (J04) at KRJ
        for ($i = 2; $i <= 6; $i++) {
            $this->insertKaryawan($i, 'KRJ', 'ADM', 'J04', $faker);
        }
    }

    private function createFieldAndOpsTeam($faker)
    {
        // Remaining 24 Employees (Index 7 to 30)
        // Distribution:
        // KRJ: Need 1 OPL Head (J05), 1 Toko Head (J01)
        // CRB: Need 1 OPL Head (J05), 1 Toko Head (J01)
        // SPG: Need 1 OPL Head (J05), 1 Toko Head (J01)
        // Rest: Spread OPL/OTK Staff

        $startIndex = 7;
        $branches = ['KRJ', 'CRB', 'SPG'];

        // A. Assign Leaders First (6 Pax)
        foreach ($branches as $branch) {
            // Head OPL (J05)
            $this->insertKaryawan($startIndex++, $branch, 'OPL', 'J05', $faker);
            // Head Toko (J01)
            $this->insertKaryawan($startIndex++, $branch, 'OTK', 'J01', $faker);
        }

        // B. Remainder (30 - 6 - 6 = 18 Pax) -> Staff (J06/J07 roughly)
        // We will assign them OPL or OTK randomly, distributed across branches
        // Current Index is 13. Target is 30.

        $branchPointer = 0;
        for ($i = $startIndex; $i <= 30; $i++) {
            $branch = $branches[$branchPointer % 3]; // Round Robin Distribution
            $dept = $faker->randomElement(['OPL', 'OTK']);
            $this->insertKaryawan($i, $branch, $dept, 'J06', $faker);
            $branchPointer++;
        }
    }

    private function insertKaryawan($index, $kode_cabang, $kode_dept, $kode_jabatan, $faker)
    {
        $nik = '2401' . str_pad($index, 4, '0', STR_PAD_LEFT);

        // Skip if exists
        if (Karyawan::where('nik', $nik)->exists())
            return;

        $name = $faker->firstName . ' ' . $faker->lastName;

        // Dynamic Schedule
        $jadwal = DB::table('presensi_jamkerja')->value('kode_jam_kerja') ?? 'JK01';

        Karyawan::create([
            'nik' => $nik,
            'nama_karyawan' => $name,
            'kode_dept' => $kode_dept,
            'kode_cabang' => $kode_cabang,
            'kode_jabatan' => $kode_jabatan, // User Specified
            'kode_status_kawin' => 'TK',
            'foto' => null,
            'password' => Hash::make('12345'),
            'kode_jadwal' => $jadwal,
            'lock_location' => '0',

            // Deterministic Unique Keys
            'no_ktp' => '320101' . str_pad($index, 10, '0', STR_PAD_LEFT),
            'no_hp' => '0812' . str_pad($index, 8, '0', STR_PAD_LEFT),

            'tempat_lahir' => substr($faker->city, 0, 20),
            'tanggal_lahir' => $faker->date('Y-m-d', '2000-01-01'),
            'alamat' => $faker->address,
            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
            'pendidikan_terakhir' => 'S1',
            'status_karyawan' => 'K',
            'status_aktif_karyawan' => '1', // Required field
            'tanggal_masuk' => '2023-01-01',
        ]);
    }
}

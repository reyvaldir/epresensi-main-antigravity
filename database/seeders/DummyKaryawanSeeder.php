<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\Karyawan;

class DummyKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Referensi Data Master (Dynamic Fetching)
        $depts = DB::table('departemen')->pluck('kode_dept')->toArray();
        $cabangs = DB::table('cabang')->pluck('kode_cabang')->toArray();
        $jabatans = DB::table('jabatan')->pluck('kode_jabatan')->toArray();
        $status_kawin = DB::table('status_kawin')->pluck('kode_status_kawin')->toArray();

        // Fallback for demo if tables are empty (Optional, but better to warn)
        if (empty($depts) || empty($cabangs) || empty($jabatans)) {
            $this->command->error("Data Master (Departemen/Cabang/Jabatan) Kosong! Harap jalankan seeder master terlebih dahulu.");
            return;
        }

        if (empty($status_kawin)) {
            $status_kawin = ['TK', 'K0', 'K1', 'K2'];
        }

        // Daftar Nama Realistis untuk Demo Skripsi
        $dummy_names = [
            'Budi Santoso',
            'Siti Aminah',
            'Rudi Hartono',
            'Dewi Sartika',
            'Agus Pratama',
            'Rina Wati',
            'Joko Susilo',
            'Eka Kurniawan',
            'Sari Indah',
            'Hendra Jaya',
            'Tri Utami',
            'Dedi Mulyadi',
            'Lina Marlina',
            'Iwan Setiawan',
            'Maya Putri'
        ];

        $this->command->info("Found " . count($depts) . " Departments, " . count($cabangs) . " Branches, " . count($jabatans) . " Positions.");
        $this->command->info('Creating 15 Dummy Employees...');

        foreach ($dummy_names as $index => $name) {
            $nik = '2401' . str_pad($index + 10, 4, '0', STR_PAD_LEFT); // ex: 24010010

            // Cek exist
            if (Karyawan::where('nik', $nik)->exists()) {
                continue;
            }

            $dept = $faker->randomElement($depts);
            $cabang = $faker->randomElement($cabangs);
            $jabatan = $faker->randomElement($jabatans);

            // Logic Jabatan & Gaji/Tunjangan bisa dikembangkan nanti

            Karyawan::create([
                'nik' => $nik,
                'nama_karyawan' => $name,
                'no_ktp' => $faker->nik,
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date('Y-m-d', '2000-01-01'),
                'alamat' => $faker->address,
                'no_hp' => '08' . $faker->numerify('##########'), // Max 13 chars
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'kode_status_kawin' => $faker->randomElement($status_kawin),
                'pendidikan_terakhir' => $faker->randomElement(['S1', 'D3', 'SMA']),

                'kode_cabang' => $cabang,
                'kode_dept' => $dept,
                'kode_jabatan' => $jabatan,

                'tanggal_masuk' => '2023-01-01',
                'status_karyawan' => $faker->randomElement(['T', 'K']), // Tetap / Kontrak
                'status_aktif_karyawan' => '1',

                'foto' => null, // default
                'password' => Hash::make('12345'), // Default Password demo

                'kode_jadwal' => 'JK01', // Asumsi JK01 ada (Jam Kerja Normal)
                'lock_location' => '0', // Bebas absen dimana saja (untuk kemudahan demo)
            ]);

            // Assign User Permission / Role if using Spatie for Login
            // But this system uses `karyawan` auth guard table directly.

            // Assign Facerecognition dummy (optional)
        }

        $this->command->info('Dummy Employees Created Successfully!');
    }
}

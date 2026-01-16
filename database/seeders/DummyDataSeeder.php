<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Dummy Data Generation for Thesis Demo...');

        // 1. Create Dummy Employees (if not exists)
        $this->call(DummyKaryawanSeeder::class);

        // 2. Create Unified Daily Transactions (Attendance, Leave, Activity, etc.)
        // Covers period: 1 Nov 2025 - 17 Jan 2026
        $this->call(DummyTransactionSeeder::class);

        // 3. Create Payroll Data (Gaji, Tunjangan, BPJS)
        $this->call(DummyPayrollSeeder::class);

        $this->command->info('ALL DUMMY DATA GENERATED SUCCESSFULLY! 🚀');
        $this->command->info('You can now login as admin to see the statistics.');
    }
}

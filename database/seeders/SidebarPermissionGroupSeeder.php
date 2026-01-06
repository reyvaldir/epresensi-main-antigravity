<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission_group;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class SidebarPermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Granular Mapping based on Sidebar Menu Items
        $structure = [
            // A. Dashboard
            'Dashboard' => ['dashboard'],

            // B. Data Master (Organisasi & SDM)
            'Data Karyawan' => ['karyawan'],
            'Departemen' => ['departemen'],
            'Kantor Cabang' => ['cabang'],
            'Jabatan' => ['jabatan'],
            'Grup' => ['grup'],

            // C. Data Master (Aturan Waktu)
            'Pola Jam Kerja' => ['jamkerja'],
            'Jam Kerja Departemen' => ['jamkerjabydept'],
            'Hari Libur' => ['harilibur'],
            'Master Cuti' => ['cuti'],

            // D. Manajemen Presensi
            'Monitoring Presensi' => ['presensi'],
            'Tracking Presensi' => ['trackingpresensi'],
            'Aktivitas Karyawan' => ['aktivitaskaryawan'],
            'Pengajuan Izin' => ['izinabsen', 'izinsakit', 'izincuti', 'izindinas', 'pengajuanizin'],
            'Istirahat' => ['presensiistirahat'],
            'Lembur' => ['lembur'],

            // E. Kunjungan
            'Data Kunjungan' => ['kunjungan'],
            'Tracking Kunjungan' => ['tracking-kunjungan'],

            // F. Payroll (Master)
            'Gaji Pokok' => ['gajipokok'],
            'Jenis Tunjangan' => ['jenistunjangan'],
            'Data Tunjangan' => ['tunjangan'],
            'BPJS Kesehatan' => ['bpjskesehatan'],
            'BPJS Ketenagakerjaan' => ['bpjstenagakerja'],
            'Denda Keterlambatan' => ['denda'],

            // G. Payroll (Transaksi)
            'Penyesuaian Gaji' => ['penyesuaiangaji'],
            'Slip Gaji' => ['slipgaji'],

            // H. Laporan
            'Laporan Presensi' => ['laporan'],

            // I. Pengaturan Sistem
            'General Setting' => ['generalsetting'],
            'Bersihkan Foto' => ['bersihkanfoto'],
            'Manajemen User' => ['users'],
            'Manajemen Role' => ['roles'],
            'Manajemen Permission' => ['permissions'],
            'Manajemen Group Permission' => ['permissiongroups'],
            'WA Gateway' => ['wagateway'],
            'PWA Generator' => ['pwa'],
            'Update System' => ['update'], // Also covers 'update' prefix
            'Face Recognition' => ['facerecognition']
        ];

        DB::beginTransaction();
        try {
            foreach ($structure as $groupName => $prefixes) {
                // A. Create or Find the Group
                $group = Permission_group::firstOrCreate(['name' => $groupName]);
                $this->command->info("Processing Group: {$groupName}");

                foreach ($prefixes as $prefix) {
                    // B. Find permissions starting with this prefix
                    // Logic:
                    // 1. Exact match (e.g. 'update')
                    // 2. Dot prefix (e.g. 'update.Index')

                    $permissions = Permission::where(function ($query) use ($prefix) {
                        $query->where('name', $prefix)
                            ->orWhere('name', 'like', "{$prefix}.%");
                    })->get();

                    foreach ($permissions as $permission) {
                        $permission->id_permission_group = $group->id;
                        $permission->save();
                        $this->command->info("  - Assigned: {$permission->name}");
                    }
                }
            }

            // C. Handle Orphans (Group them into 'Lainnya')
            $othersGroup = Permission_group::firstOrCreate(['name' => 'Lainnya']);
            Permission::whereNull('id_permission_group')->orWhere('id_permission_group', 0)->update(['id_permission_group' => $othersGroup->id]);

            DB::commit();
            $this->command->info("Granular Sidebar Permission Grouping Completed Successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error: " . $e->getMessage());
        }
    }
}

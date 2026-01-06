<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission_group;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class CleanupEmptyPermissionGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $groups = Permission_group::all();
            $deletedCount = 0;

            foreach ($groups as $group) {
                // Check if any permission uses this group ID
                $count = Permission::where('id_permission_group', $group->id)->count();

                if ($count === 0) {
                    $this->command->warn("Deleting Empty Group: {$group->name}");
                    $group->delete();
                    $deletedCount++;
                }
            }

            DB::commit();

            if ($deletedCount > 0) {
                $this->command->info("SUCCESS: Deleted {$deletedCount} empty permission groups.");
            } else {
                $this->command->info("INFO: No empty permission groups found.");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error: " . $e->getMessage());
        }
    }
}

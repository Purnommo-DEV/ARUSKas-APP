<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        $legacyRole = DB::table('roles')->where('name', 'jamaah')->where('guard_name', 'web')->first();
        $userRole = DB::table('roles')->where('name', 'user')->where('guard_name', 'web')->first();

        if ($legacyRole && ! $userRole) {
            DB::table('roles')->where('id', $legacyRole->id)->update(['name' => 'user']);
        } elseif ($legacyRole && $userRole) {
            $this->mergeRoleAssignments((int) $legacyRole->id, (int) $userRole->id);
        }

        if (Schema::hasTable('users') && ! DB::table('users')->where('email', 'user@example.com')->exists()) {
            DB::table('users')->where('email', 'jamaah@example.com')->update([
                'name' => 'User Laporan',
                'email' => 'user@example.com',
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('name', 'user')->where('guard_name', 'web')->update(['name' => 'jamaah']);
        }

        if (Schema::hasTable('users') && ! DB::table('users')->where('email', 'jamaah@example.com')->exists()) {
            DB::table('users')->where('email', 'user@example.com')->update([
                'name' => 'Jamaah Kajian',
                'email' => 'jamaah@example.com',
            ]);
        }
    }

    private function mergeRoleAssignments(int $legacyRoleId, int $userRoleId): void
    {
        DB::table('model_has_roles')->where('role_id', $legacyRoleId)->get()->each(function ($assignment) use ($userRoleId): void {
            DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $userRoleId,
                'model_type' => $assignment->model_type,
                'model_id' => $assignment->model_id,
            ]);
        });

        DB::table('role_has_permissions')->where('role_id', $legacyRoleId)->get()->each(function ($assignment) use ($userRoleId): void {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $assignment->permission_id,
                'role_id' => $userRoleId,
            ]);
        });

        DB::table('model_has_roles')->where('role_id', $legacyRoleId)->delete();
        DB::table('role_has_permissions')->where('role_id', $legacyRoleId)->delete();
        DB::table('roles')->where('id', $legacyRoleId)->delete();
    }
};

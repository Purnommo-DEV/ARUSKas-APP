<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = [
            'dashboard.view',
            'finance.view',
            'categories.manage',
            'transactions.manage',
            'users.manage',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminRole = Role::findOrCreate('admin', 'web');
        $userRole = Role::findOrCreate('user', 'web');
        $adminRole->syncPermissions($permissions);
        $userRole->syncPermissions(['dashboard.view', 'finance.view']);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Administrator', 'password' => Hash::make('password')],
        );
        $admin->syncRoles(['admin']);

        $user = User::query()->firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'User Laporan', 'password' => Hash::make('password')],
        );
        $user->syncRoles(['user']);

        $categories = [
            ['name' => 'Infak Jamaah', 'type' => CategoryType::Income->value],
            ['name' => 'Donasi', 'type' => CategoryType::Income->value],
            ['name' => 'Mukafaah', 'type' => CategoryType::Expense->value],
            ['name' => 'Setor Masjid', 'type' => CategoryType::Expense->value],
            ['name' => 'Konsumsi', 'type' => CategoryType::Expense->value],
            ['name' => 'Transport', 'type' => CategoryType::Expense->value],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['name' => $category['name'], 'type' => $category['type']],
                ['is_active' => true],
            );
        }

        Setting::current();
    }
}

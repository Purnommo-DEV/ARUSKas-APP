<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_dashboard_and_transaction_datatable(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Keuangan')
            ->assertSee('5 Transaksi Terbaru')
            ->assertDontSee('Tambah Transaksi');

        $this->actingAs($admin)
            ->getJson(route('admin.transactions.data', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
            ]))
            ->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
    }

    public function test_user_can_only_open_read_only_financial_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->where('email', 'user@example.com')->firstOrFail();

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Keuangan')
            ->assertSee('5 Transaksi Terbaru')
            ->assertDontSee('Tambah Transaksi');

        $this->actingAs($user)
            ->get(route('user.transactions.index'))
            ->assertOk()
            ->assertSee('Filter Bulan')
            ->assertDontSee('Tambah Transaksi');

        $this->actingAs($user)
            ->get(route('user.reports.index'))
            ->assertOk()
            ->assertSee('Laporan Keuangan')
            ->assertDontSee('Tambah Transaksi');

        $this->actingAs($user)
            ->getJson(route('user.transactions.data', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'month' => now()->month,
                'year' => now()->year,
            ]))
            ->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs($user)
            ->postJson(route('admin.transactions.store'), [])
            ->assertForbidden();
    }

    public function test_admin_cannot_demote_the_account_currently_in_use(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->putJson(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'user',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('role');

        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }
}

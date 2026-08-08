<?php

namespace Tests\Feature;

use App\Models\OpeningBalance;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpeningBalanceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_one_opening_balance_per_period_outside_settings(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('email', 'admin@aruskas.com')->firstOrFail();

        $this->actingAs($admin)
            ->postJson(route('admin.opening-balances.store'), $this->payload(14_000))
            ->assertCreated()
            ->assertJsonPath('data.opening_balance', 14_000);

        $openingBalance = OpeningBalance::query()->firstOrFail();
        $this->assertSame(14_000, $openingBalance->opening_balance);
        $this->assertSame($admin->id, $openingBalance->created_by);

        $this->actingAs($admin)
            ->postJson(route('admin.opening-balances.store'), $this->payload(20_000))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('period_year');

        $this->actingAs($admin)
            ->putJson(route('admin.opening-balances.update', $openingBalance), [
                ...$this->payload(25_000),
                'notes' => 'Penyesuaian kas awal',
            ])
            ->assertOk()
            ->assertJsonPath('data.opening_balance', 25_000);

        $this->assertDatabaseHas('opening_balances', [
            'id' => $openingBalance->id,
            'opening_balance' => 25_000,
            'notes' => 'Penyesuaian kas awal',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertDontSee('Saldo Awal Kas');

        $this->actingAs($admin)
            ->deleteJson(route('admin.opening-balances.destroy', $openingBalance))
            ->assertOk();

        $this->assertDatabaseMissing('opening_balances', ['id' => $openingBalance->id]);

        $user = User::query()->where('email', 'user@aruskas.com')->firstOrFail();
        $this->actingAs($user)
            ->get(route('admin.opening-balances.index'))
            ->assertForbidden();
    }

    private function payload(int $openingBalance): array
    {
        return [
            'period_year' => 2026,
            'period_month' => 8,
            'opening_balance' => $openingBalance,
        ];
    }
}

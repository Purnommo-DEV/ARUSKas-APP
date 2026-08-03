<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpeningBalanceSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_opening_balance_is_saved_as_integer_then_locked_until_an_admin_confirms_a_change(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->postJson(route('admin.settings.update'), $this->payload(14_000))
            ->assertOk()
            ->assertJsonPath('data.opening_balance', 14_000)
            ->assertJsonPath('data.opening_balance_set', true);

        $setting = Setting::current()->fresh();
        $this->assertSame(14_000, $setting->opening_balance);
        $this->assertTrue($setting->opening_balance_set);

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('data-raw-value="14000"', false)
            ->assertSee('readonly', false)
            ->assertSee('Ubah Saldo Awal');

        $this->actingAs($admin)
            ->postJson(route('admin.settings.update'), $this->payload(25_000))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('opening_balance');

        $this->assertSame(14_000, Setting::current()->fresh()->opening_balance);

        $this->actingAs($admin)
            ->postJson(route('admin.settings.update'), [
                ...$this->payload(25_000),
                'confirm_opening_balance_change' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.opening_balance', 25_000);

        $this->assertSame(25_000, Setting::current()->fresh()->opening_balance);
    }

    private function payload(int $openingBalance): array
    {
        return [
            'study_name' => 'Kajian Kita',
            'mosque_name' => 'Masjid Kita',
            'opening_balance' => $openingBalance,
        ];
    }
}

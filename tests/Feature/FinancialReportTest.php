<?php

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Enums\PaymentMethod;
use App\Models\Category;
use App\Models\OpeningBalance;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FinancialReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_summary_uses_previous_month_closing_balance_as_opening_balance(): void
    {
        $user = User::factory()->create();
        OpeningBalance::query()->create([
            'period_year' => 2026,
            'period_month' => 1,
            'opening_balance' => 100_000,
            'created_by' => $user->id,
        ]);
        $income = Category::query()->create([
            'name' => 'Infak',
            'type' => CategoryType::Income,
            'is_active' => true,
        ]);
        $expense = Category::query()->create([
            'name' => 'Konsumsi',
            'type' => CategoryType::Expense,
            'is_active' => true,
        ]);

        $this->transaction($user, $income, '2026-01-05', 1_000_000);
        $this->transaction($user, $expense, '2026-01-12', 200_000);
        $this->transaction($user, $income, '2026-02-02', 500_000);
        $this->transaction($user, $expense, '2026-02-09', 150_000);

        $summary = app(FinancialReportService::class)->summary(2, 2026);

        $this->assertSame(900_000, $summary['opening_balance']);
        $this->assertSame(500_000, $summary['cash_in']);
        $this->assertSame(150_000, $summary['cash_out']);
        $this->assertSame(1_250_000, $summary['closing_balance']);

        $lastRow = app(FinancialReportService::class)
            ->transactionTableQuery(2, 2026)
            ->get()
            ->last();

        $this->assertSame($summary['closing_balance'], (int) $lastRow->running_balance);
    }

    public function test_initial_cash_balance_is_used_before_any_previous_period_transaction_exists(): void
    {
        $user = User::factory()->create();
        OpeningBalance::query()->create([
            'period_year' => 2026,
            'period_month' => 1,
            'opening_balance' => 250_000,
            'created_by' => $user->id,
        ]);
        $income = Category::query()->create([
            'name' => 'Donasi Awal',
            'type' => CategoryType::Income,
            'is_active' => true,
        ]);

        $this->transaction($user, $income, '2026-01-02', 50_000);

        $summary = app(FinancialReportService::class)->summary(1, 2026);

        $this->assertSame(250_000, $summary['opening_balance']);
        $this->assertSame(300_000, $summary['closing_balance']);
    }

    public function test_running_balance_is_preserved_when_table_rows_are_searched(): void
    {
        $user = User::factory()->create();
        $income = Category::query()->create([
            'name' => 'Donasi',
            'type' => CategoryType::Income,
            'is_active' => true,
        ]);
        $expense = Category::query()->create([
            'name' => 'Transport',
            'type' => CategoryType::Expense,
            'is_active' => true,
        ]);

        $this->transaction($user, $income, '2026-03-01', 1_000_000, 'Donatur');
        $this->transaction($user, $expense, '2026-03-02', 200_000, 'Vendor Dicari');

        $row = app(FinancialReportService::class)
            ->transactionTableQuery(3, 2026)
            ->where('party_name', 'Vendor Dicari')
            ->first();

        $this->assertSame(800_000.0, (float) $row->running_balance);
    }

    public function test_explicit_opening_balance_takes_priority_over_the_previous_period_closing_balance(): void
    {
        $user = User::factory()->create();
        $income = Category::query()->create([
            'name' => 'Donasi',
            'type' => CategoryType::Income,
            'is_active' => true,
        ]);

        OpeningBalance::query()->create([
            'period_year' => 2026,
            'period_month' => 1,
            'opening_balance' => 100_000,
            'created_by' => $user->id,
        ]);
        OpeningBalance::query()->create([
            'period_year' => 2026,
            'period_month' => 2,
            'opening_balance' => 350_000,
            'created_by' => $user->id,
        ]);
        $this->transaction($user, $income, '2026-01-05', 1_000_000);
        $this->transaction($user, $income, '2026-02-02', 50_000);

        $summary = app(FinancialReportService::class)->summary(2, 2026);

        $this->assertSame(350_000, $summary['opening_balance']);
        $this->assertSame(400_000, $summary['closing_balance']);
    }

    private function transaction(
        User $user,
        Category $category,
        string $date,
        int $amount,
        string $partyName = 'Penguji',
    ): void {
        Transaction::query()->create([
            'transaction_date' => $date,
            'week_start' => $date,
            'payment_method' => PaymentMethod::Cash,
            'category_id' => $category->id,
            'party_name' => $partyName,
            'amount' => $amount,
            'created_by' => $user->id,
        ]);
    }
}

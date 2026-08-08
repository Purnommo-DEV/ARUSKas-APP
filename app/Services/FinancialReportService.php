<?php

namespace App\Services;

use App\Enums\CategoryType;
use App\Models\OpeningBalance;
use App\Models\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /** @var array<string, array{month: int, year: int, period_label: string, opening_balance: int, cash_in: int, cash_out: int, closing_balance: int}> */
    private array $summaries = [];

    private ?CarbonImmutable $firstFinancialPeriod = null;

    public function summary(int $month, int $year): array
    {
        $period = CarbonImmutable::create($year, $month)->startOfMonth();
        $key = $period->format('Y-m');

        if (isset($this->summaries[$key])) {
            return $this->summaries[$key];
        }

        $cashIn = (int) $this->transactionsByTypeForPeriod($period, CategoryType::Income)->sum('amount');
        $cashOut = (int) $this->transactionsByTypeForPeriod($period, CategoryType::Expense)->sum('amount');
        $openingBalance = $this->openingBalanceFor($period);

        return $this->summaries[$key] = [
            'month' => $month,
            'year' => $year,
            'period_label' => $period->locale('id')->translatedFormat('F Y'),
            'opening_balance' => $openingBalance,
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'closing_balance' => $openingBalance + $cashIn - $cashOut,
        ];
    }

    public function availableYears(): array
    {
        $transactionBounds = Transaction::query()
            ->selectRaw('MIN(transaction_date) AS oldest, MAX(transaction_date) AS newest')
            ->first();
        $openingBalanceBounds = OpeningBalance::query()
            ->selectRaw('MIN(period_year) AS oldest, MAX(period_year) AS newest')
            ->first();

        $currentYear = now()->year;
        $firstYear = min(array_filter([
            $currentYear,
            $transactionBounds->oldest ? (int) substr((string) $transactionBounds->oldest, 0, 4) : null,
            $openingBalanceBounds->oldest ? (int) $openingBalanceBounds->oldest : null,
        ]));
        $lastYear = max(array_filter([
            $currentYear,
            $transactionBounds->newest ? (int) substr((string) $transactionBounds->newest, 0, 4) : null,
            $openingBalanceBounds->newest ? (int) $openingBalanceBounds->newest : null,
        ]));

        return range($lastYear, $firstYear);
    }

    public function transactionTableQuery(int $month, int $year): Builder
    {
        $period = $this->summary($month, $year);
        $start = CarbonImmutable::create($year, $month)->startOfMonth();
        $signedAmount = "CASE WHEN categories.type = '".CategoryType::Income->value."' THEN transactions.amount ELSE -transactions.amount END";

        $rankedTransactions = Transaction::query()
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->whereBetween('transactions.transaction_date', [$start->toDateString(), $start->endOfMonth()->toDateString()])
            ->select([
                'transactions.id',
                'transactions.transaction_date',
                'transactions.week_start',
                'transactions.payment_method',
                'transactions.party_name',
                'transactions.amount',
                'transactions.notes',
                'transactions.proof_path',
                'transactions.category_id',
                'categories.name as category_name',
                'categories.type as transaction_type',
            ])
            ->selectRaw("? + SUM($signedAmount) OVER (ORDER BY transactions.transaction_date ASC, transactions.id ASC) AS running_balance", [$period['opening_balance']]);

        return DB::query()
            ->fromSub($rankedTransactions, 'transaction_rows')
            ->select('transaction_rows.*')
            ->orderBy('transaction_date')
            ->orderBy('id');
    }

    public function latestTransactions(int $limit = 5): Collection
    {
        return Transaction::query()
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function publicIncomeTransactions(int $month, int $year): Collection
    {
        $period = CarbonImmutable::create($year, $month)->startOfMonth();

        return $this->transactionsByTypeForPeriod($period, CategoryType::Income)
            ->with('category:id,name')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();
    }

    private function transactionsByTypeForPeriod(CarbonImmutable $period, CategoryType $type): EloquentBuilder
    {
        return Transaction::query()
            ->whereBetween('transaction_date', [$period->toDateString(), $period->endOfMonth()->toDateString()])
            ->whereHas('category', fn (EloquentBuilder $query) => $query->where('type', $type->value));
    }

    private function openingBalanceFor(CarbonImmutable $period): int
    {
        $configuredBalance = OpeningBalance::query()
            ->where('period_year', $period->year)
            ->where('period_month', $period->month)
            ->value('opening_balance');

        if ($configuredBalance !== null) {
            return (int) $configuredBalance;
        }

        $previousPeriod = $period->subMonthNoOverflow()->startOfMonth();
        $firstFinancialPeriod = $this->firstFinancialPeriod();

        if ($firstFinancialPeriod === null || $previousPeriod->lessThan($firstFinancialPeriod)) {
            return 0;
        }

        return $this->summary($previousPeriod->month, $previousPeriod->year)['closing_balance'];
    }

    private function firstFinancialPeriod(): ?CarbonImmutable
    {
        if ($this->firstFinancialPeriod !== null) {
            return $this->firstFinancialPeriod;
        }

        $oldestTransaction = Transaction::query()->min('transaction_date');
        $oldestOpeningBalance = OpeningBalance::query()
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->first(['period_year', 'period_month']);

        $periods = array_filter([
            $oldestTransaction ? CarbonImmutable::parse($oldestTransaction)->startOfMonth() : null,
            $oldestOpeningBalance
                ? CarbonImmutable::create($oldestOpeningBalance->period_year, $oldestOpeningBalance->period_month)->startOfMonth()
                : null,
        ]);

        if ($periods === []) {
            return null;
        }

        usort($periods, fn (CarbonImmutable $left, CarbonImmutable $right): int => $left <=> $right);

        return $this->firstFinancialPeriod = $periods[0];
    }
}

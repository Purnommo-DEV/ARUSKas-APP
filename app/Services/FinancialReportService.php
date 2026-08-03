<?php

namespace App\Services;

use App\Enums\CategoryType;
use App\Models\Setting;
use App\Models\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    public function summary(int $month, int $year): array
    {
        $start = CarbonImmutable::create($year, $month)->startOfMonth();
        $end = $start->endOfMonth();
        $signedAmount = "CASE WHEN categories.type = '".CategoryType::Income->value."' THEN transactions.amount ELSE -transactions.amount END";

        $openingBalance = (int) Setting::current()->opening_balance + (int) Transaction::query()
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->whereDate('transaction_date', '<', $start)
            ->selectRaw("COALESCE(SUM($signedAmount), 0) AS balance")
            ->value('balance');

        $periodTotals = Transaction::query()
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN categories.type = ? THEN transactions.amount ELSE 0 END), 0) AS income,
                 COALESCE(SUM(CASE WHEN categories.type = ? THEN transactions.amount ELSE 0 END), 0) AS expense',
                [CategoryType::Income->value, CategoryType::Expense->value],
            )
            ->first();

        $cashIn = (int) $periodTotals->income;
        $cashOut = (int) $periodTotals->expense;

        return [
            'month' => $month,
            'year' => $year,
            'period_label' => $start->locale('id')->translatedFormat('F Y'),
            'opening_balance' => $openingBalance,
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'closing_balance' => (int) $openingBalance + $cashIn - $cashOut,
        ];
    }

    public function availableYears(): array
    {
        $dateBounds = Transaction::query()
            ->selectRaw('MIN(transaction_date) AS oldest, MAX(transaction_date) AS newest')
            ->first();
        $currentYear = now()->year;
        $firstYear = $dateBounds->oldest
            ? min((int) substr((string) $dateBounds->oldest, 0, 4), $currentYear)
            : $currentYear;
        $lastYear = $dateBounds->newest
            ? max((int) substr((string) $dateBounds->newest, 0, 4), $currentYear)
            : $currentYear;

        return range($lastYear, $firstYear);
    }

    public function transactionTableQuery(?int $month = null, ?int $year = null): Builder
    {
        $openingBalance = (int) Setting::current()->opening_balance;
        $rankedTransactions = Transaction::query()
            ->join('categories', 'categories.id', '=', 'transactions.category_id');

        if ($month && $year) {
            $period = $this->summary($month, $year);
            $openingBalance = $period['opening_balance'];
            $start = CarbonImmutable::create($year, $month)->startOfMonth();
            $rankedTransactions->whereBetween('transactions.transaction_date', [
                $start->toDateString(),
                $start->endOfMonth()->toDateString(),
            ]);
        }

        $signedAmount = "CASE WHEN categories.type = '".CategoryType::Income->value."' THEN transactions.amount ELSE -transactions.amount END";

        $rankedTransactions->select([
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
        ])->selectRaw("? + SUM($signedAmount) OVER (ORDER BY transactions.transaction_date ASC, transactions.id ASC) AS running_balance", [$openingBalance]);

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
}

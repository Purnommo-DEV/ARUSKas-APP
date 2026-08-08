<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Services\FinancialReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicReportController extends Controller
{
    public function __construct(private readonly FinancialReportService $financialReportService) {}

    public function index(): View
    {
        $month = now()->month;
        $year = now()->year;

        return view('public.report', [
            'setting' => Setting::current(),
            'summary' => $this->financialReportService->summary($month, $year),
            'incomeTransactions' => $this->financialReportService->publicIncomeTransactions($month, $year),
            'years' => $this->financialReportService->availableYears(),
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);
        $incomeTransactions = $this->financialReportService->publicIncomeTransactions($month, $year);

        return response()->json([
            'data' => [
                ...$this->financialReportService->summary($month, $year),
                'income_transactions' => $incomeTransactions
                    ->map(fn (Transaction $transaction): array => $this->incomePayload($transaction))
                    ->values(),
                'income_total' => (int) $incomeTransactions->sum('amount'),
            ],
        ]);
    }

    private function incomePayload(Transaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'transaction_date' => $transaction->transaction_date->toDateString(),
            'category_name' => $transaction->category->name,
            'payment_method' => $transaction->payment_method->label(),
            'notes' => $transaction->notes,
            'amount' => $transaction->amount,
            'proof_url' => $transaction->proof_path ? asset('storage/'.$transaction->proof_path) : null,
        ];
    }
}

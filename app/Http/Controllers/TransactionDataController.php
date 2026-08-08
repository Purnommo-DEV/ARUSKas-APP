<?php

namespace App\Http\Controllers;

use App\Services\FinancialReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionDataController extends Controller
{
    public function __construct(private readonly FinancialReportService $financialReportService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);
        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);
        $summary = $this->financialReportService->summary($month, $year);

        return DataTables::query($this->financialReportService->transactionTableQuery($month, $year))
            ->addColumn('proof_url', fn (object $transaction): ?string => $transaction->proof_path
                ? asset('storage/'.$transaction->proof_path)
                : null)
            ->with('opening_balance', $summary['opening_balance'])
            ->toJson();
    }
}

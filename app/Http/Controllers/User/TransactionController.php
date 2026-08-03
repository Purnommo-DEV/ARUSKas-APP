<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\FinancialReportService;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(private readonly FinancialReportService $financialReportService) {}

    public function index(): View
    {
        $month = now()->month;
        $year = now()->year;

        return view('reports.index', [
            'summary' => $this->financialReportService->summary($month, $year),
            'years' => $this->financialReportService->availableYears(),
            'readOnly' => true,
            'dataUrl' => route('user.transactions.data'),
            'summaryUrl' => route('user.summary'),
            'layout' => 'layouts.user',
            'title' => 'Laporan Keuangan',
        ]);
    }
}

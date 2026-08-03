<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinancialReportService;
use Illuminate\View\View;

class ReportController extends Controller
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
            'dataUrl' => route('admin.transactions.data'),
            'summaryUrl' => route('admin.summary'),
            'layout' => 'layouts.admin',
            'title' => 'Laporan Keuangan',
        ]);
    }
}

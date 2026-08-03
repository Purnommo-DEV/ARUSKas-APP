<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinancialReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly FinancialReportService $financialReportService) {}

    public function __invoke(): View
    {
        $month = now()->month;
        $year = now()->year;

        return view('admin.dashboard', [
            'summary' => $this->financialReportService->summary($month, $year),
            'latestTransactions' => $this->financialReportService->latestTransactions(),
        ]);
    }
}

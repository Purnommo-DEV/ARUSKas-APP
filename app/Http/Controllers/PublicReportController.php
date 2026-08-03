<?php

namespace App\Http\Controllers;

use App\Models\Setting;
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
            'years' => $this->financialReportService->availableYears(),
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        return response()->json([
            'data' => $this->financialReportService->summary(
                (int) ($validated['month'] ?? now()->month),
                (int) ($validated['year'] ?? now()->year),
            ),
        ]);
    }
}

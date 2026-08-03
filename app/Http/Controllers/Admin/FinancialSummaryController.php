<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportPeriodRequest;
use App\Services\FinancialReportService;
use Illuminate\Http\JsonResponse;

class FinancialSummaryController extends Controller
{
    public function __construct(private readonly FinancialReportService $financialReportService) {}

    public function __invoke(ReportPeriodRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return response()->json([
            'data' => $this->financialReportService->summary(
                (int) ($validated['month'] ?? now()->month),
                (int) ($validated['year'] ?? now()->year),
            ),
        ]);
    }
}

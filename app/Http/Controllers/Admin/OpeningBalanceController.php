<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpeningBalanceRequest;
use App\Models\OpeningBalance;
use App\Services\OpeningBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class OpeningBalanceController extends Controller
{
    public function __construct(private readonly OpeningBalanceService $openingBalanceService) {}

    public function index(): View
    {
        return view('admin.opening-balances.index');
    }

    public function data(): JsonResponse
    {
        return DataTables::eloquent(OpeningBalance::query()->with('creator'))
            ->addColumn('period_label', fn (OpeningBalance $openingBalance): string => $this->periodLabel($openingBalance))
            ->addColumn('creator_name', fn (OpeningBalance $openingBalance): string => $openingBalance->creator?->name ?? 'Migrasi sistem')
            ->orderColumn('period_label', function ($query, string $order): void {
                $query->orderBy('period_year', $order)->orderBy('period_month', $order);
            })
            ->toJson();
    }

    public function store(OpeningBalanceRequest $request): JsonResponse
    {
        $openingBalance = $this->openingBalanceService->create($request->validated(), $request->user()->id);

        return response()->json([
            'message' => 'Kas Awal berhasil ditambahkan.',
            'data' => $this->payload($openingBalance),
        ], 201);
    }

    public function show(OpeningBalance $openingBalance): JsonResponse
    {
        return response()->json(['data' => $this->payload($openingBalance)]);
    }

    public function update(OpeningBalanceRequest $request, OpeningBalance $openingBalance): JsonResponse
    {
        $openingBalance = $this->openingBalanceService->update($openingBalance, $request->validated());

        return response()->json([
            'message' => 'Kas Awal berhasil diperbarui.',
            'data' => $this->payload($openingBalance),
        ]);
    }

    public function destroy(OpeningBalance $openingBalance): JsonResponse
    {
        $this->openingBalanceService->delete($openingBalance);

        return response()->json(['message' => 'Kas Awal berhasil dihapus.']);
    }

    private function payload(OpeningBalance $openingBalance): array
    {
        return [
            'id' => $openingBalance->id,
            'period_year' => $openingBalance->period_year,
            'period_month' => $openingBalance->period_month,
            'period_label' => $this->periodLabel($openingBalance),
            'opening_balance' => $openingBalance->opening_balance,
            'notes' => $openingBalance->notes,
        ];
    }

    private function periodLabel(OpeningBalance $openingBalance): string
    {
        return now()
            ->setDate($openingBalance->period_year, $openingBalance->period_month, 1)
            ->locale('id')
            ->translatedFormat('F Y');
    }
}

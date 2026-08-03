<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\FinancialReportService;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
        private readonly FinancialReportService $financialReportService,
    ) {}

    public function index(): View
    {
        return view('admin.transactions.index', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::cases(),
            'years' => $this->financialReportService->availableYears(),
        ]);
    }

    public function store(TransactionRequest $request): JsonResponse
    {
        $this->transactionService->create($request->validated(), $request->user()->id);

        return response()->json(['message' => 'Transaksi berhasil ditambahkan.'], 201);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->loadMissing('category');
        $weekEnd = $transaction->week_start->copy()->addDays(6);

        return response()->json([
            'data' => [
                'id' => $transaction->id,
                'transaction_date' => $transaction->transaction_date->toDateString(),
                'week_period' => $transaction->week_start->format('d/m/Y').' - '.$weekEnd->format('d/m/Y'),
                'payment_method' => $transaction->payment_method->value,
                'category_id' => $transaction->category_id,
                'category_name' => $transaction->category->name,
                'category_type' => $transaction->category->type->value,
                'party_name' => $transaction->party_name,
                'amount' => $transaction->amount,
                'notes' => $transaction->notes,
                'proof_url' => $transaction->proof_path ? asset('storage/'.$transaction->proof_path) : null,
            ],
        ]);
    }

    public function update(TransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $this->transactionService->update($transaction, $request->validated());

        return response()->json(['message' => 'Transaksi berhasil diperbarui.']);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $this->transactionService->delete($transaction);

        return response()->json(['message' => 'Transaksi berhasil dihapus.']);
    }
}

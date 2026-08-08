<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('transactions.manage') ?? false;
    }

    public function rules(): array
    {
        $transaction = $this->route('transaction');

        return [
            'transaction_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(
                    fn (Builder $query) => $query
                        ->where('is_active', true)
                        ->when($transaction, fn (Builder $query) => $query->orWhere('id', $transaction->category_id)),
                ),
            ],
            'party_name' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'integer', 'min:1', 'max:9000000000000000000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'remove_proof' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'transaction_date' => 'tanggal',
            'payment_method' => 'metode transaksi',
            'category_id' => 'kategori',
            'party_name' => 'keterangan',
            'amount' => 'nominal',
            'notes' => 'catatan',
            'proof' => 'bukti transaksi',
        ];
    }
}

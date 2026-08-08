<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OpeningBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $openingBalance = $this->route('opening_balance');

        return [
            'period_year' => [
                'required',
                'integer',
                'between:2000,2100',
                Rule::unique('opening_balances')->where(
                    fn (Builder $query) => $query->where('period_month', $this->integer('period_month')),
                )->ignore($openingBalance),
            ],
            'period_month' => ['required', 'integer', 'between:1,12'],
            'opening_balance' => ['required', 'integer', 'min:0', 'max:9000000000000000000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'period_year' => 'tahun periode',
            'period_month' => 'bulan periode',
            'opening_balance' => 'kas awal',
            'notes' => 'catatan',
        ];
    }
}

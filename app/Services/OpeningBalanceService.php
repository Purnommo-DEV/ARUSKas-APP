<?php

namespace App\Services;

use App\Models\OpeningBalance;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OpeningBalanceService
{
    public function create(array $data, int $createdBy): OpeningBalance
    {
        try {
            return DB::transaction(fn (): OpeningBalance => OpeningBalance::query()->create([
                ...$data,
                'created_by' => $createdBy,
            ]));
        } catch (QueryException $exception) {
            $this->throwIfDuplicatePeriod($exception);

            throw $exception;
        }
    }

    public function update(OpeningBalance $openingBalance, array $data): OpeningBalance
    {
        try {
            return DB::transaction(function () use ($openingBalance, $data): OpeningBalance {
                $openingBalance->update($data);

                return $openingBalance->refresh();
            });
        } catch (QueryException $exception) {
            $this->throwIfDuplicatePeriod($exception);

            throw $exception;
        }
    }

    public function delete(OpeningBalance $openingBalance): void
    {
        DB::transaction(fn () => $openingBalance->delete());
    }

    private function throwIfDuplicatePeriod(QueryException $exception): void
    {
        if (str_contains(strtolower($exception->getMessage()), 'unique')) {
            throw ValidationException::withMessages([
                'period_year' => 'Kas Awal untuk periode tersebut sudah tersedia.',
            ]);
        }
    }
}

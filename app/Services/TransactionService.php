<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactionService
{
    public function __construct(private readonly TransactionProofService $proofService) {}

    public function create(array $data, int $creatorId): Transaction
    {
        $proof = $this->extractProof($data);
        $proofPath = null;

        try {
            if ($proof) {
                $proofPath = $this->proofService->store($proof);
            }

            return DB::transaction(function () use ($data, $creatorId, $proofPath): Transaction {
                $data['week_start'] = Carbon::parse($data['transaction_date'])->startOfWeek(Carbon::MONDAY)->toDateString();
                $data['created_by'] = $creatorId;
                $data['proof_path'] = $proofPath;

                return Transaction::query()->create($data)->load(['category', 'creator']);
            });
        } catch (Throwable $exception) {
            $this->proofService->delete($proofPath);
            throw $exception;
        }
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $proof = $this->extractProof($data);
        $oldProofPath = $transaction->proof_path;
        $newProofPath = null;
        $removeProof = (bool) ($data['remove_proof'] ?? false);
        unset($data['remove_proof']);

        try {
            if ($proof) {
                $newProofPath = $this->proofService->store($proof);
            }

            $transaction = DB::transaction(function () use ($transaction, $data, $newProofPath, $removeProof): Transaction {
                $data['week_start'] = Carbon::parse($data['transaction_date'])->startOfWeek(Carbon::MONDAY)->toDateString();

                if ($newProofPath) {
                    $data['proof_path'] = $newProofPath;
                } elseif ($removeProof) {
                    $data['proof_path'] = null;
                }

                $transaction->update($data);

                return $transaction->refresh()->load(['category', 'creator']);
            });
        } catch (Throwable $exception) {
            $this->proofService->delete($newProofPath);
            throw $exception;
        }

        if ($newProofPath || $removeProof) {
            $this->proofService->delete($oldProofPath);
        }

        return $transaction;
    }

    public function delete(Transaction $transaction): void
    {
        $proofPath = $transaction->proof_path;
        DB::transaction(fn () => $transaction->delete());
        $this->proofService->delete($proofPath);
    }

    private function extractProof(array &$data): ?UploadedFile
    {
        $proof = $data['proof'] ?? null;
        unset($data['proof']);

        return $proof instanceof UploadedFile ? $proof : null;
    }
}

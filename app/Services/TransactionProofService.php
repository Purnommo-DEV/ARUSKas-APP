<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class TransactionProofService
{
    public function store(UploadedFile $file): string
    {
        $image = (new ImageManager(new Driver))
            ->read($file->getRealPath())
            ->scaleDown(width: 1920);

        $path = 'transactions/proofs/'.Str::uuid().'.webp';
        Storage::disk('public')->put($path, (string) $image->toWebp(82));

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}

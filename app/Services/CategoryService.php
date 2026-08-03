<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function create(array $data): Category
    {
        return DB::transaction(fn () => Category::query()->create($data));
    }

    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data): Category {
            $category->update($data);

            return $category->refresh();
        });
    }

    public function toggle(Category $category): Category
    {
        return DB::transaction(function () use ($category): Category {
            $category->update(['is_active' => ! $category->is_active]);

            return $category->refresh();
        });
    }

    public function delete(Category $category): void
    {
        if ($category->transactions()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Kategori yang sudah dipakai transaksi tidak dapat dihapus. Nonaktifkan kategori sebagai gantinya.',
            ]);
        }

        DB::transaction(fn () => $category->delete());
    }
}

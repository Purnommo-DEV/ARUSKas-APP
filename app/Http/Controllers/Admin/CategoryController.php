<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService) {}

    public function index(): View
    {
        return view('admin.categories.index');
    }

    public function data(): JsonResponse
    {
        return DataTables::eloquent(Category::query()->select('categories.*'))
            ->editColumn('type', fn (Category $category) => $category->type->value)
            ->toJson();
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type->value,
                'type_label' => $category->type->label(),
            ],
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type->value,
                'is_active' => $category->is_active,
            ],
        ]);
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $this->categoryService->update($category, $request->validated());

        return response()->json(['message' => 'Kategori berhasil diperbarui.']);
    }

    public function toggle(Category $category): JsonResponse
    {
        $category = $this->categoryService->toggle($category);

        return response()->json([
            'message' => $category->is_active ? 'Kategori berhasil diaktifkan.' : 'Kategori berhasil dinonaktifkan.',
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }
}

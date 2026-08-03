<?php

namespace App\Http\Requests;

use App\Enums\CategoryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('categories.manage') ?? false;
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')->where(fn ($query) => $query->where('type', $this->input('type')))
                    ->ignore($category?->id),
            ],
            'type' => ['required', Rule::enum(CategoryType::class)],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama kategori',
            'type' => 'jenis',
            'is_active' => 'status aktif',
        ];
    }
}

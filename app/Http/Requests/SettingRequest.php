<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'study_name' => ['required', 'string', 'max:150'],
            'mosque_name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:1000'],
            'confirmation_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'thanks_message' => ['nullable', 'string', 'max:120'],
            'blessing_message' => ['nullable', 'string'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'study_name' => 'nama kajian',
            'mosque_name' => 'nama masjid',
            'address' => 'alamat',
            'confirmation_phone' => 'nomor konfirmasi',
            'thanks_message' => 'ucapan terima kasih',
            'blessing_message' => 'ucapan penutup',
            'qris_image' => 'gambar QRIS',
            'logo' => 'logo kajian',
        ];
    }
}

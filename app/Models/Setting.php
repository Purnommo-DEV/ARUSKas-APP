<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'study_name',
        'mosque_name',
        'address',
        'confirmation_phone',
        'thanks_message',
        'blessing_message',
        'opening_balance',
        'opening_balance_set',
        'qris_image_path',
        'logo_path',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate(['id' => 1], [
            'study_name' => 'Kajian Kita',
            'mosque_name' => 'Masjid Kita',
        ]);
    }

    public function getWhatsappNumberAttribute(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $this->confirmation_phone);

        if ($number === '') {
            return null;
        }

        return str_starts_with($number, '0')
            ? '62'.substr($number, 1)
            : $number;
    }

    protected function casts(): array
    {
        return [
            'opening_balance' => 'integer',
            'opening_balance_set' => 'boolean',
        ];
    }
}

<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Transfer = 'transfer';
    case Qris = 'qris';
    case Cash = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::Transfer => 'Transfer',
            self::Qris => 'QRIS',
            self::Cash => 'Cash',
        };
    }
}

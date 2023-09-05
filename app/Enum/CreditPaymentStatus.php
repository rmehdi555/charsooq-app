<?php

namespace App\Enum;

enum CreditPaymentStatus: string
{
    case Preinitiated = 'در انتظار پرداخت';
    case Initiated = 'پرداخت شده';
    case Succeeded = 'موفق';
    case Failed = 'رد شده';

    public static function fromName(string $name)
    {
        return constant("self::$name");
    }
}

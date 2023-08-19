<?php

namespace App\Enum;

enum UserGender: int
{
    case unknown = 1;
    case male = 2;
    case female = 3;

    public static function fromName(string $name)
    {
        return constant("self::$name");
    }
}

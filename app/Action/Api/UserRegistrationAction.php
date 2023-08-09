<?php

namespace App\Action\Api;


class UserRegistrationAction
{
    public static function cleanApiDataRegisterForm($data): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'nationalcode' => $data['nationalcode'],
        ];
    }
}

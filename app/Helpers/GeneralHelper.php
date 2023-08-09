<?php

if (!function_exists('generateRandomNumber')) {
    function generateRandomNumber($length = 4): int
    {
        $intMin = (10 ** $length) / 10;
        $intMax = (10 ** $length) - 1;

        return mt_rand($intMin, $intMax);
    }
}

if (!function_exists('checkPhoneNumber')) {
    function checkPhoneNumber($cell_number): string
    {
        $cell_number = convertToEnglishDigit($cell_number);
        return str_replace('+98', '0', $cell_number);
    }
}

if (!function_exists('getCompleteIpAddr')) {
    function getCompleteIpAddr(): string
    {
        $ip = '';
        if (!empty($_SERVER['HTTP_AR_REAL_IP'])) {
            $ip .= 'HTTP_AR_REAL_IP => ' . $_SERVER['HTTP_AR_REAL_IP'] . ' | ';
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip .= 'HTTP_X_FORWARDED_FOR => ' . $_SERVER['HTTP_X_FORWARDED_FOR'] . ' | ';
        }

        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip .= 'HTTP_X_REAL_IP => ' . $_SERVER['HTTP_X_REAL_IP'] . ' | ';
        }

        if (!empty($_SERVER['SERVER_ADDR'])) {
            $ip = 'SERVER_ADDR => ' . $_SERVER['SERVER_ADDR'] . ' | ';
        }

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip .= 'REMOTE_ADDR => ' . $_SERVER['REMOTE_ADDR'] . ' | ';
        }

        $ip = rtrim($ip, ' | ');
        return $ip;
    }
}

if (!function_exists('generateTransferCode')) {
    function generateTransferCode($length = 15): int
    {
        $intMin = (10 ** $length) / 10;
        $intMax = (10 ** $length) - 1;

        return mt_rand($intMin, $intMax);
    }
}

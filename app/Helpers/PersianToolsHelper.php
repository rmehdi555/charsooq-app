<?php

use Hekmatinasser\Verta\Verta;

if (!function_exists('convertToPersianDigit')) {
    function convertToPersianDigit($number): array|string
    {
        $en = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $fa = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹");
        return str_replace($en, $fa, $number);
    }
}

if (!function_exists('convertToEnglishDigit')) {
    function convertToEnglishDigit($number): string
    {
        $fa = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹");
        $ar = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
        $en = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $convertedPersianNums = str_replace($fa, $en, $number);
        return str_replace($ar, $en, $convertedPersianNums);
    }
}

if (!function_exists('todayPersianDate')) {
    function todayPersianDate(): string
    {
        return Verta::instance()->format('H:i - %d %B %Y');
    }
}

if (!function_exists('todayPersianDate')) {
    function todayPersianDate(): string
    {
        return Verta::instance()->format('H:i - %d %B %Y');
    }
}

if (!function_exists('weekDay')) {
    function weekDay($dayNumber): string
    {
        $weekDay = [
            0 => 'شنبه',
            1 => 'یکشنبه',
            2 => 'دوشنبه',
            3 => 'سه شنبه',
            4 => 'چهارشنبه',
            5 => 'پنج شنبه',
            6 => 'جمعه',
        ];
        return $weekDay[$dayNumber];
    }
}

if (!function_exists('convertToPersianDate')) {
    function convertToPersianDate(string $date): Verta
    {
        return Verta::instance($date);
    }
}


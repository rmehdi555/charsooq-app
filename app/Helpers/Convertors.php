<?php

namespace App\Helpers;

class Convertors
{
    public static function weightConverter($weighttype, $weightvalue): float|int|string
    {
        $returnee = "";
        switch ($weighttype) {
            case 'گرم':
                $returnee = $weightvalue * 1;
                break;
            case 'کیلوگرم':
                $returnee = $weightvalue * 1000;
                break;
            case 'پوند':
                $returnee = $weightvalue * 454;
                break;
            case 'انس':
                $returnee = $weightvalue * 28;
                break;
        }
        return $returnee;
    }

    public static function paymentSignByPaymentType($amount, $payment_method): int
    {
        $result = 0;
        switch ($payment_method) {
            case 'پرداخت ثانویه':
            case 'پرداخت اولیه':
            case 'پرداخت از کیف پول':
                $result = $amount;
                break;
            case 'ارسال':
            case 'عودت وجه':
            case 'جریمه':
                $result = $amount * -1;
                break;
            default:
                break;
        }
        return $result;
    }

    public static function extractWeightUnit($weightString)
    {

        $unit = 'notfound';

        if (strpos($weightString, 'pound') or strpos($weightString, 'Pound'))
            $unit = 'پوند';
        elseif (strpos($weightString, 'ounce') or strpos($weightString, 'Ounce'))
            $unit = 'انس';
        elseif (strpos($weightString, 'Kg') or strpos($weightString, 'Kilogram') or strpos($weightString, 'kg'))
            $unit = 'کیلوگرم';
        elseif (strpos($weightString, 'grams') or strpos($weightString, 'Grams') or strpos($weightString, 'gr') or strpos($weightString, 'g'))
            $unit = 'گرم';

        return $unit;
    }
    public static function datetocode()
    {
        $dts = date('Y-m-d H:i:s');
        $num = rand(0, 999);
        $dts = str_replace(str_split(' -:'), '', $dts) + $num;

        return $dts;
    }
}

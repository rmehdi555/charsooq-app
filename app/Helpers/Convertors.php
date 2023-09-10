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

    public static function invoiceCaseUser($invoice)
    {
//        $invoice=(array)$invoice;
//        dd($invoice);
        foreach ($invoice as $data)
            {
                $status=$data['status'];
                $orderLevel=$data['orderlevel'];
                switch (true) {
                    case ($status == 'در حال بررسی' and $orderLevel == 'درخواست'):
                        $userShowStatus = "در حال بررسی";
                        break;
                    case ($status == 'پیش فاکتور ارسال شد در انتظار پرداخت' and $orderLevel == 'فاکتور'):
                        $userShowStatus = "پیش فاکتور در انتظار پرداخت";
                        break;
                    case ($status == 'در حال خرید' and $orderLevel == 'سفارش'):
                        $userShowStatus = "در حال خرید";
                        break;
                    case ($status == 'خریداری شده' and $orderLevel == 'سفارش'):
                    case ($status == 'تحویل دفتر آمریکا' and $orderLevel == 'سفارش'):
                    case ($status == 'ارسال به دوبی' and $orderLevel == 'سفارش'):
                    case ($status == 'تحویل دفتر دوبی' and $orderLevel == 'سفارش'):
                    case ($status == 'ارسال به ایران' and $orderLevel == 'سفارش'):
                    case ($status == 'تحویل دفتر ایران' and $orderLevel == 'سفارش'):
                        $userShowStatus = "ارسال به ایران";
                        break;
                    case ($status == 'تحویل دفتر ایران' and $orderLevel == 'آماده برای پرداخت'):
                        $userShowStatus = "فاکتور نهایی در انتظار پرداخت";
                        break;
                    case ($status == 'تحویل دفتر ایران' and $orderLevel == 'پرداخت شده'):
                        $userShowStatus = "در حال ارسال به مشتری";
                        break;
                    case ($status == 'تحویل مشتری' and $orderLevel == 'تحویل مشتری'):
                        $userShowStatus = "تحویل مشتری";
                        break;
                    default:
                        $userShowStatus = 'نامشخص';
                        break;
                }
                $data['user_show_status']=$userShowStatus;
                unset($data['status']);
                unset($data['orderlevel']);
            }
        return $invoice;
    }
}

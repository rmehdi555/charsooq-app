<?php

if (!function_exists('invoiceStatusUserPanel')) {
    function invoiceStatusUserPanel($status, $orderLevel): string
    {
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
        return $userShowStatus;
    }
}

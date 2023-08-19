<?php


namespace App\Classes;


use App\Models\Invoice;
use Illuminate\Support\Carbon;

class InvoiceCode
{

    public static function generateCode()
    {
        $year     = substr(Carbon::now()->format('Y'), 2, 4);
        $month    = Carbon::now()->format('m');
        $day      = Carbon::now()->format('d');
        $ymd      = $year . $month . $day;
        $lastCode = Invoice::orderBy('id', 'desc')->first();
        if($lastCode)
            $lastCode = $lastCode->code;
        else
            $lastCode = 000;

        $counter = self::CounterCalculator($ymd, $lastCode);
        return $counter;
    }

    private static function CounterCalculator($ymd, $lastCode)
    {
        $res = '';
        if (strpos($lastCode, $ymd) !== false)
        {
            $datePart = substr($lastCode, 0, 6);
            $counterPart = str_replace($datePart, '', $lastCode);
            if ($counterPart == self::LastDigitGenerator($counterPart)) {
                $output = '';
                for ($i = 0; $i <= strlen($counterPart); $i++) {
                    $output = $output . "0";
                }

                $res = $ymd . $output;
            } else {
                $res = (string)($lastCode + 1);
            }

        } else {
            $res = $ymd . "000";
        }
        return $res;
    }

    private static function LastDigitGenerator($counterPart)
    {
        $output = "";
        for ($i = 0; $i < strlen($counterPart); $i++) {
            $output = $output . "9";
        }
        return $output;
    }
}

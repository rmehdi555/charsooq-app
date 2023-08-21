<?php

namespace App\Classes;

use App\Helpers\Convertors;
use App\Models\CalculatorConfig;
use App\Models\Exchanges;

class Calculator
{
    public static function singleProduct(float $price, string $massUnit, int $exchangeType, int $region, float $weight, int $count = 1): array
    {
        $exchangeValue          = Exchanges::find($exchangeType);
        $calculatorConfigs      = CalculatorConfig::find($region);
        $productTotalPrice      = floatval($price * $count);
        $profitPercentageBroker = $calculatorConfigs['BuyBroker'] * 0.01;
        $profitPercentageBroker = $exchangeType == '1' ? $profitPercentageBroker : $profitPercentageBroker + 0.03;
        $buyProfit              = ($productTotalPrice * $profitPercentageBroker) * $exchangeValue['value'];
        $buyCost                = $productTotalPrice * $exchangeValue['value'];
        $currency_ship_cost     = Exchanges::find($calculatorConfigs->IndexExchange);
        $shipCost               = $calculatorConfigs->AdditionalPerRow * $exchangeValue->value;
        $weighValue             = Convertors::weightConverter($massUnit, $weight) * $count;
        if (Convertors::weightConverter($massUnit, $weight) != 0)
        {
            $shipCost = (self::ExchangeCalculatorForShipping($weighValue, $calculatorConfigs) + $calculatorConfigs->AdditionalPerRow) * $currency_ship_cost->value;
        }
        return [
            'buyCost'          => round($buyCost),
            'shipBroker'       => round($shipCost),
            'buyProfit'        => round($buyProfit),
            'count'            => $count,
            'finalResult'      => round($buyCost + $buyProfit + $shipCost),
            'finalRialPrice'   => round($buyCost + $buyProfit + $shipCost),
            'bBroker'          => round($profitPercentageBroker),
            'eachKGValue'      => $calculatorConfigs['EachKgValue'],
            'minWeightVal'     => $calculatorConfigs['MinValueForWeight'],
            'additionalPerRow' => $calculatorConfigs['AdditionalPerRow'],
            'exchangeValue'    => $exchangeValue['value']
        ];
    }

    public static function ExchangeCalculatorForShipping($val1, $val2)
    {
        return (($val2['EachKgValue']) * $val1 / 1000) < $val2['MinValueForWeight'] ? $val2['MinValueForWeight'] : ($val2['EachKgValue']) * $val1 / 1000;
    }
}

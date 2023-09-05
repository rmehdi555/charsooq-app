<?php

namespace App\Classes;

use App\Helpers\Convertors;
use App\Models\CalculatorConfig;
use App\Models\Exchanges;
use App\Models\InvoiceItem;

class Calculator
{
    public static function singleProduct(float $price, string $massUnit, int $exchangeType, int $region, float $weight, int $count = 1, ?int $exchangeValue = null): array
    {
        $exchangeValue = $exchangeValue ?? Exchanges::where('id', $exchangeType)->value('value');
        $calculatorConfigs = CalculatorConfig::find($region);
        $profitPercentageBroker = $calculatorConfigs['BuyBroker'] * 0.01;
        $weighValue = Convertors::weightConverter($massUnit, $weight) * $count;
        $additionalPerRow = $calculatorConfigs->AdditionalPerRow * $exchangeValue;

        $buyProfit = ($price * $profitPercentageBroker) * $exchangeValue * $count;
        $buyCost = $price * $exchangeValue * $count;
        $shipCost = (int)$weighValue < 170
            ? $calculatorConfigs['MinValueForWeight']
            : ($calculatorConfigs['EachKgValue'] * $weighValue) / 1000;
        $shipCost = ($shipCost * $exchangeValue);

        return [
            'buyCost' => round($buyCost),
            'shipBroker' => round($shipCost),
            'buyProfit' => round($buyProfit) + $additionalPerRow,
            'count' => $count,
            'finalResult' => round($buyCost + $buyProfit + $shipCost),
            'finalRialPrice' => round($buyCost + $buyProfit + $shipCost),
            'bBroker' => round($profitPercentageBroker),
            'eachKGValue' => $calculatorConfigs['EachKgValue'],
            'minWeightVal' => $calculatorConfigs['MinValueForWeight'],
            'additionalPerRow' => $calculatorConfigs['AdditionalPerRow'],
            'exchangeValue' => $exchangeValue
        ];
    }

    public static function reCalculateItemsBasedOnLogistic(int $invoiceId): void
    {
        $invoiceItems = InvoiceItem::where('invoice_id', $invoiceId)->withWhereHas('logistics', fn($q) => $q->where('logistics.iscancel', false))->get();

        foreach ($invoiceItems as $invoiceItem) {
            $weighValue = $invoiceItem->logistics->sum('finalweight') == 0 ? ($invoiceItem->firstweight * $invoiceItem->count) : $invoiceItem->logistics->sum('finalweight');
            $region = $invoiceItem->region_id;
            $calculatorConfigs = CalculatorConfig::find($region);
            $additionalPerRow = $calculatorConfigs->AdditionalPerRow;

            foreach ($invoiceItem->logistics as $logistic) {
                // recalculate transport price
                $shipCost = (int)$weighValue < 170 ? $calculatorConfigs['MinValueForWeight'] : ($calculatorConfigs['EachKgValue'] * $weighValue) / 1000;
                $shipCost = ($shipCost * $logistic->exchangevalue);
                $shipCost = $shipCost / $invoiceItem->logistics->count();
                // recalculate buy price
                $buyCost = $logistic->price * $logistic->exchangevalue;
                // recalculate buy profit
                $buyProfit = ($logistic->price * ($calculatorConfigs['BuyBroker'] * 0.01)) * $logistic->exchangevalue;
                $buyProfit = $buyProfit + (($additionalPerRow * $logistic->exchangevalue) / $invoiceItem->logistics->count());

                $logistic->update([
                    'transportprice' => $shipCost,
                    'itemprice' => $buyCost,
                    'brokerwageprice' => $buyProfit,
                    'singleItemfullprice' => $shipCost + $buyCost + $buyProfit,
                ]);
            }
        }
    }

    public static function reCalculateFinalItemsBasedOnLogistic(int $invoiceId): void
    {
        $invoiceItems = InvoiceItem::where('invoice_id', $invoiceId)->withWhereHas('logistics', fn($q) => $q->where('logistics.iscancel', false))->get();

        foreach ($invoiceItems as $invoiceItem) {
            $weighValue = $invoiceItem->logistics->sum('finalweight') == 0 ? ($invoiceItem->firstweight * $invoiceItem->count) : $invoiceItem->logistics->sum('finalweight');
            $region = $invoiceItem->region_id;
            $calculatorConfigs = CalculatorConfig::find($region);
            $additionalPerRow = $calculatorConfigs->AdditionalPerRow;

            foreach ($invoiceItem->logistics as $logistic) {
                // recalculate transport price
                $shipCost = (int)$weighValue < 170 ? $calculatorConfigs['MinValueForWeight'] : ($calculatorConfigs['EachKgValue'] * $weighValue) / 1000;
                $shipCost = ($shipCost * $logistic->exchangevalue);
                $shipCost = $shipCost / $invoiceItem->logistics->count();
                // recalculate buy price
                $buyCost = $logistic->price * $logistic->exchangevalue;
                // recalculate buy profit
                $buyProfit = ($logistic->price * ($calculatorConfigs['BuyBroker'] * 0.01)) * $logistic->exchangevalue;
                $buyProfit = $buyProfit + (($additionalPerRow * $logistic->exchangevalue) / $invoiceItem->logistics->count());

                $logistic->update([
                    'finalTransportprice' => $shipCost,
                    'itemprice' => $buyCost,
                    'brokerwageprice' => $buyProfit,
                    'singleItemfullprice' => $shipCost + $buyCost + $buyProfit,
                ]);
            }
        }
    }
}

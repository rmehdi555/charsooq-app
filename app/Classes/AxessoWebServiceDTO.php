<?php

namespace App\Classes;

use App\Helpers\Convertors;
use App\Models\Exchanges;
use App\Models\Region;

class AxessoWebServiceDTO
{
    public static function extractDetail($result, $link)
    {
        $RegionAndExchangeType = extractRegionAndExchangeType($link);

        $result['url'] = $link;
        $result['exchangeType'] = $RegionAndExchangeType['exchangeType'];
        $exchangeType = Exchanges::where('id', $result['exchangeType'])->first();
        $result['exchangeName'] = $exchangeType->name;
        $result['region'] = $RegionAndExchangeType['region'];
        $region = Region::where('id', $result['region'])->first();
        $result['regionName'] = $region->name;
        $result['productRating'] = explode(' ', $result['productRating'])[0];


        foreach ($result['productDetails'] as $value) {
            if ($value['name'] == 'Shipping Weight') {
                $result['ShippingWeight'] = floatval($value['value']);
                $result['massUnit'] = Convertors::extractWeightUnit($value['value']);
            }
            if ($value['name'] == 'Item Weight' or $value['name'] == 'Weight' or $value['name'] == 'Item Weight ') {
                $result['itemWeight'] = floatval($value['value']);
                $result['massUnit'] = Convertors::extractWeightUnit($value['value']);
            }
            if ($value['name'] == 'Color' or $value['name'] == 'color') {
                $result['color'] = $value['value'];
            }
            if ($value['name'] == 'Manufacturer') {
                if (!isset($result['manufacturer']) or is_null($result['manufacturer']))
                    $result['manufacturer'] = $value['value'];
            }
            if ($value['name'] == 'Product Dimensions' or $value['name'] = 'Package Dimensions') {
                if (!isset($result['itemWeight'])) {
                    $dimentions = explode(";", $value['value']);
                    foreach ($dimentions as $dimention) {
                        $massUnitDimensions = Convertors::extractWeightUnit($dimention);
                        if (strcmp($massUnitDimensions, "notfound") != 0) {
                            $weightDimensions = floatval($dimention);
                            $result['itemWeight'] = $weightDimensions;
                            $result['massUnit'] = $massUnitDimensions;
                            break;
                        }
                    }
                }
            }
        }

        if (!isset($result['color'])) $result['color'] = '';
        if (!isset($result['manufacturer'])) $result['manufacturer'] = '';
        if (isset($result['ShippingWeight'])) {
            $result['finalWeight'] = max($result['itemWeight'], $result['ShippingWeight']);
        } else if (isset($result['itemWeight'])) {
            $result['finalWeight'] = $result['itemWeight'];
        } else if (isset($result['Item Weight'])) {
            $result['finalWeight'] = $result['Item Weight'];
        } else {
            $result['finalWeight'] = 0;
            $result['massUnit'] = 'گرم';
        }
        if (empty($result['price']) or $result['price'] == 0) {
            if (isset($result['dealPrice']) and $result['dealPrice'] != 0)
                $result['price'] = $result['dealPrice'];
            elseif (isset($result['retailPrice']))
                $result['price'] = $result['retailPrice'];
        }

        foreach ($result['reviews'] as $key => $item) {
            if (strlen($item['text']) > 800)
                unset($result['reviews'][$key]);
        }

        return $result;
    }


}

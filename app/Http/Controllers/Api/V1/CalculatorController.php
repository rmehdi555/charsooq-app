<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\Calculator;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Calculator\CalculatorRequest;
use App\Http\Resources\ExchangeResource;
use App\Http\Resources\RegionResource;
use App\Http\Resources\WeightResource;
use App\Models\Exchanges;
use App\Models\Region;
use App\Models\Weight;

class CalculatorController extends Controller
{
    public function index()
    {
        $regions = Region::all();
        $regions = RegionResource::collection($regions);
        $exchanges = Exchanges::all();
        $exchanges = ExchangeResource::collection($exchanges);
        $weight = Weight::all();
        $weight = WeightResource::collection($weight);
        return $this->successResponse([
            'regions' => $regions,
            'exchanges' => $exchanges,
            'weight' => $weight,
        ]);
    }

    public function show(CalculatorRequest $request)
    {
        $priceRial = Calculator::singleProduct(
            $request->price,
            $request->weight_unit,
            $request->exchange_id,
            $request->region_id,
            $request->weight
        );
        return $this->successResponse([
            'priceRial' => $priceRial,
        ]);
    }
}

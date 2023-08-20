<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\Calculator;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Calculator\CalculatorRequest;
use App\Models\Exchanges;
use App\Models\Region;
use App\Models\Weight;

class CalculatorController extends Controller
{
    public function index()
    {
        $regions = Region::all();
        $exchanges = Exchanges::all();
        $weight = Weight::all();
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
            $request->weight_title,
            $request->exchange_id,
            $request->region_id,
            $request->weight
        );
        return $this->successResponse([
            'priceRial' => $priceRial,
        ]);
    }
}

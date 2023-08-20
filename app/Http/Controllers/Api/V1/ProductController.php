<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\Calculator;
use App\Http\Controllers\Controller;
use App\Models\CrawlerProduct;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function show($asin): JsonResponse
    {
        $product = CrawlerProduct::where('asin', $asin)->first();
        if (!filled($product))
            return $this->errorResponse(__('messages.item_not_found'), 404);
        $product->setHidden(['response']);
        $priceRial = Calculator::singleProduct(
            $product['price'],
            $product['weight_unit'],
            $product['exchange_type'],
            $product['region_type'],
            $product['weight']
        );

        return $this->successResponse([
            'data' => ['details' => $product, 'financial' => $priceRial],
        ], __('messages.item_found_success'));
    }
}

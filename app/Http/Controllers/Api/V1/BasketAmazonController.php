<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\InvoiceItemDetails;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\Calculator;

class BasketAmazonController extends Controller
{
    public function link(Request $request): JsonResponse
    {
        $domain = stripos($request->url, 'www.amazon');
        $data = InvoiceItemDetails::extractDetail($request->url);
        if (isset($data['status']) && $data['status'] == 'error')
            return $this->errorResponse(__('messages.url_entered_invalid'));


        if ($domain > 0 && $data['region'] == 1) {
            $data['count'] = 1;
            $priceRial = Calculator::singleProduct(
                $data['price'],
                $data['massUnit'],
                $data['exchangeType'],
                $data['region'],
                $data['finalWeight'],
                $data['count']);
            $data['productRating'] = str_split($data['productRating']);

            return $this->successResponse([
                'data' => $data,
                'priceRial' => $priceRial['finalResult'],
            ], '');
        }

        return $this->errorResponse(__('messages.url_entered_invalid_amazon'));

    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\AxessoWebService;
use App\Classes\AxessoWebServiceDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProductDetails\AmazonRequest;
use App\Models\CrawlerProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Route;

class AmazonProductController extends Controller
{
    public function url(AmazonRequest $request): JsonResponse
    {
        if ($asin = extractAsinAmazon($request->url)) {
            $product = CrawlerProduct::where('asin', 'amz-' . $asin)->first();

            if (filled($product)) {
                ++$product->view_count;
                $product->save();
                return $this->successResponse([
                    'asin' => 'amz-' . $asin,
                ], __('messages.item_found_success'));
            }
        }

        $response = json_decode((new AxessoWebService)->amazonProductInfo($request->url), true);
        if (isset($response[0]['logref']))
            return $this->errorResponse(__('messages.url_entered_invalid'));

        $data = AxessoWebServiceDTO::extractDetail($response, $request->url);
        $data['region'] = 1;


        CrawlerProduct::create([
            'website' => 'www.amazon.com',
            'source' => 'api-prd.axesso.de',
            'asin' => 'amz-' . $data['asin'],
            'url' => $request->url,
            'title' => $data['productTitle'],
            'title_fa' => null,
            'price' => $data['price'],
            'retail_price' => $data['retailPrice'] ?? 0,
            'price_saving' => $data['priceSaving'] ?? 0,
            'ratings' => $data['productRating'],
            'images' => $data['imageUrlList'],
            'description' => $data['productDescription'],
            'description_fa' => null,
            'reviews' => $data['reviews'],
            'videos' => $data['videoeUrlList'],
            'details' => $data['productDetails'],
            'features' => $data['features'],
            'exchange_type' => $data['exchangeType'],
            'exchange_name' => $data['exchangeName'],
            'region_type' => $data['region'],
            'region_name' => $data['regionName'],
            'weight' => $data['finalWeight'],
            'weight_unit' => $data['massUnit'],
            'categories' => $data['categories'],
            'variations' => $data['variations'],
            'response' => $response,
            'view_count' => 1,

        ]);

        return $this->successResponse([
            'asin' => 'amz-' . $data['asin'],
        ], __('messages.item_found_success'));


    }
}

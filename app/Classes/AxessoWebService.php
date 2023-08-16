<?php

namespace App\Classes;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Axesso is real-time data API to collect structured information from various
 * sources like Amazon, Walmart, Otto, Facebook, Instagram and many more.
 * axesso API documentation: http://api-doc.axesso.de/
 */

class AxessoWebService
{
    public ?PendingRequest $httpObject;

    public function __construct()
    {
        $this->httpObject = Http::withHeaders([
            'Content-Type' => 'application/json',
            'axesso-api-key' => config('charsooq.axesso_api_key')
        ]);
    }

    public function accountInfo(): PromiseInterface|Response
    {
        return $this->httpObject->get(config('charsooq.axesso_api_account_endpoint'), [
            'apiKey' => config('charsooq.axesso_api_key')
        ]);
    }

    public function amazonProductInfo(string $url): PromiseInterface|Response
    {
        return $this->httpObject->get(config('charsooq.axesso_api_query_endpoint') , [
            'url' => $url
        ]);
    }

    public function amazonProductSearch(string $keyword,string $domainCode='com',string $sortBy="relevanceblender",string $page="5",string $category)
    {
        return $this->httpObject->get(config('charsooq.axesso_api_search_product') , [
            'keyword'    => $keyword,
            'domainCode' =>$domainCode,
            'sortBy'     =>$sortBy,
            'page'       =>$page,
            'category'   => $category
        ]);
    }
}

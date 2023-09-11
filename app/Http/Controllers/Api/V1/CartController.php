<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\InvoiceCode;
use App\Helpers\Convertors;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Cart\CartStoreRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\ExchangeResource;
use App\Http\Resources\RegionResource;
use App\Http\Resources\ShopingSiteResource;
use App\Http\Resources\WeightResource;
use App\Models\Exchanges;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Region;
use App\Models\ShopingSite;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Weight;
use App\Notifications\SendMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $regions = Region::all();
        $regions = RegionResource::collection($regions);
        $exchanges = Exchanges::all();
        $exchanges = ExchangeResource::collection($exchanges);
        $weight = Weight::all();
        $weight = WeightResource::collection($weight);
        $shopingSite = ShopingSite::all();
        $shopingSite = ShopingSiteResource::collection($shopingSite);
        $userAddress = UserAddress::where('user_id', Auth::id())->get();
        $userAddress = AddressResource::collection($userAddress);
        return $this->successResponse([
            'regions' => $regions,
            'exchanges' => $exchanges,
            'weight' => $weight,
            'shopingSite' => $shopingSite,
            'userAddress' => $userAddress
        ]);
    }

    public function store(CartStoreRequest $request)//: JsonResponse
    {
        $code = InvoiceCode::generateCode();
        $invoices_data = [
            'status' => 'در حال بررسی',
            'code' => $code,
            'orderlevel' => 'درخواست',
            'totalprice' => 0,
            'totaltransportprice' => 0,
            'totalitemprice' => 0,
            'invoicingprice' => 0,
            'isonesteppayment' => 0,
            'user_id' => Auth::id(),
            'address_id' => $request->address_id,
            'description' => $request->description ?? '',
            'lastmodifydate' => Carbon::now(),
            'invoicedate' => Carbon::today()->toDateString(),
        ];
        DB::beginTransaction();
        try {
            $invoice = Invoice::create($invoices_data);
            $invoicesItemId = 0;
            foreach ($request->cart as $key => $cart_item) {
                $invoiceItemData = [
                    'link' => $cart_item['link'],
                    'name' => $cart_item['name'] ?? null,
                    'cost' => $cart_item['cost'],
                    'count' => $cart_item['count'],
                    'firstweight' => Convertors::weightConverter($cart_item['weight_unit'], $cart_item['firstweight']),
                    'ischecked' => 0,
                    'isauction' => 0,
                    'isapproved' => 0,
                    'isbuy' => 0,
                    'finalweight' => 0,
                    'transportprice' => 0,
                    'itemprice' => 0,
                    'exchangevalue' => 0,
                    'brokerwageprice' => 0,
                    'singleitemfullprice' => $cart_item['singleitemfullprice'],
                    'invoice_id' => $invoice->id,
                    'exchange_id' => $cart_item['exchange_id'],
                    'description' => $cart_item['description'],
                    'region_id' => $cart_item['region_id'],
                    'image' => $cart_item['image'],
                    'breakable' => 0
                ];
                $invoicesItemId = InvoiceItem::create($invoiceItemData)->id;
            }
            DB::commit();
            if ($invoice->id >= 1 and $invoicesItemId >= 1) {
                $user = User::find(Auth::id());
                $user->notify(new SendMessageNotification($user['name'] . ' ' . __('messages.sms_for_user_new_invoice') . $code));
                return $this->successResponse([
                    'code' => $code,
                ], $user['name'] . __('messages.sms_for_user_new_invoice') . $code);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse(__('messages.cart_store_failed'));
        }


    }

}

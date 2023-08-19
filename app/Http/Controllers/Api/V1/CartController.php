<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\InvoiceCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Cart\CartStoreRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Notifications\Channels\SmsRahyabChannel;
use App\Notifications\SendMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function store(CartStoreRequest $request): JsonResponse
    {
        $cart = json_decode($request->cart);
        $code = InvoiceCode::generateCode();
        $invoices_data = [
            'status' => 'در حال بررسی',
            'code' => $code,
            'orderlevel' => 'درخواست',
            'totalprice' => 0,
            'totaltransportprice' => 0,
            'totalitemprice' => 0,
            'invoicingprice' => 0,
            'new' => 1,
            'isonesteppayment' => 0,
            'user_id' => Auth::id(),
            'address_id' => 1,
            'description' => $request->description ?? '',
            'isFromCharsooq' => 1
        ];
        DB::beginTransaction();
        try {
            $invoices_inserted_id = Invoice::create($invoices_data);
            $invoices_item_inserted_id = 0;

            foreach ($cart as $key => $cart_item) {
                $invoices_item_data = [
                    'link' => $cart_item['url'],
                    'name' => $cart_item['name'] ?? null,
                    'cost' => $cart_item['price'],
                    'count' => $cart_item['quantity'],
                    'firstweight' => $cart_item['weight'],
                    'ischecked' => 0,
                    'isauction' => 0,
                    'isapproved' => 0,
                    'isbuy' => 0,
                    'finalweight' => 0,
                    'transportprice' => 0,
                    'itemprice' => 0,
                    'exchangevalue' => 0,
                    'brokerwageprice' => 0,
                    'singleitemfullprice' => $cart_item['priceRial'],
                    'apistatus' => 0,
                    'invoice_id' => $invoices_inserted_id,
                    'exchange_id' => $cart_item['exchange_id'],
                    'description' => $cart_item['description'],
                    'region_id' => $cart_item['region_id'],
                    'image' => $cart_item['image'],
                    'breakable' => 0
                ];
                $invoices_item_inserted_id = InvoiceItem::create($invoices_item_data);
            }
            DB::commit();
            if ($invoices_inserted_id >= 1 and $invoices_item_inserted_id >= 1) {
                $user = User::find(Auth::id());
                $user->notify(new SendMessageNotification($user['name'] . __('messages.sms_for_user_new_invoice') . $code));
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

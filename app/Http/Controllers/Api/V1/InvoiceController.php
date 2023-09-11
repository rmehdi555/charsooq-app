<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Convertors;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Invoice\InvoiceIndexRequest;
use App\Http\Resources\InvoiceItemResource;
use App\Http\Resources\InvoiceOthercostResource;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\TransactionResource;
use App\Models\Credit;
use App\Models\ExchangeInvoice;
use App\Models\Invoice;
use App\Models\InvoicesOthercosts;
use App\Models\Transaction;
use App\Services\Payment\Payment;
use App\Services\Payment\Request;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(InvoiceIndexRequest $request): JsonResponse
    {
        $invoices = Invoice::select('invoices.*', DB::raw('SUM(invoice_items.count) as sum_count'))
            ->where('user_id', Auth::id())
            ->leftjoin('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->when(
                isset($request->code),
                fn($q) => $q->where('invoices.code', '=', $request->code)
            )
            ->when(
                isset($request->date),
                fn($q) => $q->where('invoices.invoicedate', 'Like', '%' . $request->date . '%')
            )
            ->when(
                isset($request->order_level),
                fn($q) => $q->where('invoices.orderlevel', '=', $request->order_level)
            )
            ->groupBy('invoices.code')
            ->orderBy('invoices.created_at', 'desc')
            ->get();
        $invoices = InvoiceResource::collection($invoices);
        $status_order_level = ['در حال بررسی', 'پیش فاکتور در انتظار پرداخت', 'در حال خرید', 'ارسال به ایران', 'فاکتور نهایی در انتظار پرداخت', 'در حال ارسال به مشتری', 'نامشخص', 'تحویل مشتری'];
        return $this->successResponse([
            'invoices' => $invoices,
            'status_order_level' => $status_order_level
        ], '');
    }

    public function show($code): JsonResponse
    {
        $invoice = Invoice::where('code', $code)->first();
        if (!filled($invoice) or $invoice->user_id != Auth::id())
            return $this->errorResponse(__('messages.item_not_found'), 404);
        $user = Auth::user();
        $data = [];
        $data['code'] = $code;
        $data['invoice_status'] = $invoice->status;
        $data['order_level'] = $invoice->orderlevel;
        $data['items'] = InvoiceItemResource::collection($invoice->invoiceItems);
        $data['othercost_list'] = InvoicesOthercosts::where('invoice_id', $invoice->id)->get();
        $data['othercost_list'] = InvoiceOthercostResource::collection($data['othercost_list']);
        $data['transaction_list'] = Transaction::where('invoice_id', $invoice->id)->get();
        $data['transaction_list'] = TransactionResource::collection($data['transaction_list']);
        $data['sum_pay'] = Transaction::where('invoice_id', $invoice->id)->sum('amount');
        $data['othercost'] = InvoicesOthercosts::where('invoice_id', $invoice->id)->sum('OtherCostPrice');

        switch ($invoice->orderlevel) {
            case 'درخواست':
                $data['total_transport_price'] = 'نامشخص';
                $data['total_item_price'] = 'نامشخص';
                $data['brokerwage_price'] = 'نامشخص';
                $data['total_price'] = 'نامشخص';
                $data['price_for_pay'] = 'نامشخص';
                $data['is_pay'] = false;
                break;
            case 'فاکتور':
                $data['total_transport_price'] = $invoice->totaltransportprice;
                $data['total_item_price'] = $invoice->totalitemprice;
                $data['total_price'] = (int)$invoice->totalitemprice + (int)$invoice->totaltransportprice + (int)$data['othercost'];
                $data['price_for_pay'] = $data['total_price'] - (int)$data['sum_pay'];
                $data['is_pay'] = true;
                break;
            case 'آماده برای پرداخت':
                $data['total_transport_price'] = $invoice->finalTotaltransportprice;
                $data['total_item_price'] = $invoice->finalTotalitemprice;
                $data['total_price'] = (int)$invoice->finalTotalitemprice + (int)$invoice->finalTotaltransportprice + (int)$data['othercost'];
                $data['price_for_pay'] = $data['total_price'] - (int)$data['sum_pay'];
                $data['is_pay'] = true;
                break;

            default:
                $data['total_transport_price'] = $invoice->totaltransportprice;
                $data['total_item_price'] = $invoice->totalitemprice;
                $data['total_price'] = $invoice->totalitemprice + $invoice->totaltransportprice + (int)$data['othercost'];
                $data['price_for_pay'] = (int)$data['total_price'] - (int)$data['sum_pay'];
                $data['is_pay'] = false;
                break;
        }

        $data['user'] = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'cell_number' => $user->cell_number,
            'national_code' => $user->national_code,
            'wallet_balance' => $user->wallet_balance,
        ];

        return $this->successResponse($data, '');
    }

    public function onlinePayment($code): JsonResponse
    {
        $invoice = Invoice::where('code', $code)->first();
        if (!filled($invoice) or $invoice->user_id != Auth::id())
            return $this->errorResponse(__('messages.item_not_found'), 404);
        $sum_pay = Transaction::where('invoice_id', $invoice->id)->sum('amount');
        $othercost = InvoicesOthercosts::where('invoice_id', $invoice->id)->sum('OtherCostPrice');

        switch ($invoice->orderlevel) {
            case 'فاکتور':
                $price_for_pay = (int)$invoice->totalitemprice + (int)$invoice->totaltransportprice + (int)$othercost - (int)$sum_pay;
                $method = 'پرداخت اولیه';
                break;
            case 'آماده برای پرداخت':
                $price_for_pay = (int)$invoice->finalTotalitemprice + (int)$invoice->finalTotaltransportprice + (int)$othercost - (int)$sum_pay;
                $method = 'پرداخت ثانویه';
                break;

            default:
                return $this->errorResponse(__('messages.invoice_does_not_require_payment'));
        }
        if ($price_for_pay <= 0) {
            return $this->errorResponse(__('messages.invoice_does_not_require_payment'));
        }
        $payment = new Payment(config('payment'));
        $transaction = Transaction::create([
            'method' => $method,
            'amount' => $price_for_pay,
            'issuccess' => 0,
            'payment_method_id' => 1,
            'user_id' => Auth::id(),
            'invoice_id' => $invoice->id,
            'status' => 1,
        ]);
        try {
            $result = $payment->via(config('custom.map_wallets_payment')[config('custom.map_invoice_payment_default')])->purchase(
                (new \App\Services\Payment\Invoice)->amount($transaction->amount),
                function ($driver, $bankTransactionId) use ($transaction) {
                    $transaction->transId = $bankTransactionId;
                    $transaction->save();
                }
            )->pay()->getAction();
            return $this->successResponse($result);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function callbackZarinpal(Request $request): JsonResponse
    {
        $map = config('custom.map_wallets_payment');
        if (!isset($map[config('custom.map_invoice_payment_default')]))
            return $this->errorResponse('dont find config\custom.map_wallets_payment for zarinpalWallet');

        $transaction = Transaction::where('transId', $request->input('Authority'))
            ->where('status', 1)->first();

        if (!isset($transaction) or empty($transaction))
            return $this->errorResponse(__('messages.field_not_find'), 404);

        $payment = new Payment(config('payment'));
        DB::beginTransaction();
        try {
            $receipt = $payment->via(config('custom.map_wallets_payment')[config('custom.map_invoice_payment_default')])
                ->amount($transaction->amount)
                ->transactionId($transaction->transId)
                ->verify();
            $transaction->refnumber = $receipt->getReferenceId();
            $transaction->issuccess = 1;
            $transaction->status = 2;
            $transaction->save();
            ExchangeInvoice::storeAllExchangeValueForInvoice($transaction->invoice_id);

            Invoice::where('id', $transaction->invoice_id)->update([
                'orderlevel' => 'سفارش',
                'status' => 'در حال خرید'
            ]);
            DB::commit();
        } catch (Exception $e) {
            $transaction->status = 3;
            $transaction->save();
            return $this->errorResponse(__('messages.field_deposit_payment'));
        }
        return $this->successResponse($transaction, __('messages.success_payment'));
    }

    public function walletPayment($code): JsonResponse
    {
        $invoice = Invoice::where('code', $code)->first();
        if (!filled($invoice) or $invoice->user_id != Auth::id())
            return $this->errorResponse(__('messages.item_not_found'), 404);
        $sum_pay = Transaction::where('invoice_id', $invoice->id)->sum('amount');
        $othercost = InvoicesOthercosts::where('invoice_id', $invoice->id)->sum('OtherCostPrice');

        switch ($invoice->orderlevel) {
            case 'فاکتور':
                $price_for_pay = (int)$invoice->totalitemprice + (int)$invoice->totaltransportprice + (int)$othercost - (int)$sum_pay;
                $method = 'پرداخت اولیه';
                break;
            case 'آماده برای پرداخت':
                $price_for_pay = (int)$invoice->finalTotalitemprice + (int)$invoice->finalTotaltransportprice + (int)$othercost - (int)$sum_pay;
                $method = 'پرداخت ثانویه';
                break;

            default:
                return $this->errorResponse(__('messages.invoice_does_not_require_payment'),);
        }
        if ($price_for_pay <= 0)
            return $this->errorResponse(__('messages.invoice_does_not_require_payment'));

        $user = Auth::user();
        $user->wallet_balance = Credit::where('user_id', $user->id)->where('payment_status', 'Succeeded')->sum('amount');
        $user->save();
        if ($user->wallet_balance < $price_for_pay)
            return $this->errorResponse(__('messages.wallet_balance_not_enough'));
        DB::beginTransaction();
        try {
            $credit = Credit::create([
                'amount' => -$price_for_pay,
                'payment_status' => 'Succeeded',
                'status' => 'discharge',
                'user_id' => Auth::id(),
                'invoice_id' => $invoice->id,
            ]);
            $transaction = Transaction::create([
                'method' => $method,
                'amount' => $price_for_pay,
                'issuccess' => 1,
                'payment_method_id' => 6,
                'user_id' => Auth::id(),
                'invoice_id' => $invoice->id,
                'status' => 2,
            ]);
            $user->wallet_balance = Credit::where('user_id', $user->id)->where('payment_status', 'Succeeded')->sum('amount');
            $user->save();
            ExchangeInvoice::storeAllExchangeValueForInvoice($transaction->invoice_id);

            Invoice::where('id', $transaction->invoice_id)->update([
                'orderlevel' => 'سفارش',
                'status' => 'در حال خرید'
            ]);
            DB::commit();
            return $this->successResponse($transaction, __('messages.success_payment'));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Payment\PaymentListRequest;
use App\Http\Resources\PaymentListResource;
use App\Models\Transaction;
use App\Services\Payment\Invoice;
use App\Services\Payment\Payment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(PaymentListRequest $request): JsonResponse
    {
        $transaction = Transaction::where('transactions.user_id', Auth::id())
            ->when(
                isset($request->invoices_code),
                fn($q) => $q->leftjoin('invoices', 'invoices.id', '=', 'transactions.invoice_id')
                    ->where('invoices.code', 'Like', '%' . $request->invoices_code . '%')->select('transactions.*')
            )
            ->when(
                isset($request->date),
                fn($q) => $q->where('transactions.date', 'Like', '%' . $request->date . '%')
            )
            ->latest()
            ->paginate(config('custom.paginate_count'));
        $data = PaymentListResource::collection($transaction);
        return $this->successResponse($data, '');
    }

    public function invoiceOnline()
    {
        $payment = new Payment(config('payment'));
        $amount = 20000;
        try {
            $result = $payment->via(config('custom.map_wallets_payment')[config('custom.map_wallets_payment_default')])->purchase(
                (new Invoice)->amount($amount),
                function ($driver, $bankTransactionId) use ($amount) {
                    dd($bankTransactionId);
                }
            )->pay()->getAction();
            return $result;
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function callbackZarinpal(Request $request): JsonResponse
    {
        $map = config('custom.map_wallets_payment');
        $walletId = array_search('zarinpal', $map);
        if (!$walletId)
            return $this->errorResponse('dont find config\custom.map_wallets_payment for zarinpal');

//        $depositGateway = DepositGateway::findByBankTransactionId($request->input('Authority'), $walletId);
//
//        if (!isset($depositGateway) or empty($depositGateway))
//            return $this->errorResponse(__('messages.field_not_find'));
//
//        $depositGateway = depositGatewayVerify($depositGateway);
//        if ($depositGateway->status_id == DepositGatewayStatus::failed) {
//            $transactionAction->faildDepositPayment($depositGateway);
//            return $this->errorResponse(__('messages.field_deposit_payment'));
//        }
//
//        $transactionAction->stepNextDepositPayment($depositGateway);
//        return $this->successResponse($depositGateway);
    }
}

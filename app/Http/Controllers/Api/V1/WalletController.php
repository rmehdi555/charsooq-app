<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Wallet\WalletChargeRequest;
use App\Http\Resources\CreditResource;
use App\Models\Credit;
use App\Models\User;
use App\Services\Payment\Invoice;
use App\Services\Payment\Payment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index(Request $request): JsonResponse
    {

        $user = Auth::user();
        $paginateCount = isset($request->paginate_count) ? $request->paginate_count : config('custom.paginate_count');
        $credits = Credit::where('user_id', $user->id)->latest()
            ->paginate($paginateCount);
        $credits = CreditResource::collection($credits);

        return $this->successResponse([
            'wallet_balance' => $user->wallet_balance,
            'credits' => $credits
        ]);
    }

    public function walletCharge(WalletChargeRequest $request): JsonResponse
    {
        $payment = new Payment(config('payment'));
        $credit = Credit::create([
            'amount' => $request->amount,
            'payment_status' => 'Preinitiated',
            'status' => 'charge',
            'user_id' => Auth::id()
        ]);
        try {
            $result = $payment->via(config('custom.map_wallets_payment')[config('custom.map_wallets_payment_default')])->purchase(
                (new Invoice)->amount($credit->amount),
                function ($driver, $bankTransactionId) use ($credit) {
                    $credit->bank_transaction_id = $bankTransactionId;
                    $credit->save();
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
        if (!isset($map[config('custom.map_wallets_payment_default')]))
            return $this->errorResponse('dont find config\custom.map_wallets_payment for zarinpalWallet');

        $credit = Credit::where('bank_transaction_id', $request->input('Authority'))
            ->where('payment_status', 'Preinitiated')->first();

        if (!isset($credit) or empty($credit))
            return $this->errorResponse(__('messages.field_not_find'), 404);

        $payment = new Payment(config('payment'));
        try {
            $receipt = $payment->via(config('custom.map_wallets_payment')[config('custom.map_wallets_payment_default')])
                ->amount($credit->amount)
                ->transactionId($credit->bank_transaction_id)
                ->verify();
            $credit->bank_reference_id = $receipt->getReferenceId();
            $credit->payment_status = 'Succeeded';
            $credit->save();
            $user = User::find($credit->user_id);
            $user->wallet_balance = Credit::where('user_id', $credit->user_id)->where('payment_status', 'Succeeded')->sum('amount');
            $user->save();
        } catch (Exception $e) {
            $credit->payment_status = 'Failed';
            $credit->save();
            return $this->errorResponse(__('messages.field_deposit_payment'));
        }
        return $this->successResponse($credit);
    }
}

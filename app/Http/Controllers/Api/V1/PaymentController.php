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
}

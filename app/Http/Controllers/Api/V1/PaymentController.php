<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentListResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function list(): JsonResponse
    {
        $transaction = Transaction::where('user_id', Auth::id())
            ->latest()
            ->paginate(config('custom.paginate_count'));
        $data = PaymentListResource::collection($transaction);
        return $this->successResponse($data , '');
    }
}

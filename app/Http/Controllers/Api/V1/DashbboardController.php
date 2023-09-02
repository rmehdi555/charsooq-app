<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashbboardController extends Controller
{
    public function index(): JsonResponse
    {
        $user = User::where('id', Auth::id())->get();
        $tickets = Ticket::where('user_id', Auth::id())->where('status_id','!=',7)->count();
        $invoices = Invoice::where('user_id', Auth::id())->count();
        $buy_invoice = Invoice::where('user_id', Auth::id())->where('orderlevel','!=',['درخواست','نامشخص','آماده برای پرداخت'])->count();
        return $this->successResponse([
            'wallet_balance' => $user[0]['wallet_balance'],
            'open ticket' => $tickets,
            'invoices'=>$invoices,
            '$buy invoice'=>$buy_invoice
        ], '');
    }
}

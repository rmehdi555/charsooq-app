<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile(): JsonResponse
    {
        $user = Auth::user();

        return $this->successResponse([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'cell_number' => $user->cell_number,
            'national_code' => $user->national_code,
            'wallet_balance' => $user->wallet_balance,
        ], '');
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\profile\UpdateProfileRequest;
use App\Models\User;
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
            'wallet_balance' => (int)$user->wallet_balance,
        ], '');
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = User::where('id', Auth::id())->first();
        $user->name = $request->name;
        $user->nationalcode = $request->nationalcode;
        $user->email = $request->email;
        $user->cell_number = $request->cell_number;
        $user->telegram_user = $request->telegram_user;
        $user->save();
        return $this->successResponse($user->id, __('messages.profile_update_successfully'));

    }
}

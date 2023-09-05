<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Login\GetNumberRequest;
use App\Http\Requests\V1\Login\ValidateOtpRequest;
use App\Models\OtpUser;
use App\Models\User;
use App\Notifications\SendOtpCodeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    public function getNumber(GetNumberRequest $request): JsonResponse
    {
        $cell_number = $request->cell_number;
        $user = User::whereCellNumber($cell_number)->first();
        $otp = generateRandomNumber();

        OtpUser::setOtpForUser($user->id, $otp);

        $user->notify(new SendOtpCodeNotification($otp));

        return $this->successResponse([
            'user_id' => $user->id,
            //TODO remove $otp
            'otp' => $otp
        ]);
    }

    public function validateOTP(ValidateOtpRequest $request): JsonResponse
    {
        $user_id = $request->user_id;
        $otp = $request->otp;

        $otpValidation = OtpUser::validateOtp($user_id, $otp, 4000);

        if (!$otpValidation)
            return $this->errorResponse(__('messages.auth_failed_otp'));

        $user = User::find($user_id);

        return $this->successResponse([
            'user_id' => $user_id,
            'name' => $user->name ?? null,
            'cell_number' => $user->phone,
            'token' => $user->createToken('normal_user')->accessToken
        ], __('messages.success_login'));
    }

    public function logout(): JsonResponse
    {
        Auth::user()->token()->revoke();
        return $this->successResponse([], __('messages.success_logout'));
    }
}

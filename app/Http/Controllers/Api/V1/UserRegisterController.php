<?php

namespace App\Http\Controllers\Api\V1;

use App\Action\Api\UserRegistrationAction;
use App\Classes\FileUpload;
use App\Enum\FileCategory;
use App\Events\UserRegistrationCompletely;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Register\RegisterFromRequest;
use App\Http\Requests\V1\Register\RegisterImageAuthRequest;
use App\Http\Requests\V1\Register\RegisterSetNumberRequest;
use App\Http\Requests\V1\Register\RegisterValidateOtpRequest;
use App\Http\Requests\V1\Register\RegisterVideoAuthRequest;
use App\Models\AuthorizationFile;
use App\Models\OtpUser;
use App\Models\User;
use App\Notifications\SendOtpCodeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class UserRegisterController extends Controller
{

    public function setNumber(RegisterSetNumberRequest $request): JsonResponse
    {
        $cell_number = checkPhoneNumber($request->cell_number);
        $user = User::create(['cell_number' => $cell_number]);
        $otp = generateRandomNumber();
        OtpUser::setOtpForUser($user->id, $otp);
        $user->notify(new SendOtpCodeNotification($otp));
        return $this->successResponse([
            'user_id' => $user->id,
        ]);
    }

    public function validateOtp(RegisterValidateOtpRequest $request): JsonResponse
    {
        $user_id = $request->user_id;
        $otp = convertToEnglishDigit($request->otp);
        $user = User::find($user_id);

        $otpValidation = OtpUser::validateOtp($user_id, $otp, 4000);

        if (!$otpValidation)
            return $this->errorResponse(__('messages.auth_failed'));

        return $this->successResponse([
            'user_id' => $user_id,
            'name' => $user->name,
            'cell_number' => $user->cell_number,
            'token' => $user->createToken('normal_user')->accessToken
        ]);
    }

    public function registerForm(RegisterFromRequest $request): JsonResponse
    {
        $data = UserRegistrationAction::cleanApiDataRegisterForm($request->validated());
        User::whereId(Auth::user()->id)->update($data);
        return $this->successResponse([], __('messages.success_register'));
    }

}

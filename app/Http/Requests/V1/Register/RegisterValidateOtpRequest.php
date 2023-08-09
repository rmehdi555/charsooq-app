<?php

namespace App\Http\Requests\V1\Register;

use Illuminate\Foundation\Http\FormRequest;

class RegisterValidateOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|min:4',
        ];
    }
}

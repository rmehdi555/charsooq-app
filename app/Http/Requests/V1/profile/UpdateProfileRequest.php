<?php

namespace App\Http\Requests\V1\profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'nationalcode' => 'required|size:10',
            'email' => 'nullable|email',
            'cell_number' => 'required|size:11',
            'telegram_user' => 'nullable|string',
        ];
    }
}

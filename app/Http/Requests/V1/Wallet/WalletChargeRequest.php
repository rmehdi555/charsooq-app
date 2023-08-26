<?php

namespace App\Http\Requests\V1\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class WalletChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|integer|min:100000|max:500000000',
        ];
    }
}

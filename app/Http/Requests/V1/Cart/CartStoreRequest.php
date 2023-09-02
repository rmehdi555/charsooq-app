<?php

namespace App\Http\Requests\V1\Cart;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class CartStoreRequest extends FormRequest
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
            'cart' => 'required|array',
            'cart.*.link' => 'required|string',
            'cart.*.name' => 'required|string',
            'cart.*.cost' => 'required|string',
            'cart.*.count' => 'required|integer',
            'cart.*.firstweight' => 'required',
            'cart.*.singleitemfullprice' => 'required',
            'cart.*.exchange_id' => 'required|integer',
            'cart.*.description' => 'required|string',
            'cart.*.region_id' => 'required|integer',
            'cart.*.image' => 'required|string',
            'description' => 'nullable|string',
            'address_id' => 'nullable|integer|exists:users_addresses,id'
        ];
    }
}

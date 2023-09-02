<?php

namespace App\Http\Requests\V1\Calculator;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class CalculatorRequest extends FormRequest
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
            'price' => 'required|numeric',
            'weight_unit' => 'required|string|exists:weights,title',
            'exchange_id' => 'required|integer|exists:exchanges,id',
            'region_id' => 'required|integer|exists:regions,id',
            'weight' => 'required|integer',
        ];
    }
}

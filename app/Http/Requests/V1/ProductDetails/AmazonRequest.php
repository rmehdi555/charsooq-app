<?php

namespace App\Http\Requests\V1\ProductDetails;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class AmazonRequest extends FormRequest
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
            'url' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (parse_url($value, PHP_URL_HOST) != 'www.amazon.com')
                        $fail(__('messages.url_entered_invalid_amazon'));
                },
            ]
        ];
    }
}

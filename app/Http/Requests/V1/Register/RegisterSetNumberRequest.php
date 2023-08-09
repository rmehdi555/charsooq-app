<?php

namespace App\Http\Requests\V1\Register;

use Illuminate\Foundation\Http\FormRequest;

class RegisterSetNumberRequest extends FormRequest
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
            'cell_number' => 'required|min:10|unique:users,cell_number',
        ];
    }
}

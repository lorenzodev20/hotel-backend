<?php

namespace App\Http\Requests\Api\V1\Hotel;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('hotels')->whereNull('deleted_at'),
            ],
            'address' => 'required|string|max:255',
            'tax_id' => 'required|string|max:125',
            'quantity_rooms' => 'required|integer|min:0',
            'city_id' => 'required|integer|exists:cities,id',
        ];
    }
}

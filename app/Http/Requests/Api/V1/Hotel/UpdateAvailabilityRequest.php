<?php

namespace App\Http\Requests\Api\V1\Hotel;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvailabilityRequest extends FormRequest
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
            "quantity" => "required|integer|min:1",
            "room_type_id" => "required|integer|exists:room_types,id",
            "accommodation_type_id" => "required|integer|exists:accommodation_types,id",
            "hotel_id" => "required|integer|exists:hotels,id",
        ];
    }

    public function getQuantity(): int
    {
        return $this->get('quantity') ?? 1;
    }
    public function getHotelId(): int
    {
        return $this->get('hotel_id') ?? 1;
    }
    public function getRoomTypeId(): int
    {
        return $this->get('room_type_id') ?? 1;
    }
    public function getAccommodationTypeId(): int
    {
        return $this->get('accommodation_type_id') ?? 1;
    }
}

<?php

namespace App\Http\Requests\Api\V1\Paginator;

use Illuminate\Foundation\Http\FormRequest;

class SamplePaginatorRequest extends FormRequest
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
            "page" => "required|integer",
            "perPage" => "required|integer",
        ];
    }

    public function getPage(): int
    {
        return $this->get('page') ?? 1;
    }

    public function getPerPage(): int
    {
        return $this->get('perPage') ?? 10;
    }
}

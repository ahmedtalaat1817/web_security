<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMenuVariantRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'menu_item_id' => ['required', 'exists:menu_items,id'],
        'name' => ['required', 'string', 'max:255'],
        'price_adjustment' => ['required', 'numeric'],
        'is_available' => ['nullable'],
    ];
}
}

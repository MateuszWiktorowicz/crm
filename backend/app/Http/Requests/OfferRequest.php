<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'status_id' => 'required|exists:statuses,id',
            'total_net_price' => 'required|numeric',
            'offer_details' => 'required|array',
            'offer_details.*.tool_geometry_id' => 'required|exists:tool_geometries,id',
            'offer_details.*.tool_quantity' => 'required|integer|min:1',
            'offer_details.*.tool_discount' => 'nullable|numeric|min:0|max:100',
            'offer_details.*.tool_total_net_price' => 'required|numeric|min:0',
            'offer_details.*.tool_total_gross_price' => 'required|numeric|min:0',
            'offer_details.*.coating_price_id' => 'nullable|exists:coating_prices,id',
            'offer_details.*.coating_quantity' => 'nullable|integer|min:0',
            'offer_details.*.coating_discount' => 'nullable|numeric|min:0|max:100',
            'offer_details.*.coating_net_price' => 'nullable|numeric|min:0',
            'offer_details.*.coating_gross_price' => 'nullable|numeric|min:0',
        ];
    }
}

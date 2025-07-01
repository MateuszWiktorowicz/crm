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
            'offer_details.*.tool_geometry_id' => [
                'required_if:offer_details.*.tool_type,!=,Kartoteka', // Tylko jeśli toolType != "Kartoteka"
                'nullable',
                'exists:tool_geometries,id',
            ],
            'offer_details.*.coating_price_id' => 'nullable|exists:coating_prices,id',
                      'offer_details.*.tool_type_id' => 'required|numeric|min:0',
            'offer_details.*.tool_net_price' => 'required|numeric|min:0',
            'offer_details.*.coating_net_price' => 'nullable|numeric|min:0',
            'offer_details.*.quantity' => 'required|integer|min:1',
            'offer_details.*.discount' => 'nullable|numeric|min:0|max:100',
            'offer_details.*.radius' => 'numeric',
            'offer_details.*.regrinding_option' => 'nullable|string',
            'offer_details.*.description' => 'nullable|string',
            'offer_details.*.fileId' => 'nullable',
            'offer_details.*.symbol' => 'nullable',
            'pdf_info.deliveryTime' => 'nullable|string',
            'pdf_info.offerValidity' => 'nullable|string',
            'pdf_info.paymentTerms' => 'nullable|string',
            'pdf_info.displayDiscount' => 'nullable|boolean'
        ];
    }

    /**
 * Get custom attributes for validator errors.
 */
public function attributes(): array
{
    return [
        'customer_id' => 'Kontrahent',
        'status_id' => 'Status oferty',
        'total_net_price' => 'Kwota netto oferty',
        'offer_details' => 'Szczegóły oferty',
        'offer_details.*.tool_geometry_id' => 'Geometria narzędzia - Typ, Ilość ostrzy, średnica',
        'offer_details.*.coating_price_id' => 'Cena pokrycia',
        'offer_details.*.tool_net_price' => 'Cena jednostkowa ostrzenia netto',
        'offer_details.*.coating_net_price' => 'Cena jednostkowa pokrycia netto',
        'offer_details.*.quantity' => 'Ilość',
        'offer_details.*.discount' => 'Rabat',
        'offer_details.*.radius' => 'Promień',
        'offer_details.*.regrinding_option' => 'Wariant ostrzenia',
        'offer_details.*.description' => 'Opis pozycji',
    ];
}

}


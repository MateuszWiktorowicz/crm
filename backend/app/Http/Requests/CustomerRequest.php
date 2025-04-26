<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CustomerRequest extends FormRequest
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
        $customerId = $this->customer?->id;

        return [
            'code' => 'required|string|max:255|unique:customers,code,' . $customerId,
            'name' => 'required|string|max:255',
            'nip' => 'required|string|unique:customers,nip,' . $customerId,
            'zip_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'saler_marker' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:1000',
        ];
    }

     /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'code' => 'kod klienta',
            'name' => 'nazwa klienta',
            'nip' => 'NIP',
            'zip_code' => 'kod pocztowy',
            'city' => 'miasto',
            'address' => 'adres',
            'saler_marker' => 'znacznik handlowca',
            'description' => 'uwagi',
        ];
    }
}

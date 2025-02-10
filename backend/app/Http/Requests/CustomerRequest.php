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
        return [
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:10',
            'zip_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'saler_marker' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:255',
        ];
    }
}

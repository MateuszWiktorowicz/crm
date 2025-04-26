<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        switch ($this->method()) {
            case 'POST':
                return [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users,email,',
                    'password' => 'required|string|min:8|confirmed',
                    'marker' => 'required|string|max:10|unique:users,marker,',
                    'roles' => 'nullable|array',
                ];
                break;
            case 'PATCH':
            case 'PUT':
                return [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users,email,' .$this->user->id,
                    'password' => 'nullable|string|min:8|confirmed',
                    'marker' => 'required|string|max:10|unique:users,marker,' .$this->user->id,
                    'roles' => 'nullable|array',
                ];
        }
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nazwa użytkownika',
            'email' => 'adres e-mail',
            'password' => 'hasło',
            'marker' => 'znacznik',
            'roles' => 'role',
        ];
    }
}

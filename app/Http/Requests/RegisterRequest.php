<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|string|max:30|min:3',
            'last_name' => 'required|string|max:30|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:3|string|confirmed',
            'password_confirmation' => 'required|min:3|string|confirmed:password'
        ];
    }
    public function messages()
    {
        return [
            'password.confirmed' => 'Password doesn`t match.',
            'password_confirmation.confirmed' => 'Retype password doesn`t match.'
        ];
    }
}

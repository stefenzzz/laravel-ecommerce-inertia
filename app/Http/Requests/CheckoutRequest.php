<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
    public function rules()
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'phone' => ['required','numeric', 'min:7'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:3'],
            'password_confirmation' => ['required', 'string', 'confirmed:password'],

            'shippingAddress1' => ['required'],
            'shippingAddress2' => ['required'],
            'shippingCity' => ['required'],
            'shippingState' => ['required'],
            'shippingZipcode' => ['required'],
            'shippingCountry' => ['nullable', 'exists:countries,code'],

            'billingAddress1' => ['required'],
            'billingAddress2' => ['required'],
            'billingCity' => ['required'],
            'billingState' => ['required'],
            'billingZipcode' => ['required'],
            'billingCountry' => ['required', 'exists:countries,code'],

        ];
    }

    public function attributes()
    {
        return [
            'billingAddress1' => 'address 1',
            'billingAddress2' => 'address 2',
            'billingCity' => 'city',
            'billingState' => 'state',
            'billingZipcode' => 'zip code',
            'billingCountry' => 'country',
            'shippingAddress1' => 'address 1',
            'shippingAddress2' => 'address 2',
            'shippingCity' => 'city',
            'shippingState' => 'state',
            'shippingZipcode' => 'zip code',
            'shippingCountry' => 'country',
        ];
    }

    public function messages()
    {
        return [
            'password.confirmed' => 'Password didn`t match.',
            'password_confirmation.confirmed' => 'Password didn`t match.',
        ];
    }


}

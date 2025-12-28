<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'website' => ['nullable', 'string', 'max:0'], // honeypot
            'name'    => ['nullable', 'string', 'max:100'],
            'phone'   => ['required', 'string', 'max:50'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'page_url'=> ['nullable', 'string', 'max:2048'],
            'utm'     => ['nullable', 'array'],
        ];
    }


    public function messages(): array
    {
        return [
            'phone.required' => 'Укажите телефон.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'car' => ['nullable', 'string', 'max:255'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['nullable', 'string', 'max:1000'],
            'product_id' => ['nullable', 'exists:products,id'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Имя обязательно для заполнения',
            'name.max' => 'Имя не должно превышать 255 символов',
            'phone.required' => 'Телефон обязателен для заполнения',
            'phone.max' => 'Телефон не должен превышать 255 символов',
            'email.email' => 'Введите корректный email адрес',
            'email.max' => 'Email не должен превышать 255 символов',
            'car.max' => 'Марка авто не должна превышать 255 символов',
            'preferred_date.date' => 'Введите корректную дату',
            'preferred_date.after_or_equal' => 'Дата не может быть в прошлом',
            'message.max' => 'Сообщение не должно превышать 1000 символов',
            'product_id.exists' => 'Выбранный продукт не существует',
        ];
    }
}

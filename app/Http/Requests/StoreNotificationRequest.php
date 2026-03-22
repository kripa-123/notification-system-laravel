<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNotificationRequest extends FormRequest
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
            'type' => ['required', 'string', Rule::in(['email', 'sms', 'push'])],
            'payload' => ['required', 'array'],
            'payload.title' => ['required', 'string', 'max:255'],
            'payload.body' => ['required', 'string'],
            'payload.force_fail' => ['nullable', 'boolean'],
            'user_id' => ['required', 'exists:users,id'],
            'tenant_id' => ['required', 'integer'],

        ];
    }
}

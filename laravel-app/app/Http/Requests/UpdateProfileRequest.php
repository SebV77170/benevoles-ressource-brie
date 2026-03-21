<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'mail' => ['nullable', 'email:rfc,dns', 'max:255'],
            'tel' => ['nullable', 'string', 'max:30'],
        ];
    }
}

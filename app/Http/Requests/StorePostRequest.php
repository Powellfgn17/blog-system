<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // max 5MB
        ];
    }
}

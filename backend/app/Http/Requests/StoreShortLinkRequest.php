<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShortLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'max:32', 'regex:/^[a-zA-Z0-9_-]{1,32}$/'],
            'destination_url' => ['required', 'string', 'max:2048', 'url'],
            'title' => ['nullable', 'string', 'max:255'],
        ];
    }
}

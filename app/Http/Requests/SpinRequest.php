<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'min_playtime' => ['nullable', 'integer', 'min:0'],
            'max_playtime' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array{search: string|null, min_playtime: int|null, max_playtime: int|null}
     */
    public function filters(): array
    {
        return [
            'search' => $this->validated('search'),
            'min_playtime' => $this->validated('min_playtime'),
            'max_playtime' => $this->validated('max_playtime'),
        ];
    }
}
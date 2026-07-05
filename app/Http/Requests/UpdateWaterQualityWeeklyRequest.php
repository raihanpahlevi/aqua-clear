<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWaterQualityWeeklyRequest extends FormRequest
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
            'tgl' => ['required', 'date'],
            'tan' => ['nullable', 'numeric', 'min:0'],
            'ammonia' => ['nullable', 'numeric', 'min:0'],
            'nitrit' => ['nullable', 'numeric', 'min:0'],
            'nitrat' => ['nullable', 'numeric', 'min:0'],
            'tom' => ['nullable', 'numeric', 'min:0'],
            'alkalinitas' => ['nullable', 'numeric', 'min:0'],
            'fe' => ['nullable', 'numeric', 'min:0'],
            'vibrio_hijau' => ['nullable', 'numeric', 'min:0'],
            'vibrio_hitam' => ['nullable', 'numeric', 'min:0'],
            'vibrio_luminer' => ['nullable', 'numeric', 'min:0'],
            'total_bakteri' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePrepLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Checklist bebas, tanpa validasi ketat — lihat CLAUDE.md Bagian 8.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cycle_id' => ['nullable', 'exists:cycles,id'],
            'jenis' => ['required', 'in:tambak,air'],
            'tgl' => ['required', 'date'],
            'checklist' => ['nullable', 'array'],
            'checklist.*' => ['nullable'],
            'item_lainnya' => ['nullable', 'string', 'max:255'],
            'biaya' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHarvestRequest extends FormRequest
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
            'tahap' => ['required', 'in:partial1,partial2,total'],
            'tgl' => ['required', 'date'],
            'berat_kg' => ['required', 'numeric', 'min:0.01'],
            'size' => ['nullable', 'numeric', 'min:0'],
            'harga_per_kg' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}

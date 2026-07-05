<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryUsageRequest extends FormRequest
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
            'kategori' => ['required', 'in:pakan,probiotik,mineral,desinfektan,obat'],
            'item' => ['required', 'string', 'max:150'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'satuan' => ['nullable', 'string', 'max:30'],
            'harga' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

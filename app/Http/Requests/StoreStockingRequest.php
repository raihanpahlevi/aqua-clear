<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockingRequest extends FormRequest
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
            'cycle_id' => ['required', 'exists:cycles,id'],
            'tgl_tebar' => ['required', 'date'],
            'tgl_pakan_pertama' => ['nullable', 'date', 'after_or_equal:tgl_tebar'],
            'asal_benur' => ['nullable', 'string', 'max:150'],
            'jumlah_tebar' => ['required', 'integer', 'min:1'],
            'harga_benur' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

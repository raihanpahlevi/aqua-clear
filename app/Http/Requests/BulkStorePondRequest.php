<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BulkStorePondRequest extends FormRequest
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
            'block_id' => ['required', 'exists:blocks,id'],
            'prefix' => ['required', 'string', 'max:20'],
            'nomor_mulai' => ['required', 'integer', 'min:1'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:100'],
            'luas' => ['nullable', 'numeric', 'min:0'],
            'kapasitas' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:kosong,siap_tebar,aktif,panen,maintenance'],
        ];
    }
}

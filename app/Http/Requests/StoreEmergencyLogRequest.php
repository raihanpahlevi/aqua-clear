<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmergencyLogRequest extends FormRequest
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
            'jenis' => ['required', 'string', 'max:150'],
            'tindakan' => ['nullable', 'string'],
            'keputusan' => ['nullable', 'in:lanjut,flush_out,panen_parsial'],
        ];
    }
}

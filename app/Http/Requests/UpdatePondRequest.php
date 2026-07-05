<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePondRequest extends FormRequest
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
            'kode_kolam' => [
                'required', 'string', 'max:50',
                Rule::unique('ponds')->where('block_id', $this->input('block_id'))->ignore($this->route('pond')),
            ],
            'luas' => ['nullable', 'numeric', 'min:0'],
            'kapasitas' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:kosong,siap_tebar,aktif,panen,maintenance'],
        ];
    }
}

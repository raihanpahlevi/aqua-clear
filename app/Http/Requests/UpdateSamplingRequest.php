<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSamplingRequest extends FormRequest
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
            'tgl' => ['required', 'date', function ($attribute, $value, $fail) {
                $stocking = $this->route('stocking');

                if (! $stocking->tgl_pakan_pertama) {
                    $fail('Tanggal pakan pertama siklus ini belum diisi — lengkapi dulu di halaman Edit Siklus sebelum input sampling, karena DOC dihitung dari situ.');

                    return;
                }

                if ($stocking->tgl_pakan_pertama->gt($value)) {
                    $fail('Tanggal sampling tidak boleh sebelum tanggal pakan pertama ('.$stocking->tgl_pakan_pertama->format('d M Y').').');
                }
            }],
            'berat_sampel_total' => ['required', 'numeric', 'min:0.01'],
            'jumlah_sampel' => ['required', 'integer', 'min:1'],
            'populasi' => ['required', 'integer', 'min:0'],
            'kondisi_organ' => ['nullable', 'string', 'max:150'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}

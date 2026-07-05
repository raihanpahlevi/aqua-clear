<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDailyLogRequest extends FormRequest
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
        $anchoRule = ['nullable', 'in:habis,sisa_sedikit,sisa_banyak'];

        return [
            'tgl' => [
                'required',
                'date',
                Rule::unique('daily_logs')
                    ->where('stocking_id', $this->route('stocking')->id)
                    ->ignore($this->route('dailyLog')->id),
            ],
            'pakan_07_kg' => ['nullable', 'numeric', 'min:0'],
            'pakan_11_kg' => ['nullable', 'numeric', 'min:0'],
            'pakan_15_kg' => ['nullable', 'numeric', 'min:0'],
            'pakan_19_kg' => ['nullable', 'numeric', 'min:0'],
            'kode_pakan' => ['nullable', 'string', 'max:50'],
            'ancho_07' => $anchoRule,
            'ancho_11' => $anchoRule,
            'ancho_15' => $anchoRule,
            'ancho_19' => $anchoRule,
            'do_pagi' => ['nullable', 'numeric', 'min:0'],
            'do_sore' => ['nullable', 'numeric', 'min:0'],
            'ph_pagi' => ['nullable', 'numeric', 'min:0'],
            'ph_sore' => ['nullable', 'numeric', 'min:0'],
            'suhu_pagi' => ['nullable', 'numeric', 'min:0'],
            'suhu_sore' => ['nullable', 'numeric', 'min:0'],
            'salinitas' => ['nullable', 'numeric', 'min:0'],
            'mortalitas' => ['nullable', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}

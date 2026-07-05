<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePondMaintenanceLogRequest extends FormRequest
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
        $uniqueTgl = Rule::unique('pond_maintenance_logs')->where('stocking_id', $this->route('stocking')->id);

        if ($maintenanceLog = $this->route('pondMaintenanceLog')) {
            $uniqueTgl = $uniqueTgl->ignore($maintenanceLog->id);
        }

        return [
            'tgl' => ['required', 'date', $uniqueTgl],
            'siphon' => ['nullable', 'boolean'],
            'kondisi_lumpur' => ['nullable', 'string', 'max:100'],
            'jumlah_kincir' => ['nullable', 'integer', 'min:0'],
            'jam_nyala_kincir' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}

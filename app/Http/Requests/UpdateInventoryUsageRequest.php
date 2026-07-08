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
            'warehouse_item_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('warehouse_items', 'id')->where('farm_id', $this->user()->farm_id),
            ],
            // Kalau ambil dari gudang, nama item & kategori otomatis ikut master (diisi controller)
            'kategori' => ['required_without:warehouse_item_id', 'nullable', 'in:pakan,probiotik,mineral,desinfektan,obat'],
            'item' => ['required_without:warehouse_item_id', 'nullable', 'string', 'max:150'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'satuan' => ['nullable', 'string', 'max:30'],
            'harga' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

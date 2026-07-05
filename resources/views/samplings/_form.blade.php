@php
    $old = fn ($field, $default = null) => old($field, $sampling?->{$field} ?? $default);
@endphp

<div>
    <x-input-label for="tgl" value="Tanggal Sampling" />
    <x-text-input id="tgl" name="tgl" type="date" class="mt-1 block w-full max-w-xs" :value="$old('tgl', $sampling?->tgl?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
    <x-input-error :messages="$errors->get('tgl')" class="mt-2" />
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">DOC dihitung otomatis dari tanggal pakan pertama siklus ini.</p>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="berat_sampel_total" value="Berat Sampel Total (gram)" />
        <x-text-input id="berat_sampel_total" name="berat_sampel_total" type="number" step="0.01" class="mt-1 block w-full" :value="$old('berat_sampel_total')" required />
        <x-input-error :messages="$errors->get('berat_sampel_total')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="jumlah_sampel" value="Jumlah Sampel (ekor)" />
        <x-text-input id="jumlah_sampel" name="jumlah_sampel" type="number" class="mt-1 block w-full" :value="$old('jumlah_sampel')" required />
        <x-input-error :messages="$errors->get('jumlah_sampel')" class="mt-2" />
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">MBW = berat sampel total ÷ jumlah sampel, dihitung otomatis.</p>
    </div>
</div>

<div>
    <x-input-label for="populasi" value="Estimasi Populasi Saat Ini (ekor)" />
    <x-text-input id="populasi" name="populasi" type="number" class="mt-1 block w-full" :value="$old('populasi')" required />
    <x-input-error :messages="$errors->get('populasi')" class="mt-2" />
</div>

<div>
    <x-input-label for="kondisi_organ" value="Kondisi Organ" />
    <x-text-input id="kondisi_organ" name="kondisi_organ" type="text" class="mt-1 block w-full" :value="$old('kondisi_organ')" />
</div>

<div>
    <x-input-label for="catatan" value="Catatan" />
    <textarea id="catatan" name="catatan" rows="2" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">{{ $old('catatan') }}</textarea>
</div>

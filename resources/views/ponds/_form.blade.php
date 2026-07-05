@php
    $old = fn ($field, $default = null) => old($field, $pond?->{$field} ?? $default);
@endphp

<div>
    <x-input-label for="block_id" value="Blok" />
    <select id="block_id" name="block_id" required class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
        <option value="">— Pilih Blok —</option>
        @foreach ($blocks as $block)
            <option value="{{ $block->id }}" @selected($old('block_id') == $block->id)>Blok {{ $block->nama }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('block_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="kode_kolam" value="Kode Kolam" />
    <x-text-input id="kode_kolam" name="kode_kolam" type="text" class="mt-1 block w-full" :value="$old('kode_kolam')" required autofocus />
    <x-input-error :messages="$errors->get('kode_kolam')" class="mt-2" />
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="luas" value="Luas (m²)" />
        <x-text-input id="luas" name="luas" type="number" step="0.01" class="mt-1 block w-full" :value="$old('luas')" />
        <x-input-error :messages="$errors->get('luas')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="kapasitas" value="Kapasitas" />
        <x-text-input id="kapasitas" name="kapasitas" type="number" step="0.01" class="mt-1 block w-full" :value="$old('kapasitas')" />
        <x-input-error :messages="$errors->get('kapasitas')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="status" value="Status" />
    <select id="status" name="status" required class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
        @foreach (['kosong', 'siap_tebar', 'aktif', 'panen', 'maintenance'] as $status)
            <option value="{{ $status }}" @selected($old('status', 'kosong') === $status)>{{ str_replace('_', ' ', $status) }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>

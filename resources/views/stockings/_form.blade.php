@php
    $old = fn ($field, $default = null) => old($field, $stocking?->{$field}?->format('Y-m-d') ?? $stocking?->{$field} ?? $default);
@endphp

<div>
    <x-input-label for="cycle_id" value="Siklus" />
    <select id="cycle_id" name="cycle_id" required class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
        <option value="">— Pilih Siklus —</option>
        @foreach ($cycles as $cycle)
            <option value="{{ $cycle->id }}" @selected(old('cycle_id', $stocking?->cycle_id) == $cycle->id)>{{ $cycle->nama }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('cycle_id')" class="mt-2" />
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Belum ada siklus yang cocok? <a href="{{ route('cycles.index') }}" class="underline">Tambah siklus baru</a>.</p>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="tgl_tebar" value="Tanggal Tebar" />
        <x-text-input id="tgl_tebar" name="tgl_tebar" type="date" class="mt-1 block w-full" :value="$old('tgl_tebar')" required />
        <x-input-error :messages="$errors->get('tgl_tebar')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="tgl_pakan_pertama" value="Tgl Pakan Pertama (anchor DOC)" />
        <x-text-input id="tgl_pakan_pertama" name="tgl_pakan_pertama" type="date" class="mt-1 block w-full" :value="$old('tgl_pakan_pertama')" />
        <x-input-error :messages="$errors->get('tgl_pakan_pertama')" class="mt-2" />
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">DOC dihitung dari tanggal ini, bukan tanggal tebar. Bisa diisi menyusul.</p>
    </div>
</div>

<div>
    <x-input-label for="asal_benur" value="Asal Benur" />
    <x-text-input id="asal_benur" name="asal_benur" type="text" class="mt-1 block w-full" :value="$old('asal_benur')" />
    <x-input-error :messages="$errors->get('asal_benur')" class="mt-2" />
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="jumlah_tebar" value="Jumlah Tebar (ekor)" />
        <x-text-input id="jumlah_tebar" name="jumlah_tebar" type="number" class="mt-1 block w-full" :value="$old('jumlah_tebar')" required />
        <x-input-error :messages="$errors->get('jumlah_tebar')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="harga_benur" value="Harga Benur (Rp)" />
        <x-text-input id="harga_benur" name="harga_benur" type="number" step="0.01" class="mt-1 block w-full" :value="$old('harga_benur')" />
        <x-input-error :messages="$errors->get('harga_benur')" class="mt-2" />
    </div>
</div>

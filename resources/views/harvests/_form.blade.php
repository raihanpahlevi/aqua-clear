@php
    $old = fn ($field, $default = null) => old($field, $harvest?->{$field} ?? $default);
@endphp

<div>
    <x-input-label for="tahap" value="Tahap" />
    <select id="tahap" name="tahap" required class="mt-1 block w-full max-w-xs border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
        @foreach ($referensiTahap as $value => $ref)
            <option value="{{ $value }}" @selected($old('tahap') === $value)>{{ $ref['label'] }} (MBW {{ $ref['mbw'] }}, size {{ $ref['size'] }})</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('tahap')" class="mt-2" />
</div>

<div>
    <x-input-label for="tgl" value="Tanggal Panen" />
    <x-text-input id="tgl" name="tgl" type="date" class="mt-1 block w-full max-w-xs" :value="$old('tgl', $harvest?->tgl?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
    <x-input-error :messages="$errors->get('tgl')" class="mt-2" />
</div>

<div class="grid grid-cols-3 gap-4">
    <div>
        <x-input-label for="berat_kg" value="Berat (kg)" />
        <x-text-input id="berat_kg" name="berat_kg" type="number" step="0.01" class="mt-1 block w-full" :value="$old('berat_kg')" required />
        <x-input-error :messages="$errors->get('berat_kg')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="size" value="Size (ekor/kg)" />
        <x-text-input id="size" name="size" type="number" step="0.01" class="mt-1 block w-full" :value="$old('size')" />
        <x-input-error :messages="$errors->get('size')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="harga_per_kg" value="Harga/kg (Rp)" />
        <x-text-input id="harga_per_kg" name="harga_per_kg" type="number" step="0.01" class="mt-1 block w-full" :value="$old('harga_per_kg')" required />
        <x-input-error :messages="$errors->get('harga_per_kg')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="catatan" value="Catatan" />
    <textarea id="catatan" name="catatan" rows="2" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">{{ $old('catatan') }}</textarea>
</div>

@if(($old('tahap') ?? '') === 'total')
    <div class="p-3 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-sm rounded-lg">
        Tahap "Total/Habis" akan mengubah status kolam jadi "panen".
    </div>
@endif

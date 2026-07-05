@php
    $old = fn ($field, $default = null) => old($field, $inventoryUsage?->{$field} ?? $default);
@endphp

<div>
    <x-input-label for="tgl" value="Tanggal" />
    <x-text-input id="tgl" name="tgl" type="date" class="mt-1 block w-full max-w-xs" :value="$old('tgl', $inventoryUsage?->tgl?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
    <x-input-error :messages="$errors->get('tgl')" class="mt-2" />
</div>

<div>
    <x-input-label for="kategori" value="Kategori" />
    <select id="kategori" name="kategori" required class="mt-1 block w-full max-w-xs border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
        @foreach (['pakan', 'probiotik', 'mineral', 'desinfektan', 'obat'] as $kategori)
            <option value="{{ $kategori }}" @selected($old('kategori') === $kategori)>{{ ucfirst($kategori) }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
</div>

<div>
    <x-input-label for="item" value="Nama Item" />
    <x-text-input id="item" name="item" type="text" class="mt-1 block w-full" :value="$old('item')" required placeholder="mis. Pakan 3M, Probiotik X" />
    <x-input-error :messages="$errors->get('item')" class="mt-2" />
</div>

<div class="grid grid-cols-3 gap-4">
    <div>
        <x-input-label for="qty" value="Qty" />
        <x-text-input id="qty" name="qty" type="number" step="0.01" class="mt-1 block w-full" :value="$old('qty')" required />
        <x-input-error :messages="$errors->get('qty')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="satuan" value="Satuan" />
        <x-text-input id="satuan" name="satuan" type="text" class="mt-1 block w-full" :value="$old('satuan')" placeholder="kg, liter, sak" />
    </div>
    <div>
        <x-input-label for="harga" value="Biaya Total (Rp)" />
        <x-text-input id="harga" name="harga" type="number" step="0.01" class="mt-1 block w-full" :value="$old('harga')" />
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kosongkan kalau harga acuan belum tersedia.</p>
    </div>
</div>

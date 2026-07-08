@php
    $old = fn ($field, $default = null) => old($field, $inventoryUsage?->{$field} ?? $default);
@endphp

<div>
    <x-input-label for="tgl" value="Tanggal" />
    <x-text-input id="tgl" name="tgl" type="date" class="mt-1 block w-full max-w-xs" :value="$old('tgl', $inventoryUsage?->tgl?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
    <x-input-error :messages="$errors->get('tgl')" class="mt-2" />
</div>

<div>
    <x-input-label for="warehouse_item_id" value="Ambil dari Gudang (opsional)" />
    <select id="warehouse_item_id" name="warehouse_item_id" class="mt-1 block w-full border-lumpur/40 bg-paper text-ink focus:border-teal-mid focus:ring-teal-mid rounded-lg shadow-sm">
        <option value="">— tidak dari gudang —</option>
        @foreach ($warehouseItems as $w)
            <option value="{{ $w['item']->id }}" @selected($old('warehouse_item_id') == $w['item']->id)>
                {{ $w['item']->nama }} — saldo {{ number_format($w['saldo'], 1, ',', '.') }} {{ $w['item']->satuan }}
            </option>
        @endforeach
    </select>
    <p class="text-xs text-ink/50 mt-1">Kalau dipilih: saldo gudang otomatis berkurang, dan nama item + kategori ikut barang gudang (isian di bawah boleh dikosongkan).</p>
    <x-input-error :messages="$errors->get('warehouse_item_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="kategori" value="Kategori" />
    <select id="kategori" name="kategori" required class="mt-1 block w-full max-w-xs border-lumpur/40 bg-paper text-ink focus:border-teal-mid focus:ring-teal-mid rounded-lg shadow-sm">
        @foreach (['pakan', 'probiotik', 'mineral', 'desinfektan', 'obat'] as $kategori)
            <option value="{{ $kategori }}" @selected($old('kategori') === $kategori)>{{ ucfirst($kategori) }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
</div>

<div>
    <x-input-label for="item" value="Nama Item (wajib kalau tidak ambil dari gudang)" />
    <x-text-input id="item" name="item" type="text" class="mt-1 block w-full" :value="$old('item')" placeholder="mis. Pakan 3M, Probiotik X" />
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
        <p class="text-xs text-ink/50 mt-1">Kosongkan kalau harga acuan belum tersedia.</p>
    </div>
</div>

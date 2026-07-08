@php
    $old = fn ($field, $default = null) => old($field, $item?->{$field} ?? $default);
@endphp

<div>
    <x-input-label for="nama" value="Nama Barang" />
    <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="$old('nama')" required placeholder="mis. Pakan Grower SGH, Probiotik Rhodo" />
    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="kategori" value="Kategori" />
        <select id="kategori" name="kategori" required class="mt-1 block w-full border-lumpur/40 bg-paper text-ink focus:border-teal-mid focus:ring-teal-mid rounded-lg shadow-sm">
            @foreach (['pakan', 'probiotik', 'mineral', 'desinfektan', 'obat'] as $kategori)
                <option value="{{ $kategori }}" @selected($old('kategori') === $kategori)>{{ ucfirst($kategori) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="satuan" value="Satuan" />
        <x-text-input id="satuan" name="satuan" type="text" class="mt-1 block w-full" :value="$old('satuan')" required placeholder="kg / liter / sak / botol" />
        <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
    </div>
</div>

<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Catat Barang Masuk" subtitle="Pembelian / restock gudang" :back="route('gudang.index')" />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-xl">
            @if ($items->isEmpty())
                <x-card class="text-center py-10">
                    <p class="text-ink/50 text-sm">Belum ada barang terdaftar. <a href="{{ route('gudang.item.create') }}" class="text-teal-mid hover:underline font-medium">Daftarkan barang dulu</a>.</p>
                </x-card>
            @else
                <x-card>
                    <form method="POST" action="{{ route('gudang.entry.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="warehouse_item_id" value="Barang" />
                            <select id="warehouse_item_id" name="warehouse_item_id" required class="mt-1 block w-full border-lumpur/40 bg-paper text-ink focus:border-teal-mid focus:ring-teal-mid rounded-lg shadow-sm">
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" @selected(old('warehouse_item_id') == $item->id)>{{ $item->nama }} ({{ $item->kategori }}, {{ $item->satuan }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('warehouse_item_id')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tgl" value="Tanggal" />
                                <x-text-input id="tgl" name="tgl" type="date" class="mt-1 block w-full" :value="old('tgl', now()->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tgl')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="qty" value="Jumlah (sesuai satuan barang)" />
                                <x-text-input id="qty" name="qty" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('qty')" required />
                                <x-input-error :messages="$errors->get('qty')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="harga" value="Total Harga (Rp, opsional)" />
                            <x-text-input id="harga" name="harga" type="number" step="0.01" min="0" class="mt-1 block w-full max-w-xs" :value="old('harga')" />
                            <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="catatan" value="Catatan (opsional)" />
                            <x-text-input id="catatan" name="catatan" type="text" class="mt-1 block w-full" :value="old('catatan')" placeholder="mis. supplier, no. nota" />
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('gudang.index') }}" class="text-sm text-ink/50 py-2">Batal</a>
                            <x-primary-button>Simpan</x-primary-button>
                        </div>
                    </form>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

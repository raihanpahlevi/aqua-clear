<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Tambah Banyak Kolam Sekaligus') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">

                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                    Cocok untuk setup awal (mis. 76 kolam). Kode kolam otomatis dibuat dari prefix + nomor urut — mis. prefix "A", mulai 1, jumlah 15 → A1, A2, ... A15. Kode yang sudah ada otomatis dilewati.
                </p>

                <form method="POST" action="{{ route('ponds.bulk-store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="block_id" value="Blok" />
                        <select id="block_id" name="block_id" required class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
                            <option value="">— Pilih Blok —</option>
                            @foreach ($blocks as $block)
                                <option value="{{ $block->id }}" @selected(old('block_id') == $block->id)>Blok {{ $block->nama }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('block_id')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="prefix" value="Prefix Kode" />
                            <x-text-input id="prefix" name="prefix" type="text" class="mt-1 block w-full" :value="old('prefix')" required placeholder="mis. A" />
                            <x-input-error :messages="$errors->get('prefix')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="nomor_mulai" value="Nomor Mulai" />
                            <x-text-input id="nomor_mulai" name="nomor_mulai" type="number" class="mt-1 block w-full" :value="old('nomor_mulai', 1)" required />
                            <x-input-error :messages="$errors->get('nomor_mulai')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="jumlah" value="Jumlah Kolam" />
                            <x-text-input id="jumlah" name="jumlah" type="number" class="mt-1 block w-full" :value="old('jumlah')" required max="100" />
                            <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="luas" value="Luas per Kolam (m²) — opsional, sama untuk semua" />
                            <x-text-input id="luas" name="luas" type="number" step="0.01" class="mt-1 block w-full" :value="old('luas')" />
                        </div>
                        <div>
                            <x-input-label for="kapasitas" value="Kapasitas per Kolam — opsional, sama untuk semua" />
                            <x-text-input id="kapasitas" name="kapasitas" type="number" step="0.01" class="mt-1 block w-full" :value="old('kapasitas')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="status" value="Status Awal" />
                        <select id="status" name="status" required class="mt-1 block w-full max-w-xs border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
                            @foreach (['kosong', 'siap_tebar', 'aktif', 'panen', 'maintenance'] as $status)
                                <option value="{{ $status }}" @selected(old('status', 'kosong') === $status)>{{ str_replace('_', ' ', $status) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('ponds.index') }}" class="text-sm text-slate-500 dark:text-slate-400 py-2">Batal</a>
                        <x-primary-button>Buat Kolam</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

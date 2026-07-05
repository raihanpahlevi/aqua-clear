<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Catat Kejadian — Kolam {{ $stocking->pond->kode_kolam }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                <form method="POST" action="{{ route('stockings.emergency-logs.store', $stocking) }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="tgl" value="Tanggal" />
                        <x-text-input id="tgl" name="tgl" type="date" class="mt-1 block w-full max-w-xs" :value="old('tgl', now()->format('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('tgl')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="jenis" value="Jenis Kejadian" />
                        <x-text-input id="jenis" name="jenis" type="text" class="mt-1 block w-full" :value="old('jenis')" required placeholder="mis. udang sakit, air jelek, SR turun" />
                        <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="tindakan" value="Tindakan Penanganan" />
                        <textarea id="tindakan" name="tindakan" rows="3" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">{{ old('tindakan') }}</textarea>
                    </div>

                    <div>
                        <x-input-label for="keputusan" value="Keputusan" />
                        <select id="keputusan" name="keputusan" class="mt-1 block w-full max-w-xs border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
                            <option value="">—</option>
                            <option value="lanjut" @selected(old('keputusan') === 'lanjut')>Lanjut</option>
                            <option value="flush_out" @selected(old('keputusan') === 'flush_out')>Flush-out</option>
                            <option value="panen_parsial" @selected(old('keputusan') === 'panen_parsial')>Panen Parsial</option>
                        </select>
                        <x-input-error :messages="$errors->get('keputusan')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('stockings.emergency-logs.index', $stocking) }}" class="text-sm text-slate-500 dark:text-slate-400 py-2">Batal</a>
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

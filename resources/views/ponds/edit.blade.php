<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Edit Kolam') }} — {{ $pond->kode_kolam }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                <form method="POST" action="{{ route('ponds.update', $pond) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('ponds._form', ['pond' => $pond])

                    <div class="flex justify-between items-center">
                        <form method="POST" action="{{ route('ponds.destroy', $pond) }}" onsubmit="return confirm('Yakin hapus kolam ini? Cuma bisa kalau belum ada riwayat siklus.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Hapus Kolam</button>
                        </form>

                        <div class="flex gap-3">
                            <a href="{{ route('ponds.index') }}" class="text-sm text-slate-500 dark:text-slate-400 py-2">Batal</a>
                            <x-primary-button>Simpan Perubahan</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

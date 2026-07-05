<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Edit Input Harian — Kolam {{ $stocking->pond->kode_kolam }} — {{ $dailyLog->tgl->format('d M Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase">FR% (dari biomass terakhir)</div>
                    <div class="text-xl font-semibold text-slate-800 dark:text-slate-200">{{ $fr !== null ? number_format($fr, 2).'%' : '—' }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase">Mortalitas Terkoreksi (×2)</div>
                    <div class="text-xl font-semibold text-slate-800 dark:text-slate-200">{{ $correctedMortality ?? '—' }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase">Porsi Ancho (2% total pakan)</div>
                    <div class="text-xl font-semibold text-slate-800 dark:text-slate-200">{{ number_format($anchoPortionKg, 3) }} kg</div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                <form method="POST" action="{{ route('stockings.daily-logs.update', [$stocking, $dailyLog]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('daily-logs._form', ['dailyLog' => $dailyLog])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('stockings.daily-logs.index', $stocking) }}" class="text-sm text-slate-500 dark:text-slate-400 py-2">Batal</a>
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

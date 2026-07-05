<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Input Harian — Kolam {{ $stocking->pond->kode_kolam }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                <form method="POST" action="{{ route('stockings.daily-logs.store', $stocking) }}" class="space-y-6">
                    @csrf
                    @include('daily-logs._form', ['dailyLog' => null])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('stockings.daily-logs.index', $stocking) }}" class="text-sm text-slate-500 dark:text-slate-400 py-2">Batal</a>
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink leading-tight">
            Input Harian — Kolam {{ $stocking->pond->kode_kolam }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-sand/40 p-6 rounded-2xl border border-lumpur/20">
                <form method="POST" action="{{ route('stockings.daily-logs.store', $stocking) }}" class="space-y-6">
                    @csrf
                    @include('daily-logs._form', ['dailyLog' => null])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('stockings.daily-logs.index', $stocking) }}" class="text-sm text-ink/50 py-2">Batal</a>
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink leading-tight">
            Edit Input Harian — Kolam {{ $stocking->pond->kode_kolam }} — {{ $dailyLog->tgl->format('d M Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                    <div class="text-xs text-ink/50 uppercase">FR% (dari biomass terakhir)</div>
                    <div class="text-xl font-semibold text-ink">{{ $fr !== null ? number_format($fr, 2).'%' : '—' }}</div>
                </div>
                <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                    <div class="text-xs text-ink/50 uppercase">Kematian (kg, ekor × MBW)</div>
                    <div class="font-mono text-xl font-semibold text-ink">{{ $kematianKg !== null ? number_format($kematianKg, 2, ',', '.').' kg' : '—' }}</div>
                </div>
                <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                    <div class="text-xs text-ink/50 uppercase">Porsi Ancho (2% total pakan)</div>
                    <div class="text-xl font-semibold text-ink">{{ number_format($anchoPortionKg, 3) }} kg</div>
                </div>
            </div>

            <div class="bg-sand/40 p-6 rounded-2xl border border-lumpur/20">
                <form method="POST" action="{{ route('stockings.daily-logs.update', [$stocking, $dailyLog]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('daily-logs._form', ['dailyLog' => $dailyLog])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('stockings.daily-logs.index', $stocking) }}" class="text-sm text-ink/50 py-2">Batal</a>
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

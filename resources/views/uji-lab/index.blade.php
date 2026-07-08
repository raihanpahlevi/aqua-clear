<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Uji Lab" subtitle="Grafik parameter uji air mingguan per kolam. Ambang sesuai standar mutu PRD." />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if ($stockings->isEmpty())
            <x-card class="text-center py-12">
                <div class="w-12 h-12 rounded-xl bg-teal-mid/10 text-teal-mid flex items-center justify-center mx-auto mb-3">
                    <x-icon name="flask" class="w-6 h-6" />
                </div>
                <p class="text-ink/50 text-sm">Belum ada data uji air mingguan. Input dulu lewat menu Kualitas Air Mingguan di Hub Siklus.</p>
            </x-card>
        @else
            <form method="GET" action="{{ route('uji-lab.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="stocking" class="block text-[11px] font-semibold text-ink/50 uppercase tracking-wider mb-1">Kolam · Siklus</label>
                    <select id="stocking" name="stocking" class="border-lumpur/40 bg-paper text-ink text-sm focus:border-teal-mid focus:ring-teal-mid rounded-lg shadow-sm py-1.5">
                        @foreach ($stockings as $s)
                            <option value="{{ $s->id }}" @selected($selected && $selected->id === $s->id)>{{ $s->pond->kode_kolam }} · {{ $s->cycle->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-teal-mid rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep focus:outline-none focus:ring-2 focus:ring-teal-mid focus:ring-offset-2">Tampilkan</button>
                @if ($selected)
                    <a href="{{ route('stockings.water-quality-weekly.index', $selected) }}" class="text-sm text-teal-mid hover:underline py-2">Lihat data mentah →</a>
                @endif
            </form>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($charts as $c)
                    <x-line-chart
                        :title="$c['title']"
                        :points="$c['points']"
                        :threshold="$c['threshold']"
                        :unit="$c['unit']"
                        :decimals="$c['decimals']"
                    />
                @endforeach
            </div>

            <p class="text-xs text-ink/40">Garis putus-putus merah = ambang standar mutu (PRD 5.3). TOM, alkalinitas, dan kepadatan vibrio hijau tidak punya ambang pasti — dicatat sebagai referensi tren.</p>
        @endif

    </div>
</x-app-layout>

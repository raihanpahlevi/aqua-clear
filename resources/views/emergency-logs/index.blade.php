<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ink leading-tight">
                Emergency & Kesehatan — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-ink/50 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.emergency-logs.create', $stocking) }}" class="inline-flex items-center px-4 py-2 bg-kritis border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide shadow-sm hover:bg-kritis/90">
                    + Catat Kejadian
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-sehat/10 text-sehat rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="p-4 bg-sand/30 text-ink/70 text-sm rounded-lg">
                Flush-out berlaku bila DOC &lt; 30 dan kondisi kritis (SR turun tajam / serangan penyakit), lalu restocking.
            </div>

            <div class="bg-sand/40 rounded-2xl border border-lumpur/20 overflow-x-auto">
                <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                    <thead class="bg-sand/30">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Jenis</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Tindakan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-4 py-2 font-mono text-ink/80 font-medium">{{ $log->tgl->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-ink/70">{{ $log->jenis }}</td>
                                <td class="px-4 py-2 text-ink/70">{{ $log->tindakan ?? '—' }}</td>
                                <td class="px-4 py-2 text-ink/70">{{ $log->keputusan ? str_replace('_', ' ', $log->keputusan) : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-ink/50">Belum ada kejadian tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>

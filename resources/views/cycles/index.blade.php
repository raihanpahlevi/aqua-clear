<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight">
            {{ __('Siklus Budidaya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-sehat/10 text-sehat rounded-lg">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-kritis/10 text-kritis rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-sand/40 p-6 rounded-2xl border border-lumpur/20">
                <h3 class="font-semibold text-ink/80 mb-4">Tambah Siklus Baru</h3>
                <form method="POST" action="{{ route('cycles.store') }}" class="flex gap-3">
                    @csrf
                    <x-text-input name="nama" type="text" class="flex-1" placeholder="mis. Siklus 1 2026" required :value="old('nama')" />
                    <x-primary-button>Tambah</x-primary-button>
                </form>
                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
            </div>

            <div class="bg-sand/40 rounded-2xl border border-lumpur/20 overflow-hidden">
                <table class="min-w-full divide-y divide-lumpur/10">
                    <thead class="bg-sand/30">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Nama Siklus</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Jumlah Stocking</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @forelse ($cycles as $cycle)
                            <tr>
                                <td class="px-4 py-2 text-sm text-ink/80">{{ $cycle->nama }}</td>
                                <td class="px-4 py-2 text-sm font-mono text-ink/60">{{ $cycle->stockings_count }}</td>
                                <td class="px-4 py-2 text-sm text-right">
                                    <form method="POST" action="{{ route('cycles.destroy', $cycle) }}" onsubmit="return confirm('Hapus siklus ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-kritis hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-ink/50 text-sm">Belum ada siklus.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>

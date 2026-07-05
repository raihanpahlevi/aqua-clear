<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Siklus Budidaya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">Tambah Siklus Baru</h3>
                <form method="POST" action="{{ route('cycles.store') }}" class="flex gap-3">
                    @csrf
                    <x-text-input name="nama" type="text" class="flex-1" placeholder="mis. Siklus 1 2026" required :value="old('nama')" />
                    <x-primary-button>Tambah</x-primary-button>
                </form>
                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-900/40">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Nama Siklus</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Jumlah Stocking</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($cycles as $cycle)
                            <tr>
                                <td class="px-4 py-2 text-sm text-slate-700 dark:text-slate-300">{{ $cycle->nama }}</td>
                                <td class="px-4 py-2 text-sm text-slate-600 dark:text-slate-400">{{ $cycle->stockings_count }}</td>
                                <td class="px-4 py-2 text-sm text-right">
                                    <form method="POST" action="{{ route('cycles.destroy', $cycle) }}" onsubmit="return confirm('Hapus siklus ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 dark:text-rose-400 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400 text-sm">Belum ada siklus.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>

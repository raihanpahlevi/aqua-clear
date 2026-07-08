<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink leading-tight">
            {{ __('Mulai Siklus Baru') }} — Kolam {{ $pond->kode_kolam }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-sand/40 p-6 rounded-2xl border border-lumpur/20">
                <form method="POST" action="{{ route('ponds.stockings.store', $pond) }}" class="space-y-4">
                    @csrf
                    @include('stockings._form', ['stocking' => null])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('ponds.show', $pond) }}" class="text-sm text-ink/50 py-2">Batal</a>
                        <x-primary-button>Mulai Siklus</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

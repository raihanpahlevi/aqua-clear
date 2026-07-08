<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink leading-tight">
            Edit Sampling — Kolam {{ $stocking->pond->kode_kolam }} — {{ $sampling->tgl->format('d M Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-sand/40 p-6 rounded-2xl border border-lumpur/20">
                <form method="POST" action="{{ route('stockings.samplings.update', [$stocking, $sampling]) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('samplings._form', ['sampling' => $sampling])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('stockings.samplings.index', $stocking) }}" class="text-sm text-ink/50 py-2">Batal</a>
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

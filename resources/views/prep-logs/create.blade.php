<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Catat Progres Persiapan" subtitle="Kolam {{ $pond->kode_kolam }}" />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-2xl">
            <x-card>
                <form method="POST" action="{{ route('ponds.prep-logs.store', $pond) }}" class="space-y-4">
                    @csrf
                    @include('prep-logs._form', ['prepLog' => null])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('ponds.prep-logs.index', $pond) }}" class="text-sm text-ink/50 py-2">Batal</a>
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>

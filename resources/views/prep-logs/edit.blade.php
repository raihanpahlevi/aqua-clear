<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Edit Progres Persiapan" subtitle="Kolam {{ $pond->kode_kolam }}" />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-2xl">
            <x-card>
                <form method="POST" action="{{ route('ponds.prep-logs.update', [$pond, $prepLog]) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('prep-logs._form', ['prepLog' => $prepLog])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('ponds.prep-logs.index', $pond) }}" class="text-sm text-ink/50 py-2">Batal</a>
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>

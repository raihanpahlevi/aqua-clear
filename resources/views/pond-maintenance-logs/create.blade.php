<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Catat Manajemen Dasar Tambak" subtitle="Kolam {{ $stocking->pond->kode_kolam }}" />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-2xl">
            <x-card>
                <form method="POST" action="{{ route('stockings.pond-maintenance-logs.store', $stocking) }}" class="space-y-4">
                    @csrf
                    @include('pond-maintenance-logs._form', ['log' => null])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('stockings.pond-maintenance-logs.index', $stocking) }}" class="text-sm text-slate-500 dark:text-slate-400 py-2">Batal</a>
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>

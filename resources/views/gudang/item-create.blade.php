<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Daftarkan Barang Gudang" :back="route('gudang.index')" />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-xl">
            <x-card>
                <form method="POST" action="{{ route('gudang.item.store') }}" class="space-y-4">
                    @csrf
                    @include('gudang._item-form', ['item' => null])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('gudang.index') }}" class="text-sm text-ink/50 py-2">Batal</a>
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>

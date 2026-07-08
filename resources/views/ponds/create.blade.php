<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight">
            {{ __('Tambah Kolam') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-sand/40 p-6 rounded-2xl border border-lumpur/20">
                <form method="POST" action="{{ route('ponds.store') }}" class="space-y-4">
                    @csrf
                    @include('ponds._form', ['pond' => null])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('ponds.index') }}" class="text-sm text-ink/50 py-2">Batal</a>
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

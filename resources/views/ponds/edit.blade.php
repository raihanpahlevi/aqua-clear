<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight">
            {{ __('Edit Kolam') }} — {{ $pond->kode_kolam }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="p-4 bg-sehat/10 text-sehat rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-kritis/10 text-kritis rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-sand/40 p-6 rounded-2xl border border-lumpur/20">
                <form method="POST" action="{{ route('ponds.update', $pond) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('ponds._form', ['pond' => $pond])

                    <div class="flex justify-between items-center">
                        <form method="POST" action="{{ route('ponds.destroy', $pond) }}" onsubmit="return confirm('Yakin hapus kolam ini? Cuma bisa kalau belum ada riwayat siklus.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-kritis hover:underline">Hapus Kolam</button>
                        </form>

                        <div class="flex gap-3">
                            <a href="{{ route('ponds.index') }}" class="text-sm text-ink/50 py-2">Batal</a>
                            <x-primary-button>Simpan Perubahan</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

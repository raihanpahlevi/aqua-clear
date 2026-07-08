<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Gudang" subtitle="Stok pakan, obat & bahan — saldo otomatis berkurang dari pemakaian di kolam.">
            @role('operasional')
                <x-slot name="actions">
                    <a href="{{ route('gudang.item.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-paper border border-lumpur/40 rounded-lg font-semibold text-xs text-ink/80 uppercase tracking-wide shadow-sm hover:bg-sand/30">
                        <x-icon name="plus" class="w-3.5 h-3.5" /> Daftarkan Barang
                    </a>
                    <a href="{{ route('gudang.entry.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep">
                        <x-icon name="plus" class="w-3.5 h-3.5" /> Barang Masuk
                    </a>
                </x-slot>
            @endrole
        </x-page-header>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-5">

        @if (session('status'))
            <div class="p-4 bg-sehat/10 text-sehat rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="p-4 bg-sand/30 text-ink/60 text-xs rounded-lg">
            Kolom "Terpakai" dihitung dari pemakaian di menu <strong>Aplikasi Kimia & Biologi</strong> yang ditautkan ke barang gudang.
            Pemakaian tanpa tautan gudang tidak mengurangi saldo.
        </div>

        <x-card :padded="false">
            @if ($rows->isEmpty())
                <div class="p-10 text-center">
                    <div class="w-12 h-12 rounded-xl bg-teal-mid/10 text-teal-mid flex items-center justify-center mx-auto mb-3">
                        <x-icon name="gudang" class="w-6 h-6" />
                    </div>
                    <p class="text-ink/50 text-sm">Belum ada barang terdaftar. Mulai dengan "Daftarkan Barang", lalu catat stok lewat "Barang Masuk".</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                        <thead class="bg-sand/30">
                            <tr>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Barang</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Kategori</th>
                                <th class="px-5 py-2.5 text-right text-xs font-medium text-ink/50 uppercase tracking-wide">Masuk</th>
                                <th class="px-5 py-2.5 text-right text-xs font-medium text-ink/50 uppercase tracking-wide">Terpakai</th>
                                <th class="px-5 py-2.5 text-right text-xs font-medium text-ink/50 uppercase tracking-wide">Saldo</th>
                                <th class="px-5 py-2.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-lumpur/10">
                            @foreach ($rows as $row)
                                <tr class="hover:bg-sand/30 {{ $row['saldo'] < 0 ? 'bg-kritis/5' : '' }}">
                                    <td class="px-5 py-2.5 font-medium text-ink/80">{{ $row['item']->nama }}</td>
                                    <td class="px-5 py-2.5"><x-badge :tone="$row['item']->kategori === 'pakan' ? 'teal' : 'slate'">{{ $row['item']->kategori }}</x-badge></td>
                                    <td class="px-5 py-2.5 font-mono text-right text-ink/70">{{ number_format($row['masuk'], 1, ',', '.') }} {{ $row['item']->satuan }}</td>
                                    <td class="px-5 py-2.5 font-mono text-right text-ink/70">{{ number_format($row['terpakai'], 1, ',', '.') }} {{ $row['item']->satuan }}</td>
                                    <td class="px-5 py-2.5 font-mono text-right font-semibold {{ $row['saldo'] < 0 ? 'text-kritis' : 'text-ink' }}">
                                        {{ number_format($row['saldo'], 1, ',', '.') }} {{ $row['item']->satuan }}
                                        @if ($row['saldo'] < 0)
                                            <span class="block text-[10px] font-sans font-normal text-kritis">pemakaian melebihi stok tercatat</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-2.5 text-right">
                                        @role('operasional')
                                            <a href="{{ route('gudang.item.edit', $row['item']) }}" class="text-ink/40 hover:text-teal-mid">
                                                <x-icon name="pencil" class="w-4 h-4" />
                                            </a>
                                        @endrole
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>

    </div>
</x-app-layout>

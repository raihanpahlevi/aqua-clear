@php
    $old = fn ($field, $default = null) => old($field, $prepLog?->{$field} ?? $default);
    $checkedItems = old('checklist', $prepLog?->checklist ? array_fill_keys($prepLog->checklist, '1') : []);
    $selectedJenis = old('jenis', $prepLog?->jenis ?? 'tambak');
    $allStandardItems = array_merge(...array_values(\App\Http\Controllers\PrepLogController::CHECKLIST_ITEMS));
    $existingCustomItem = collect($prepLog?->checklist ?? [])->first(fn ($item) => ! in_array($item, $allStandardItems, true));
@endphp

<div>
    <x-input-label for="jenis" value="Jenis" />
    <select id="jenis" name="jenis" required onchange="document.querySelectorAll('[data-jenis]').forEach(el => el.classList.add('hidden')); document.querySelector('[data-jenis=' + this.value + ']').classList.remove('hidden');" class="mt-1 block w-full max-w-xs border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
        <option value="tambak" @selected($selectedJenis === 'tambak')>Persiapan Tambak</option>
        <option value="air" @selected($selectedJenis === 'air')>Persiapan Air</option>
    </select>
    <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="tgl" value="Tanggal" />
        <x-text-input id="tgl" name="tgl" type="date" class="mt-1 block w-full" :value="$old('tgl', $prepLog?->tgl?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
        <x-input-error :messages="$errors->get('tgl')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="cycle_id" value="Siklus (opsional)" />
        <select id="cycle_id" name="cycle_id" class="mt-1 block w-full border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
            <option value="">—</option>
            @foreach ($cycles as $cycle)
                <option value="{{ $cycle->id }}" @selected($old('cycle_id') == $cycle->id)>{{ $cycle->nama }}</option>
            @endforeach
        </select>
    </div>
</div>

<div>
    <x-input-label value="Checklist (bebas, centang yang sudah selesai)" />
    <div data-jenis="tambak" class="{{ $selectedJenis !== 'tambak' ? 'hidden' : '' }} mt-2 space-y-2">
        @foreach (\App\Http\Controllers\PrepLogController::CHECKLIST_ITEMS['tambak'] as $item)
            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                <input type="checkbox" name="checklist[{{ $item }}]" value="1" @checked(isset($checkedItems[$item])) class="rounded border-slate-300 dark:border-slate-600 text-teal-600 focus:ring-teal-500">
                {{ $item }}
            </label>
        @endforeach
    </div>
    <div data-jenis="air" class="{{ $selectedJenis !== 'air' ? 'hidden' : '' }} mt-2 space-y-2">
        @foreach (\App\Http\Controllers\PrepLogController::CHECKLIST_ITEMS['air'] as $item)
            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                <input type="checkbox" name="checklist[{{ $item }}]" value="1" @checked(isset($checkedItems[$item])) class="rounded border-slate-300 dark:border-slate-600 text-teal-600 focus:ring-teal-500">
                {{ $item }}
            </label>
        @endforeach
    </div>
</div>

<div>
    <x-input-label for="item_lainnya" value="Item Lainnya (opsional)" />
    <x-text-input id="item_lainnya" name="item_lainnya" type="text" class="mt-1 block w-full" :value="old('item_lainnya', $existingCustomItem)" placeholder="Checklist bebas — isi kalau ada item di luar daftar" />
</div>

<div>
    <x-input-label for="biaya" value="Biaya Desinfektan/Sterilisasi (Rp, opsional)" />
    <x-text-input id="biaya" name="biaya" type="number" step="0.01" class="mt-1 block w-full max-w-xs" :value="$old('biaya')" />
</div>

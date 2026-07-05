@php
    $old = fn ($field, $default = null) => old($field, $log?->{$field} ?? $default);
@endphp

<div>
    <x-input-label for="tgl" value="Tanggal" />
    <x-text-input id="tgl" name="tgl" type="date" class="mt-1 block w-full max-w-xs" :value="$old('tgl', $log?->tgl?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
    <x-input-error :messages="$errors->get('tgl')" class="mt-2" />
</div>

<div>
    <x-input-label for="siphon" value="Siphon Dilakukan?" />
    <select id="siphon" name="siphon" class="mt-1 block w-full max-w-xs border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">
        <option value="">—</option>
        <option value="1" @selected($old('siphon') == 1)>Ya</option>
        <option value="0" @selected($old('siphon') === '0' || $old('siphon') === false)>Tidak</option>
    </select>
</div>

<div>
    <x-input-label for="kondisi_lumpur" value="Kondisi Lumpur Dasar (kualitatif)" />
    <x-text-input id="kondisi_lumpur" name="kondisi_lumpur" type="text" class="mt-1 block w-full" :value="$old('kondisi_lumpur')" placeholder="mis. baik, sedang, buruk" />
    <p class="text-xs text-slate-400 mt-1">Acuan uji lab TOM bila tersedia (lihat Kualitas Air Mingguan).</p>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="jumlah_kincir" value="Jumlah Kincir Nyala" />
        <x-text-input id="jumlah_kincir" name="jumlah_kincir" type="number" class="mt-1 block w-full" :value="$old('jumlah_kincir')" />
    </div>
    <div>
        <x-input-label for="jam_nyala_kincir" value="Jam Nyala Kincir" />
        <x-text-input id="jam_nyala_kincir" name="jam_nyala_kincir" type="number" step="0.5" class="mt-1 block w-full" :value="$old('jam_nyala_kincir')" />
    </div>
</div>

<div>
    <x-input-label for="catatan" value="Catatan" />
    <textarea id="catatan" name="catatan" rows="2" class="mt-1 block w-full border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm">{{ $old('catatan') }}</textarea>
</div>

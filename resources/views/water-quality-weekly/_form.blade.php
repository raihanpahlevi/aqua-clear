@php
    $old = fn ($field, $default = null) => old($field, $waterQualityWeekly?->{$field} ?? $default);
@endphp

<div>
    <x-input-label for="tgl" value="Tanggal Uji" />
    <x-text-input id="tgl" name="tgl" type="date" class="mt-1 block w-full max-w-xs" :value="$old('tgl', $waterQualityWeekly?->tgl?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
    <x-input-error :messages="$errors->get('tgl')" class="mt-2" />
</div>

<div>
    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-2">Mingguan</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <x-input-label for="tan" value="TAN (<2 ppm)" />
            <x-text-input id="tan" name="tan" type="number" step="0.001" class="mt-1 block w-full" :value="$old('tan')" />
        </div>
        <div>
            <x-input-label for="ammonia" value="Ammonia (<0.1 ppm)" />
            <x-text-input id="ammonia" name="ammonia" type="number" step="0.001" class="mt-1 block w-full" :value="$old('ammonia')" />
        </div>
        <div>
            <x-input-label for="nitrit" value="Nitrit (<0.1 ppm)" />
            <x-text-input id="nitrit" name="nitrit" type="number" step="0.001" class="mt-1 block w-full" :value="$old('nitrit')" />
        </div>
        <div>
            <x-input-label for="nitrat" value="Nitrat (<50 ppm)" />
            <x-text-input id="nitrat" name="nitrat" type="number" step="0.001" class="mt-1 block w-full" :value="$old('nitrat')" />
        </div>
    </div>
</div>

<div>
    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-2">10 Hari Sekali (uji lab, tanpa ambang pasti)</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="tom" value="TOM" />
            <x-text-input id="tom" name="tom" type="number" step="0.001" class="mt-1 block w-full" :value="$old('tom')" />
        </div>
        <div>
            <x-input-label for="alkalinitas" value="Alkalinitas" />
            <x-text-input id="alkalinitas" name="alkalinitas" type="number" step="0.001" class="mt-1 block w-full" :value="$old('alkalinitas')" />
        </div>
        <div>
            <x-input-label for="fe" value="Fe" />
            <x-text-input id="fe" name="fe" type="number" step="0.001" class="mt-1 block w-full" :value="$old('fe')" />
        </div>
    </div>
</div>

<div>
    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-2">7 Hari Sekali — Vibrio & Bakteri</h3>
    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Rasio V/B dihitung otomatis; warning kalau &gt;10%. "Vibrio tinggi" khusus vibrio hijau, hitam, luminer.</p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <x-input-label for="vibrio_hijau" value="Vibrio Hijau" />
            <x-text-input id="vibrio_hijau" name="vibrio_hijau" type="number" step="0.01" class="mt-1 block w-full" :value="$old('vibrio_hijau')" />
        </div>
        <div>
            <x-input-label for="vibrio_hitam" value="Vibrio Hitam" />
            <x-text-input id="vibrio_hitam" name="vibrio_hitam" type="number" step="0.01" class="mt-1 block w-full" :value="$old('vibrio_hitam')" />
        </div>
        <div>
            <x-input-label for="vibrio_luminer" value="Vibrio Luminer" />
            <x-text-input id="vibrio_luminer" name="vibrio_luminer" type="number" step="0.01" class="mt-1 block w-full" :value="$old('vibrio_luminer')" />
        </div>
        <div>
            <x-input-label for="total_bakteri" value="Total Bakteri" />
            <x-text-input id="total_bakteri" name="total_bakteri" type="number" step="0.01" class="mt-1 block w-full" :value="$old('total_bakteri')" />
        </div>
    </div>
</div>

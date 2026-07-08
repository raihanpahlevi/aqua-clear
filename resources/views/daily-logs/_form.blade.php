@php
    $old = fn ($field, $default = null) => old($field, $dailyLog?->{$field} ?? $default);
    $anchoOptions = ['habis' => 'Habis', 'sisa_sedikit' => 'Sisa Sedikit', 'sisa_banyak' => 'Sisa Banyak'];
@endphp

<div>
    <x-input-label for="tgl" value="Tanggal" />
    <x-text-input id="tgl" name="tgl" type="date" class="mt-1 block w-full max-w-xs" :value="$old('tgl', $dailyLog?->tgl?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
    <x-input-error :messages="$errors->get('tgl')" class="mt-2" />
</div>

<div>
    <h3 class="font-semibold text-ink/80 mb-2">Pakan & Ancho (4x sehari)</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach (['07' => '07.00', '11' => '11.00', '15' => '15.00', '19' => '19.00'] as $jam => $label)
            <div class="p-3 border border-lumpur/20 rounded-lg space-y-2">
                <div class="text-xs font-semibold text-ink/50">{{ $label }}</div>
                <div>
                    <x-input-label for="pakan_{{ $jam }}_kg" value="Pakan (kg)" />
                    <x-text-input id="pakan_{{ $jam }}_kg" name="pakan_{{ $jam }}_kg" type="number" step="0.01" class="mt-1 block w-full" :value="$old('pakan_'.$jam.'_kg')" />
                </div>
                <div>
                    <x-input-label for="ancho_{{ $jam }}" value="Ancho (+2 jam)" />
                    <select id="ancho_{{ $jam }}" name="ancho_{{ $jam }}" class="mt-1 block w-full text-sm border-lumpur/40 bg-paper text-ink focus:border-teal-mid focus:ring-teal-mid rounded-lg shadow-sm">
                        <option value="">—</option>
                        @foreach ($anchoOptions as $value => $label2)
                            <option value="{{ $value }}" @selected($old('ancho_'.$jam) === $value)>{{ $label2 }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-3 max-w-xs">
        <x-input-label for="kode_pakan" value="Kode Pakan (fase DOC)" />
        <x-text-input id="kode_pakan" name="kode_pakan" type="text" class="mt-1 block w-full" :value="$old('kode_pakan')" placeholder="mis. #0, 3M" />
    </div>
</div>

<div>
    <h3 class="font-semibold text-ink/80 mb-2">Kualitas Air Harian</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <x-input-label for="do_pagi" value="DO Pagi (>4 ppm)" />
            <x-text-input id="do_pagi" name="do_pagi" type="number" step="0.01" class="mt-1 block w-full" :value="$old('do_pagi')" />
        </div>
        <div>
            <x-input-label for="do_sore" value="DO Sore (>4 ppm)" />
            <x-text-input id="do_sore" name="do_sore" type="number" step="0.01" class="mt-1 block w-full" :value="$old('do_sore')" />
        </div>
        <div>
            <x-input-label for="ph_pagi" value="pH Pagi (7.5-8.5)" />
            <x-text-input id="ph_pagi" name="ph_pagi" type="number" step="0.01" class="mt-1 block w-full" :value="$old('ph_pagi')" />
        </div>
        <div>
            <x-input-label for="ph_sore" value="pH Sore (7.5-8.5)" />
            <x-text-input id="ph_sore" name="ph_sore" type="number" step="0.01" class="mt-1 block w-full" :value="$old('ph_sore')" />
        </div>
        <div>
            <x-input-label for="suhu_pagi" value="Suhu Pagi (28-32°C)" />
            <x-text-input id="suhu_pagi" name="suhu_pagi" type="number" step="0.01" class="mt-1 block w-full" :value="$old('suhu_pagi')" />
        </div>
        <div>
            <x-input-label for="suhu_sore" value="Suhu Sore (28-32°C)" />
            <x-text-input id="suhu_sore" name="suhu_sore" type="number" step="0.01" class="mt-1 block w-full" :value="$old('suhu_sore')" />
        </div>
        <div>
            <x-input-label for="salinitas" value="Salinitas (25-30 ppt)" />
            <x-text-input id="salinitas" name="salinitas" type="number" step="0.01" class="mt-1 block w-full" :value="$old('salinitas')" />
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="mortalitas" value="Mortalitas Observasi (ekor)" />
        <x-text-input id="mortalitas" name="mortalitas" type="number" class="mt-1 block w-full" :value="$old('mortalitas')" />
        <p class="text-xs text-ink/50 mt-1">Sistem otomatis kali 2 untuk laporan (kanibalisme udang).</p>
    </div>
    <div>
        <x-input-label for="catatan" value="Catatan" />
        <textarea id="catatan" name="catatan" rows="2" class="mt-1 block w-full border-lumpur/40 bg-paper text-ink focus:border-teal-mid focus:ring-teal-mid rounded-lg shadow-sm">{{ $old('catatan') }}</textarea>
    </div>
</div>

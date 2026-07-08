@props([
    'title',
    'points' => [],        // list<['label' => string, 'value' => float]>
    'threshold' => null,   // garis ambang (opsional)
    'thresholdLabel' => null,
    'unit' => '',
    'decimals' => 2,
])

@php
    // Murni transformasi koordinat buat SVG — rumus bisnis tetap di service.
    $vals = array_column($points, 'value');
    $n = count($vals);

    $latest = $n > 0 ? end($vals) : null;
    $poly = '';
    $thresholdY = null;

    if ($n >= 2) {
        $min = min($vals);
        $max = max($vals);
        if ($threshold !== null) {
            $min = min($min, $threshold);
            $max = max($max, $threshold);
        }
        $range = $max - $min;
        $stepX = 100 / ($n - 1);
        $mapY = fn (float $v): float => $range > 0 ? round(34 - (($v - $min) / $range) * 28, 1) : 20.0;

        $pts = [];
        foreach ($vals as $i => $v) {
            $pts[] = round($i * $stepX, 1).','.$mapY($v);
        }
        $poly = implode(' ', $pts);
        $thresholdY = $threshold !== null ? $mapY((float) $threshold) : null;
    }

    $latestMelanggar = $threshold !== null && $latest !== null && $latest > $threshold;
@endphp

<div {{ $attributes->merge(['class' => 'bg-sand/40 rounded-2xl border border-lumpur/20 p-4']) }}>
    <div class="flex items-baseline justify-between gap-2">
        <div class="text-[11px] font-semibold text-ink/50 uppercase tracking-wider">{{ $title }}</div>
        <div class="font-mono text-sm font-semibold {{ $latestMelanggar ? 'text-kritis' : 'text-ink' }}">
            {{ $latest !== null ? number_format($latest, $decimals, ',', '.') : '—' }}<span class="text-ink/40 font-normal"> {{ $unit }}</span>
        </div>
    </div>

    @if ($poly !== '')
        <svg viewBox="0 0 100 40" class="w-full h-16 mt-2 text-teal-mid" preserveAspectRatio="none" fill="none" aria-hidden="true">
            @if ($thresholdY !== null)
                <line x1="0" y1="{{ $thresholdY }}" x2="100" y2="{{ $thresholdY }}"
                      stroke="currentColor" stroke-width="1" stroke-dasharray="3 3"
                      vector-effect="non-scaling-stroke" class="text-kritis/60" />
            @endif
            <polyline points="{{ $poly }}" stroke="currentColor" stroke-width="2"
                      vector-effect="non-scaling-stroke" stroke-linejoin="round" stroke-linecap="round" />
        </svg>
        <div class="flex justify-between mt-1 text-[10px] font-mono text-ink/40">
            <span>{{ $points[0]['label'] }}</span>
            @if ($threshold !== null)
                <span class="text-kritis/70">ambang {{ $thresholdLabel ?? number_format($threshold, $decimals, ',', '.') }}</span>
            @endif
            <span>{{ $points[$n - 1]['label'] }}</span>
        </div>
    @else
        <div class="mt-2 py-4 text-center text-xs text-ink/40">Belum cukup data (min. 2 titik).</div>
    @endif
</div>

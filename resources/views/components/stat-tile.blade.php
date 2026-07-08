@props(['icon' => null, 'label', 'value', 'sublabel' => null, 'tone' => 'teal', 'size' => 'md'])

@php
$toneClasses = match ($tone) {
    'rose' => 'bg-kritis/10 text-kritis',
    'amber' => 'bg-perhatian/10 text-perhatian',
    'emerald' => 'bg-sehat/10 text-sehat',
    'slate' => 'bg-lumpur/10 text-lumpur',
    default => 'bg-teal-mid/10 text-teal-mid',
};

$borderClasses = match ($tone) {
    'rose' => 'border-l-4 border-l-kritis',
    'amber' => 'border-l-4 border-l-perhatian',
    default => '',
};

$valueSize = $size === 'lg' ? 'text-4xl' : 'text-2xl';
@endphp

<x-card {{ $attributes->merge(['class' => 'flex items-start gap-3.5 '.$borderClasses]) }}>
    @if ($icon)
        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $toneClasses }}">
            <x-icon :name="$icon" class="w-5 h-5" />
        </div>
    @endif
    <div class="min-w-0">
        <div class="text-xs font-semibold text-ink/50 uppercase tracking-wider">{{ $label }}</div>
        <div class="font-mono font-semibold {{ $valueSize }} text-ink mt-1 whitespace-nowrap">{{ $value }}</div>
        @if ($sublabel)
            <div class="text-xs text-ink/40 mt-1 font-mono">{{ $sublabel }}</div>
        @endif
    </div>
</x-card>

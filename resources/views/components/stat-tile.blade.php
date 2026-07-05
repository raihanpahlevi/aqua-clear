@props(['icon' => null, 'label', 'value', 'sublabel' => null, 'tone' => 'teal'])

@php
$toneClasses = match ($tone) {
    'rose' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
    'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
    'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
    'slate' => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
    default => 'bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400',
};
@endphp

<x-card class="flex items-start gap-3.5">
    @if ($icon)
        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $toneClasses }}">
            <x-icon :name="$icon" class="w-5 h-5" />
        </div>
    @endif
    <div class="min-w-0">
        <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">{{ $label }}</div>
        <div class="text-xl font-bold text-slate-800 dark:text-slate-100 mt-0.5 whitespace-nowrap">{{ $value }}</div>
        @if ($sublabel)
            <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $sublabel }}</div>
        @endif
    </div>
</x-card>

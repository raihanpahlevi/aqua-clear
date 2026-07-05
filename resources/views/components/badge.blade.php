@props(['tone' => 'slate'])

@php
$toneClasses = match ($tone) {
    'teal' => 'bg-teal-50 text-teal-700 dark:bg-teal-500/10 dark:text-teal-400',
    'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
    'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
    'rose' => 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
    'sky' => 'bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-400',
    default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold capitalize '.$toneClasses]) }}>
    {{ $slot }}
</span>

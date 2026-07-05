@props(['padded' => true])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5 '.($padded ? 'p-5' : '')]) }}>
    {{ $slot }}
</div>

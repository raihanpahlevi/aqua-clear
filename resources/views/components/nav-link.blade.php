@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold bg-sand/15 text-sand transition duration-150 ease-in-out'
            : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-sand/60 hover:bg-sand/10 hover:text-sand transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

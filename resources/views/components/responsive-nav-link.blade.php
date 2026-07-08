@props(['active'])

@php
// Selaras dengan nav-link (dipakai di atas sidebar teal-deep gelap).
$classes = ($active ?? false)
            ? 'flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-base font-semibold bg-sand/15 text-sand transition duration-150 ease-in-out'
            : 'flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-base font-medium text-sand/60 hover:bg-sand/10 hover:text-sand transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

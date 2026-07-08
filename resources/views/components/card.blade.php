@props(['padded' => true])

<div {{ $attributes->merge(['class' => 'bg-sand/40 rounded-2xl border border-lumpur/20 '.($padded ? 'p-5' : '')]) }}>
    {{ $slot }}
</div>

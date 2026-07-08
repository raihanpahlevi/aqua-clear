@props(['tone' => 'slate'])

@php
// Nama tone lama (teal/amber/emerald/rose/sky/slate) dipertahankan sebagai alias
// supaya view pemanggil tidak perlu diubah — warnanya dipetakan ke design system.
$toneClasses = match ($tone) {
    'teal' => 'bg-teal-mid/10 text-teal-mid',
    'amber', 'perhatian' => 'bg-perhatian/15 text-perhatian',
    'emerald', 'sehat' => 'bg-sehat/10 text-sehat',
    'rose', 'kritis' => 'bg-kritis/10 text-kritis',
    'sky' => 'bg-teal-mid/10 text-teal-mid',
    default => 'bg-lumpur/10 text-lumpur',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold capitalize '.$toneClasses]) }}>
    {{ $slot }}
</span>

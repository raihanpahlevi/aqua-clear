@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-sehat']) }}>
        {{ $status }}
    </div>
@endif

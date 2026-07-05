@props(['title', 'subtitle' => null, 'back' => null])

<div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
    <div>
        @if ($back)
            <a href="{{ $back }}" class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 mb-1.5">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                Kembali
            </a>
        @endif
        <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-2 shrink-0">
            {{ $actions }}
        </div>
    @endisset
</div>

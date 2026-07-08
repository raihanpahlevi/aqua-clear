<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center gap-1.5 px-4 py-2.5 bg-paper border border-lumpur/40 rounded-lg font-semibold text-xs text-ink/80 uppercase tracking-wide shadow-sm hover:bg-sand/40 focus:outline-none focus:ring-2 focus:ring-teal-mid focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

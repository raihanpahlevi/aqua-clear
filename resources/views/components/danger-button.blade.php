<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-1.5 px-4 py-2.5 bg-kritis border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide shadow-sm hover:bg-kritis/90 active:bg-kritis focus:outline-none focus:ring-2 focus:ring-kritis focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

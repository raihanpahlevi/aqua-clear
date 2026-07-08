<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep focus:bg-teal-deep active:bg-teal-deep focus:outline-none focus:ring-2 focus:ring-teal-mid focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

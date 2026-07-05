<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-1.5 px-4 py-2.5 bg-rose-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wide shadow-sm hover:bg-rose-700 active:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-lumpur/40 bg-paper text-ink placeholder:text-ink/30 focus:border-teal-mid focus:ring-teal-mid rounded-lg shadow-sm text-sm']) }}>

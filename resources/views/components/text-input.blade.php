@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full glass-input text-white placeholder-slate-500 rounded-2xl px-5 py-4 text-sm focus:outline-none transition-all duration-200']) }}>

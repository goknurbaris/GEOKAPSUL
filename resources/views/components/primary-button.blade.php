<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold py-4 px-6 rounded-2xl shadow-xl shadow-indigo-600/20 active:scale-[0.98] transition-all text-sm uppercase tracking-widest']) }}>
    {{ $slot }}
</button>

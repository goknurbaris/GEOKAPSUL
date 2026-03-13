<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Şifremi Unuttum - GeoKapsül</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-200 min-h-screen flex items-center justify-center p-6 relative overflow-hidden">

    <div class="absolute inset-0 opacity-5" style="background-image: url('https://unpkg.com/leaflet@1.9.4/dist/images/layers-2x.png'); background-size: cover; background-position: center; filter: grayscale(100%);"></div>

    <div class="relative z-10 w-full max-w-md bg-slate-800 p-10 rounded-3xl shadow-2xl border border-slate-700">

        <div class="mb-8 text-center">
            <span class="font-black text-5xl mb-4 block drop-shadow-lg">🕵️‍♂️</span>
            <h2 class="font-black text-slate-100 text-2xl mb-2 uppercase tracking-widest">Kayıp Anahtar</h2>
            <p class="text-slate-400 text-xs font-medium leading-relaxed">Parolanı mı unuttun? Hiç sorun değil. Sisteme kayıtlı e-posta adresini gir, sana dijital kilitleri kırman için yeni bir şifre sıfırlama bağlantısı gönderelim.</p>
        </div>

        <x-auth-session-status class="mb-6 text-emerald-400 border border-emerald-900/50 bg-emerald-900/20 p-3 rounded-xl font-bold text-xs text-center uppercase tracking-widest" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <div class="relative group">
                <label class="font-bold text-slate-400 text-[10px] uppercase tracking-widest mb-2 block">Kayıtlı E-Posta Adresi</label>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none mt-6">
                    <svg class="w-5 h-5 text-slate-500 group-focus-within:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <input type="email" name="email" :value="old('email')" required autofocus class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-inner pl-10 resize-none transition-all">

                <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-500 text-[10px] uppercase tracking-widest font-black" />
            </div>

            <div class="flex flex-col gap-4 mt-2">
                <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-xl font-black uppercase tracking-widest text-xs transition-all shadow-md hover:bg-indigo-700 active:scale-95 border-2 border-indigo-600">
                    🔗 Bağlantı Gönder
                </button>

                <a href="{{ route('login') }}" class="text-center text-[10px] text-slate-500 hover:text-indigo-400 font-black uppercase tracking-widest transition-colors">
                    ← Giriş Kapısına Dön
                </a>
            </div>
        </form>
    </div>

</body>
</html>

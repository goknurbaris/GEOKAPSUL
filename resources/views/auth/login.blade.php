<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş Yap - GeoKapsül</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-200 min-h-screen flex flex-col md:flex-row">

    <div class="w-full md:w-1/2 min-h-[30vh] md:min-h-screen bg-slate-950 p-8 flex flex-col justify-center items-center text-center border-b md:border-b-0 md:border-r border-slate-800 shadow-xl relative overflow-hidden"
         style="background-image: url('https://unpkg.com/leaflet@1.9.4/dist/images/layers-2x.png'); background-size: contain; background-repeat: repeat-x;">
        <div class="absolute inset-0 bg-slate-950/80 md:bg-transparent"></div> <div class="relative z-10">
            <span class="font-black text-4xl md:text-6xl mb-4 md:mb-6 text-indigo-500 drop-shadow-lg block">💎</span>
            <h2 class="font-black text-slate-100 text-2xl md:text-4xl mb-2 md:mb-3 uppercase tracking-widest">GeoKapsül</h2>
            <p class="text-slate-400 font-medium text-xs md:text-sm max-w-sm hidden sm:block">"Dijital izlerini takip etmeye hazır mısın? Zaman Makinesini çalıştırmak için giriş yapmalısın, dedektif."</p>
        </div>
    </div>

    <div class="w-full md:w-1/2 bg-slate-900 p-6 sm:p-10 md:p-16 flex flex-col justify-center items-center">
        <div class="bg-slate-800 p-6 sm:p-10 rounded-3xl shadow-2xl border border-slate-700 w-full max-w-md group">

            <div class="mb-8 md:mb-10 text-center">
                <span class="font-black text-lg md:text-xl text-indigo-500 uppercase tracking-widest block mb-1">🔑 Giriş Kapısı</span>
                <h2 class="font-black text-slate-100 text-2xl md:text-3xl mb-1 uppercase tracking-wider">Hoş Geldiniz</h2>
                <p class="text-slate-400 font-medium text-xs md:text-sm">Lütfen dijital anahtarınızı kullanın.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4 md:gap-5">
                @csrf
                <div class="relative">
                    <label class="font-bold text-slate-400 text-[10px] md:text-xs uppercase tracking-wider mb-1 block">E-Posta Adresi</label>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none mt-5 md:mt-6">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-slate-500 group-focus-within:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                    </div>
                    <input type="email" name="email" required autofocus class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-slate-200 focus:ring-2 focus:ring-indigo-500 pl-9 md:pl-10">
                </div>

                <div class="relative">
                    <div class="flex justify-between items-center mb-1">
                        <label class="font-bold text-slate-400 text-[10px] md:text-xs uppercase tracking-wider block">Şifre</label>
                        @if (Route::has('password.request'))
                            <a class="text-[10px] md:text-xs text-indigo-400 hover:text-indigo-300 font-bold uppercase tracking-widest transition-colors" href="{{ route('password.request') }}">Şifremi mi unuttun?</a>
                        @endif
                    </div>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none mt-5 md:mt-6">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-slate-500 group-focus-within:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" name="password" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-slate-200 focus:ring-2 focus:ring-indigo-500 pl-9 md:pl-10">
                </div>

                <div class="flex items-center gap-2 mt-1">
                    <input id="remember_me" type="checkbox" class="w-4 h-4 text-indigo-600 bg-slate-900 border-slate-700 rounded focus:ring-indigo-500" name="remember">
                    <label for="remember_me" class="text-xs md:text-sm text-slate-400 font-medium">Beni Hatırla</label>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white p-3 md:py-3.5 rounded-xl font-black uppercase text-xs md:text-sm transition-all shadow-md hover:bg-indigo-700 border-2 border-indigo-600 mt-2">✅ Giriş Kapısını Aç</button>
            </form>

            <div class="mt-6 md:mt-8 text-center border-t border-slate-700 pt-5 md:pt-6">
                <a href="{{ route('register') }}" class="inline-block w-full bg-slate-700 hover:bg-slate-600 text-slate-100 px-5 py-3 rounded-xl text-[10px] md:text-xs font-black uppercase tracking-widest transition-all">Yeni Bir Kimlik Oluştur ✨</a>
            </div>
        </div>
    </div>
</body>
</html>

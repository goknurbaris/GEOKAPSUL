<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profilim - GeoKapsül</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-200 min-h-screen">

    <nav class="bg-slate-950 border-b border-slate-800 px-4 sm:px-6 py-4 flex flex-wrap justify-between items-center gap-4 shadow-lg sticky top-0 z-50">
        <div class="flex items-center gap-2 sm:gap-4">
            <span class="font-black text-lg sm:text-xl text-indigo-500 tracking-widest uppercase">💎 GeoKapsül</span>
        </div>

        <div class="flex flex-wrap items-center gap-3 sm:gap-6">
            <a href="{{ url('/') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 sm:px-5 py-2 rounded-xl text-[10px] sm:text-xs font-black uppercase tracking-widest transition-all shadow-md">🗺️ Harita</a>
            <a href="{{ url('/dashboard') }}" class="text-slate-500 hover:text-indigo-400 text-[10px] sm:text-xs font-black uppercase tracking-widest transition-colors">Arşivim</a>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="text-rose-600 hover:text-rose-500 text-[10px] sm:text-xs font-black uppercase tracking-widest transition-colors">Çıkış</button>
            </form>
        </div>
    </nav>

    <div class="py-8 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

            <div class="bg-slate-800 p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-xl border border-slate-700">
                <header class="mb-5 sm:mb-6">
                    <h2 class="text-base sm:text-lg font-black text-slate-100 uppercase tracking-widest">👤 Profil Bilgileri</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-400">Hesabının profil bilgilerini güncelle.</p>
                </header>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf @method('patch')
                    <div>
                        <label class="font-bold text-slate-400 text-[10px] sm:text-xs uppercase tracking-wider mb-2 block">İsim</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-slate-200 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="font-bold text-slate-400 text-[10px] sm:text-xs uppercase tracking-wider mb-2 block">E-Posta</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-slate-200 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit" class="w-full sm:w-auto text-center bg-indigo-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-md hover:bg-indigo-700">✅ Kaydet</button>
                    </div>
                </form>
            </div>

            <div class="bg-slate-900 p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-xl border border-rose-900/50 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(45deg, #e11d48 0, #e11d48 2px, transparent 2px, transparent 8px);"></div>
                <header class="mb-5 sm:mb-6 relative z-10">
                    <h2 class="text-base sm:text-lg font-black text-rose-500 uppercase tracking-widest">🚨 Tehlikeli Bölge</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-400">Bu işlem geri alınamaz.</p>
                </header>

                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Emin misin? Tüm anıların silinecek!');" class="relative z-10">
                    @csrf @method('delete')
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                        <input type="password" name="password" required placeholder="Onaylamak için şifreni gir" class="w-full sm:flex-1 bg-slate-800 border border-rose-900/50 rounded-xl p-3 text-sm text-slate-200 focus:ring-2 focus:ring-rose-500">
                        <button type="submit" class="w-full sm:w-auto bg-rose-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-md hover:bg-rose-700 flex items-center justify-center gap-2">
                            🗑️ Hesabımı Sil
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</body>
</html>

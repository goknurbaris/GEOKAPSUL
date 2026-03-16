<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panelim - GeoKapsül</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-200 min-h-screen">

    <nav class="bg-slate-950 border-b border-slate-800 px-6 py-4 flex justify-between items-center shadow-lg sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <span class="font-black text-xl text-indigo-500 tracking-widest uppercase">💎 GeoKapsül</span>
        </div>

        <div class="flex items-center gap-6">
            <span class="text-sm font-bold text-slate-400 hidden sm:inline-block">{{ auth()->user()->name }}</span>

            <a href="{{ url('/') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-md hover:shadow-indigo-500/30 flex items-center gap-2 border border-indigo-500">
                🗺️ Haritaya Dön
            </a>

            <a href="{{ route('profile.edit') }}" class="text-slate-500 hover:text-indigo-400 text-xs font-black uppercase tracking-widest transition-colors">Profil</a>

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="text-rose-600 hover:text-rose-500 text-xs font-black uppercase tracking-widest transition-colors">
                    Çıkış
                </button>
            </form>
        </div>
    </nav>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($myCapsules as $capsule)
                    <div x-data="{ isEditing: false }" class="bg-slate-800 border border-slate-700 rounded-2xl shadow-lg hover:shadow-indigo-500/20 transition-all overflow-hidden flex flex-col group relative">

                        <div x-show="!isEditing" class="flex flex-col h-full">

                            <div class="absolute top-2 right-2 flex gap-1 z-10">
                                @if($capsule->pin_code)
                                    <span class="bg-rose-600 text-white text-[9px] font-black px-2 py-1 rounded-md shadow-md uppercase tracking-widest">🔒 ŞİFRELİ</span>
                                @endif
                                @if($capsule->unlock_date)
                                    <span class="bg-amber-500 text-white text-[9px] font-black px-2 py-1 rounded-md shadow-md uppercase tracking-widest">⏳ ZAMAN KİLİDİ</span>
                                @endif
                            </div>

                            <div class="relative w-full h-40 bg-slate-900 flex items-center justify-center border-b border-slate-700 overflow-hidden">
                                @if($capsule->image)
                                    <img src="{{ asset('storage/' . $capsule->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Kapsül Anısı">
                                @else
                                    <span class="text-slate-700 text-4xl">📸</span>
                                @endif
                            </div>

                            <div class="p-5 flex-1 flex flex-col">
                                <p class="text-slate-300 font-bold italic mb-4 line-clamp-3">"{{ $capsule->message }}"</p>

                                <div class="mt-auto pt-4 border-t border-slate-700 text-[10px] text-slate-500 font-bold tracking-wider space-y-2 uppercase">
                                    <p class="flex items-center gap-2"><span class="text-indigo-500 text-sm">📍</span> {{ number_format($capsule->latitude, 4) }}, {{ number_format($capsule->longitude, 4) }}</p>
                                    <p class="flex items-center gap-2"><span class="text-emerald-500 text-sm">🕒</span> {{ $capsule->created_at->format('d/m/Y - H:i') }}</p>

                                    @if($capsule->unlock_date)
                                        <p class="flex items-center gap-2"><span class="text-amber-500 text-sm">⏳</span> Kırılma: {{ \Carbon\Carbon::parse($capsule->unlock_date)->format('d.m.Y') }}</p>
                                    @endif

                                    @if($capsule->pin_code)
                                        <p class="flex items-center gap-2 text-rose-400"><span class="text-rose-500 text-sm">🔑</span> PIN: {{ $capsule->pin_code }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-slate-900 px-5 py-3 border-t border-slate-700 flex justify-between items-center">
                                <button @click="isEditing = true" class="text-indigo-400 hover:text-indigo-300 text-xs font-black uppercase tracking-widest flex items-center gap-1 transition-colors">
                                    ✏️ DÜZENLE
                                </button>

                                <form action="{{ route('capsule.destroy', $capsule->id) }}" method="POST" onsubmit="return confirm('Bu anıyı sonsuza dek silmek istiyor musun?');" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-400 text-xs font-black uppercase tracking-widest flex items-center gap-1 transition-colors">
                                        🗑️ SİL
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div x-cloak x-show="isEditing" class="p-5 flex flex-col h-full justify-center bg-slate-800">
                            <form action="{{ route('capsule.update', $capsule->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3 h-full m-0">
                                @csrf
                                @method('PATCH')

                                <div class="flex-1">
                                    <label class="text-[9px] text-slate-400 uppercase tracking-widest font-bold mb-1 block">Yazıyı Düzenle</label>
                                    <textarea name="message" required rows="2" class="w-full bg-slate-900 border border-slate-600 rounded-xl p-3 text-xs text-slate-200 focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ $capsule->message }}</textarea>

                                    <div class="flex gap-2 mt-2">
                                        <div class="flex-1">
                                            <label class="text-[9px] text-slate-400 uppercase tracking-widest font-bold mb-1 block">Tarih Kilidi</label>
                                            <input type="date" name="unlock_date" value="{{ $capsule->unlock_date }}" class="w-full bg-slate-900 border border-slate-600 rounded-xl p-2 text-xs text-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div class="w-1/3">
                                            <label class="text-[9px] text-slate-400 uppercase tracking-widest font-bold mb-1 block">PIN</label>
                                            <input type="text" name="pin_code" value="{{ $capsule->pin_code }}" maxlength="4" placeholder="Yok" class="w-full bg-slate-900 border border-slate-600 rounded-xl p-2 text-xs text-center tracking-[0.2em] text-slate-200 focus:ring-indigo-500 focus:border-indigo-500" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                    </div>

                                    <label class="text-[9px] text-slate-400 uppercase tracking-widest font-bold mt-3 mb-1 block">Yeni Fotoğraf (İsteğe Bağlı)</label>
                                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-indigo-900 file:text-indigo-300 hover:file:bg-indigo-800 cursor-pointer">
                                </div>

                                <div class="flex gap-2 mt-auto">
                                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">
                                        ✅ KAYDET
                                    </button>
                                    <button @click="isEditing = false" type="button" class="flex-1 bg-slate-600 hover:bg-slate-500 text-white py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">
                                        İPTAL
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full py-20 flex flex-col items-center justify-center text-center">
                        <span class="text-6xl mb-4">🌌</span>
                        <h3 class="text-2xl font-black text-slate-200 mb-2">Henüz Hiç İz Bırakmadın</h3>
                        <p class="text-slate-400 font-medium max-w-md">Karanlık haritaya geri dön ve ilk kapsülünü göm!</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</body>
</html>

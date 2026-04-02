<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="theme-color" content="#020617">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panelim - GeoKapsül</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }

        /* Glass effect */
        .glass {
            background: rgba(14, 23, 42, 0.8);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
        }

        /* Card hover effect */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(99, 102, 241, 0.25);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Skeleton loading animation */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        /* Button loading spinner */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-200 min-h-screen">

    {{-- Modern Navbar --}}
    <nav class="glass border-b border-white/5 px-4 sm:px-6 py-3 sm:py-4 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2 sm:gap-3 group">
                <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">💎</span>
                <span class="font-black text-lg sm:text-xl gradient-text tracking-tight hidden sm:inline">GeoKapsül</span>
            </a>

            {{-- Navigation --}}
            <div class="flex items-center gap-2 sm:gap-4">
                {{-- User Badge (Desktop) --}}
                <div class="hidden md:flex items-center gap-2 bg-slate-800/50 px-4 py-2 rounded-xl border border-white/5">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <span class="text-sm font-semibold text-slate-300">{{ auth()->user()->name }}</span>
                </div>

                {{-- Map Button --}}
                <a href="{{ url('/') }}" class="bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold transition-all shadow-lg hover:shadow-indigo-500/25 flex items-center gap-2 active:scale-95">
                    <span class="text-base sm:text-lg">🗺️</span>
                    <span class="hidden sm:inline">Haritaya Dön</span>
                </a>

                {{-- Profile Link --}}
                <a href="{{ route('profile.edit') }}" class="p-2.5 sm:p-3 bg-slate-800/50 hover:bg-slate-700/50 rounded-xl sm:rounded-2xl transition-all border border-white/5 hover:border-white/10">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </a>

                {{-- Logout Button --}}
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="p-2.5 sm:p-3 bg-rose-500/10 hover:bg-rose-500/20 rounded-xl sm:rounded-2xl transition-all border border-rose-500/20 hover:border-rose-500/30 group">
                        <svg class="w-5 h-5 text-rose-400 group-hover:text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Toast Notifications --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed top-20 right-4 z-50 bg-gradient-to-r from-emerald-600 to-cyan-600 text-white px-6 py-4 rounded-2xl shadow-2xl font-bold text-sm flex items-center gap-3 max-w-sm">
            <span class="text-xl">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show"
             class="fixed top-20 right-4 z-50 bg-gradient-to-r from-rose-600 to-pink-600 text-white px-6 py-4 rounded-2xl shadow-2xl font-bold text-sm max-w-sm">
            <div class="flex items-center gap-3 mb-2">
                <span class="text-xl">⚠️</span>
                <span>Hata oluştu</span>
                <button @click="show = false" class="ml-auto text-white/80 hover:text-white">✕</button>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 text-white/90">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Page Header with Search --}}
    <div class="border-b border-white/5 bg-slate-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white flex items-center gap-3">
                            <span class="text-3xl sm:text-4xl">📦</span>
                            Kapsül Arşivim
                        </h1>
                        <p class="text-slate-400 text-sm mt-1">Tüm dijital anılarını burada yönet</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-slate-800/50 px-4 py-2 rounded-xl border border-white/5 flex items-center gap-2">
                            <span class="text-indigo-400 font-black text-xl">{{ $myCapsules->total() }}</span>
                            <span class="text-slate-500 text-sm">kapsül</span>
                        </div>
                    </div>
                </div>

                {{-- Search Bar --}}
                <form action="{{ route('dashboard') }}" method="GET" class="relative">
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Kapsüllerinde ara..."
                               class="w-full sm:w-96 bg-slate-800/50 border border-white/10 rounded-xl pl-12 pr-4 py-3 text-sm text-slate-200 placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        @if($search)
                            <a href="{{ route('dashboard') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition-colors">✕</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @forelse ($myCapsules as $capsule)
                    <div x-data="{
                        isEditing: false,
                        isLoading: false,
                        shareUrl: '{{ $capsule->share_url }}',
                        showShareModal: false,
                        async createShareLink() {
                            if (this.shareUrl) {
                                this.showShareModal = true;
                                return;
                            }
                            this.isLoading = true;
                            try {
                                const res = await fetch('/kapsul/{{ $capsule->id }}/share', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await res.json();
                                this.shareUrl = data.share_url;
                                this.showShareModal = true;
                            } catch (e) {
                                alert('Paylaşım linki oluşturulamadı');
                            }
                            this.isLoading = false;
                        },
                        copyToClipboard() {
                            navigator.clipboard.writeText(this.shareUrl);
                            this.$refs.copyBtn.textContent = '✅ Kopyalandı!';
                            setTimeout(() => this.$refs.copyBtn.textContent = '📋 Kopyala', 2000);
                        }
                    }" class="bg-gradient-to-br from-slate-800 to-slate-900 border border-white/5 rounded-3xl shadow-xl card-hover overflow-hidden flex flex-col group relative">

                        {{-- Share Modal --}}
                        <div x-cloak x-show="showShareModal"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             @click.self="showShareModal = false"
                             class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                            <div class="bg-slate-800 border border-white/10 rounded-2xl p-6 max-w-md w-full shadow-2xl"
                                 @click.stop>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-bold text-white flex items-center gap-2">
                                        <span>🔗</span> Paylaşım Linki
                                    </h3>
                                    <button @click="showShareModal = false" class="text-slate-400 hover:text-white">✕</button>
                                </div>
                                <p class="text-slate-400 text-sm mb-4">Bu linki paylaşarak kapsülünü başkalarıyla paylaşabilirsin.</p>
                                <div class="flex gap-2">
                                    <input type="text" :value="shareUrl" readonly class="flex-1 bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-sm text-slate-300 font-mono">
                                    <button @click="copyToClipboard()" x-ref="copyBtn" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-3 rounded-xl text-sm font-bold transition-colors whitespace-nowrap">
                                        📋 Kopyala
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- View Mode --}}
                        <div x-show="!isEditing" class="flex flex-col h-full">

                            {{-- Category Badge (Sol üst) --}}
                            @php
                                $categoryData = \App\Models\Capsule::CATEGORIES[$capsule->category] ?? \App\Models\Capsule::CATEGORIES['memory'];
                                $categoryColors = [
                                    'indigo' => 'bg-indigo-500/90',
                                    'rose' => 'bg-rose-500/90',
                                    'violet' => 'bg-violet-500/90',
                                    'emerald' => 'bg-emerald-500/90',
                                    'amber' => 'bg-amber-500/90',
                                    'cyan' => 'bg-cyan-500/90',
                                ];
                                $categoryBgColor = $categoryColors[$categoryData['color']] ?? 'bg-indigo-500/90';
                            @endphp
                            <div class="absolute top-3 left-3 z-10">
                                <span class="{{ $categoryBgColor }} backdrop-blur text-white text-[10px] font-bold px-2.5 py-1 rounded-lg shadow-lg flex items-center gap-1">
                                    {{ $categoryData['icon'] }} {{ $categoryData['name'] }}
                                </span>
                            </div>

                            {{-- Badges (Sağ üst) --}}
                            <div class="absolute top-3 right-3 flex gap-2 z-10">
                                @if($capsule->pin_code)
                                    <span class="bg-rose-500/90 backdrop-blur text-white text-[10px] font-bold px-2.5 py-1 rounded-lg shadow-lg flex items-center gap-1">
                                        🔐 Şifreli
                                    </span>
                                @endif
                                @if($capsule->unlock_date)
                                    <span class="bg-amber-500/90 backdrop-blur text-white text-[10px] font-bold px-2.5 py-1 rounded-lg shadow-lg flex items-center gap-1">
                                        ⏳ Kilitli
                                    </span>
                                @endif
                            </div>

                            {{-- Image --}}
                            <div class="relative w-full h-44 sm:h-48 bg-slate-900 flex items-center justify-center overflow-hidden">
                                @if($capsule->image)
                                    <img src="{{ asset('storage/' . $capsule->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="Kapsül Anısı" loading="lazy">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
                                @else
                                    <div class="flex flex-col items-center gap-2 text-slate-700">
                                        <span class="text-5xl">📸</span>
                                        <span class="text-xs font-medium">Görsel yok</span>
                                    </div>
                                @endif

                                {{-- Audio indicator --}}
                                @if($capsule->audio)
                                    <div class="absolute bottom-3 left-3 bg-indigo-500/90 backdrop-blur text-white text-[10px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                                        🎙️ Ses kaydı
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="p-5 flex-1 flex flex-col">
                                <p class="text-slate-200 font-medium leading-relaxed mb-4 line-clamp-3 text-sm">"{{ $capsule->message }}"</p>

                                <div class="mt-auto pt-4 border-t border-white/5 space-y-2.5">
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        <span class="text-indigo-400">📍</span>
                                        <span class="font-mono">{{ number_format($capsule->latitude, 4) }}, {{ number_format($capsule->longitude, 4) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        <span class="text-emerald-400">🕒</span>
                                        <span>{{ $capsule->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    @if($capsule->unlock_date)
                                        <div class="flex items-center gap-2 text-xs text-amber-400">
                                            <span>⏳</span>
                                            <span>Açılış: {{ \Carbon\Carbon::parse($capsule->unlock_date)->format('d M Y') }}</span>
                                        </div>
                                    @endif
                                    @if($capsule->pin_code)
                                        <div class="flex items-center gap-2 text-xs text-rose-400">
                                            <span>🔑</span>
                                            <span class="font-mono tracking-wider">PIN korumali</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="px-5 py-4 border-t border-white/5 flex justify-between items-center bg-slate-900/50">
                                <div class="flex items-center gap-3">
                                    <button @click="isEditing = true" class="flex items-center gap-2 text-indigo-400 hover:text-indigo-300 text-xs font-bold transition-colors group/btn">
                                        <span class="group-hover/btn:rotate-12 transition-transform">✏️</span>
                                        Düzenle
                                    </button>
                                    <button @click="createShareLink()" :disabled="isLoading" :class="{ 'opacity-50': isLoading }" class="flex items-center gap-2 text-cyan-400 hover:text-cyan-300 text-xs font-bold transition-colors group/btn">
                                        <span class="group-hover/btn:scale-110 transition-transform" x-text="isLoading ? '⏳' : '🔗'"></span>
                                        Paylaş
                                    </button>
                                </div>

                                <form action="{{ route('capsule.destroy', $capsule->id) }}" method="POST" onsubmit="return confirm('Bu kapsülü silmek istediğine emin misin? Bu işlem geri alınamaz.');" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-2 text-rose-400 hover:text-rose-300 text-xs font-bold transition-colors group/btn">
                                        <span class="group-hover/btn:scale-110 transition-transform">🗑️</span>
                                        Sil
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Edit Mode --}}
                        <div x-cloak x-show="isEditing"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="p-5 flex flex-col h-full bg-slate-800">
                            <form action="{{ route('capsule.update', $capsule->id) }}" method="POST" enctype="multipart/form-data"
                                  class="flex flex-col gap-4 h-full m-0"
                                  x-data="{ submitting: false }"
                                  @submit="submitting = true">
                                @csrf
                                @method('PATCH')

                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xl">✏️</span>
                                    <h3 class="font-bold text-white">Kapsülü Düzenle</h3>
                                </div>

                                <div>
                                    <label class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-2 block">Mesaj</label>
                                    <textarea name="message" required rows="3" class="w-full bg-slate-900 border border-white/10 rounded-xl p-3 text-sm text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none transition-all">{{ $capsule->message }}</textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-2 block">📅 Tarih Kilidi</label>
                                        <input type="date" name="unlock_date" value="{{ $capsule->unlock_date ? $capsule->unlock_date->format('Y-m-d') : '' }}" class="w-full bg-slate-900 border border-white/10 rounded-xl p-2.5 text-sm text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-2 block">🔐 PIN</label>
                                        <input type="text" name="pin_code" value="" maxlength="4" placeholder="Yeni PIN gir (opsiyonel)" class="w-full bg-slate-900 border border-white/10 rounded-xl p-2.5 text-sm text-center tracking-widest font-mono text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-2 block">📸 Yeni Fotoğraf</label>
                                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer cursor-pointer file:transition-colors">
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-2 block">🎙️ Yeni Ses</label>
                                        <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer cursor-pointer file:transition-colors">
                                    </div>
                                </div>

                                <div class="flex gap-3 mt-auto pt-4">
                                    <button type="submit" :disabled="submitting" :class="{ 'btn-loading opacity-70': submitting }" class="flex-1 bg-gradient-to-r from-emerald-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white py-3 rounded-xl text-xs font-bold transition-all active:scale-95 shadow-lg">
                                        <span x-show="!submitting">✅ Kaydet</span>
                                        <span x-show="submitting" class="invisible">Kaydediliyor...</span>
                                    </button>
                                    <button @click="isEditing = false" type="button" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white py-3 rounded-xl text-xs font-bold transition-all active:scale-95">
                                        İptal
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                @empty
                    {{-- Empty State --}}
                    <div class="col-span-full py-16 sm:py-24 flex flex-col items-center justify-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-500/20 to-violet-500/20 rounded-full flex items-center justify-center mb-6">
                            <span class="text-5xl">🌌</span>
                        </div>
                        @if($search)
                            <h3 class="text-xl sm:text-2xl font-black text-white mb-2">Sonuç Bulunamadı</h3>
                            <p class="text-slate-400 font-medium max-w-md mb-6 text-sm sm:text-base">"{{ $search }}" aramasıyla eşleşen kapsül yok.</p>
                            <a href="{{ route('dashboard') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-6 py-3 rounded-xl font-bold transition-all">
                                Tümünü Göster
                            </a>
                        @else
                            <h3 class="text-xl sm:text-2xl font-black text-white mb-2">Henüz Kapsül Yok</h3>
                            <p class="text-slate-400 font-medium max-w-md mb-6 text-sm sm:text-base">Haritaya git ve ilk dijital anını göm!</p>
                            <a href="{{ url('/') }}" class="bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-8 py-4 rounded-2xl font-bold shadow-xl hover:shadow-indigo-500/25 transition-all active:scale-95 flex items-center gap-2">
                                🗺️ Haritaya Git
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($myCapsules->hasPages())
                <div class="mt-8 flex justify-center">
                    <nav class="flex items-center gap-2">
                        {{-- Previous --}}
                        @if($myCapsules->onFirstPage())
                            <span class="px-4 py-2 bg-slate-800/50 text-slate-500 rounded-xl text-sm cursor-not-allowed">← Önceki</span>
                        @else
                            <a href="{{ $myCapsules->previousPageUrl() }}" class="px-4 py-2 bg-slate-800/50 hover:bg-slate-700/50 text-slate-300 rounded-xl text-sm transition-colors">← Önceki</a>
                        @endif

                        {{-- Page Numbers --}}
                        <div class="hidden sm:flex items-center gap-1">
                            @foreach($myCapsules->getUrlRange(1, $myCapsules->lastPage()) as $page => $url)
                                @if($page == $myCapsules->currentPage())
                                    <span class="w-10 h-10 flex items-center justify-center bg-indigo-600 text-white rounded-xl text-sm font-bold">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center bg-slate-800/50 hover:bg-slate-700/50 text-slate-300 rounded-xl text-sm transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach
                        </div>

                        {{-- Current Page (Mobile) --}}
                        <span class="sm:hidden px-4 py-2 bg-slate-800/50 text-slate-300 rounded-xl text-sm">
                            {{ $myCapsules->currentPage() }} / {{ $myCapsules->lastPage() }}
                        </span>

                        {{-- Next --}}
                        @if($myCapsules->hasMorePages())
                            <a href="{{ $myCapsules->nextPageUrl() }}" class="px-4 py-2 bg-slate-800/50 hover:bg-slate-700/50 text-slate-300 rounded-xl text-sm transition-colors">Sonraki →</a>
                        @else
                            <span class="px-4 py-2 bg-slate-800/50 text-slate-500 rounded-xl text-sm cursor-not-allowed">Sonraki →</span>
                        @endif
                    </nav>
                </div>
            @endif

        </div>
    </div>
</body>
</html>

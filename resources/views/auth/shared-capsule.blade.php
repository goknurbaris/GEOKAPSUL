<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paylaşılan Kapsül | GeoKapsül</title>

    <!-- Open Graph / Sosyal Medya -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="🌍 Birisi seninle bir GeoKapsül paylaştı!">
    <meta property="og:description" content="Zaman ve mekanda gizlenmiş dijital bir anı seni bekliyor.">
    <meta property="og:image" content="{{ asset('images/og-share.png') }}">
    <meta name="twitter:card" content="summary_large_image">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        .glass-card {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(56, 189, 248, 0.2);
        }

        .gradient-border {
            background: linear-gradient(135deg, #0ea5e9, #8b5cf6, #ec4899, #0ea5e9);
            background-size: 300% 300%;
            animation: gradient-shift 8s ease infinite;
        }

        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .capsule-float {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <!-- Arka Plan Efekti -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-lg">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-cyan-500 to-purple-600 shadow-lg capsule-float">
                <span class="text-4xl">🌍</span>
            </div>
            <h1 class="mt-4 text-2xl font-bold text-white">GeoKapsül</h1>
            <p class="text-slate-400">Paylaşılan Zaman Kapsülü</p>
        </div>

        <!-- Ana Kart -->
        <div class="gradient-border p-[2px] rounded-2xl">
            <div class="glass-card rounded-2xl p-6">

                @if($locked && $lock_type === 'time')
                    <!-- Tarih Kilitli -->
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-500/20 flex items-center justify-center">
                            <span class="text-3xl">🔒</span>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-2">Zaman Kilitli Kapsül</h2>
                        <p class="text-slate-400 mb-4">Bu kapsül henüz açılamaz.</p>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/20 rounded-lg text-amber-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $unlock_date }} tarihinde açılacak</span>
                        </div>
                    </div>

                @elseif($locked && $lock_type === 'expired')
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-rose-500/20 flex items-center justify-center">
                            <span class="text-3xl">⌛</span>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-2">Paylaşım Süresi Doldu</h2>
                        <p class="text-slate-400">Bu kapsül linkinin süresi dolmuş.</p>
                    </div>

                @elseif($locked && $lock_type === 'pin')
                    <!-- PIN Girişi -->
                    <div class="text-center py-4">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-purple-500/20 flex items-center justify-center">
                            <span class="text-3xl">🔐</span>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-2">Şifre Korumalı Kapsül</h2>
                        <p class="text-slate-400 mb-6">Bu kapsülü açmak için 4 haneli şifreyi gir.</p>

                        @if(isset($error))
                            <div class="mb-4 p-3 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 text-sm">
                                {{ $error }}
                            </div>
                        @endif

                        <form action="{{ route('capsule.shared', $shareCode) }}" method="GET" class="space-y-4">
                            <input type="text" name="pin" maxlength="4" pattern="[0-9]{4}"
                                   class="w-32 mx-auto block text-center text-2xl tracking-[0.5em] py-3 bg-slate-800/50 border border-slate-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="••••" required autofocus>
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-semibold rounded-lg transition-all">
                                Kapsülü Aç
                            </button>
                        </form>
                    </div>

                @else
                    <!-- Kapsül İçeriği -->
                    <div class="space-y-6">
                        <!-- Başarı Mesajı -->
                        <div class="text-center pb-4 border-b border-slate-700">
                            <span class="text-3xl mb-2 block">🎉</span>
                            <p class="text-cyan-400 font-medium">Kapsül açıldı!</p>
                        </div>

                        <!-- Mesaj -->
                        <div class="bg-slate-800/50 rounded-xl p-4">
                            <p class="text-white whitespace-pre-wrap">{{ $capsule->message }}</p>
                        </div>

                        <!-- Görsel -->
                        @if($capsule->image)
                            <div class="rounded-xl overflow-hidden">
                                <img src="{{ asset('storage/' . $capsule->image) }}" alt="Kapsül görseli"
                                     class="w-full h-auto">
                            </div>
                        @endif

                        <!-- Ses -->
                        @if($capsule->audio)
                            <div class="bg-slate-800/50 rounded-xl p-4">
                                <p class="text-slate-400 text-sm mb-2">🎵 Ses Kaydı</p>
                                <audio controls class="w-full">
                                    <source src="{{ asset('storage/' . $capsule->audio) }}">
                                </audio>
                            </div>
                        @endif

                        <!-- Meta Bilgiler -->
                        <div class="flex items-center justify-between text-sm text-slate-500 pt-4 border-t border-slate-700">
                            <span>📅 {{ $capsule->created_at->format('d.m.Y') }}</span>
                            <span>👤 {{ $capsule->user->name ?? 'Anonim' }}</span>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- Alt Bilgi -->
        <div class="text-center mt-6">
            <a href="{{ route('welcome') }}" class="text-slate-400 hover:text-cyan-400 transition-colors text-sm">
                🌍 Sen de kendi kapsülünü oluştur →
            </a>
        </div>
    </div>
</body>
</html>

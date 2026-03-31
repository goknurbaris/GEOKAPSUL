<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rozetler | GeoKapsül</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { background: #020617; }
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .gradient-text {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .badge-locked {
            filter: grayscale(100%) opacity(0.4);
        }
        .badge-card {
            transition: all 0.3s ease;
        }
        .badge-card:hover {
            transform: translateY(-4px);
        }
        .badge-card.earned {
            box-shadow: 0 0 30px -5px var(--glow-color);
        }
        @keyframes shine {
            0% { background-position: -100% 0; }
            100% { background-position: 200% 0; }
        }
        .badge-new {
            animation: shine 2s infinite linear;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            background-size: 50% 100%;
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-200 min-h-screen">

    <!-- Navigation -->
    <nav class="glass-card sticky top-0 z-50 border-b border-white/5">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center">
                        <span class="text-xl">🌍</span>
                    </div>
                    <span class="font-bold text-lg gradient-text hidden sm:block">GeoKapsül</span>
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('leaderboard') }}" class="px-4 py-2 text-slate-400 hover:text-white rounded-lg hover:bg-white/5 transition-all text-sm">🏅 Liderlik</a>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-slate-400 hover:text-white rounded-lg hover:bg-white/5 transition-all text-sm">📦 Kapsüllerim</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-8 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">

            <!-- Header -->
            <div class="text-center mb-10">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">🏆 Rozetler</h1>
                <p class="text-slate-400 mb-4">Başarılarını topla ve koleksiyonunu büyüt!</p>

                <!-- Progress -->
                <div class="inline-flex items-center gap-3 bg-white/5 rounded-full px-6 py-3">
                    <span class="text-2xl">🎖️</span>
                    <div class="text-left">
                        <div class="text-white font-bold">{{ $earnedCount }} / {{ $totalBadges }}</div>
                        <div class="text-xs text-slate-400">Rozet Kazanıldı</div>
                    </div>
                    <div class="w-32 h-2 bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-amber-500 to-yellow-500 rounded-full" style="width: {{ $totalBadges > 0 ? ($earnedCount / $totalBadges * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Badge Groups -->
            <div class="space-y-10">

                @php
                    $groupNames = [
                        'capsule_count' => ['name' => 'Kapsül Oluşturma', 'icon' => '📦', 'desc' => 'Kapsül oluşturarak kazan'],
                        'capsule_opened' => ['name' => 'Keşif', 'icon' => '🔍', 'desc' => 'Kapsülleri keşfederek kazan'],
                        'distance' => ['name' => 'Mesafe', 'icon' => '🚶', 'desc' => 'Yürüyerek kazan'],
                        'level' => ['name' => 'Seviye', 'icon' => '⭐', 'desc' => 'Seviye atlayarak kazan'],
                        'category' => ['name' => 'Uzmanlik', 'icon' => '🎯', 'desc' => 'Kategori uzmanı ol'],
                    ];
                @endphp

                @foreach($groupedBadges as $type => $badges)
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-2xl">{{ $groupNames[$type]['icon'] ?? '🏅' }}</span>
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ $groupNames[$type]['name'] ?? 'Özel' }}</h2>
                            <p class="text-sm text-slate-400">{{ $groupNames[$type]['desc'] ?? 'Özel başarılar' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($badges as $badge)
                        @php
                            $isEarned = in_array($badge->id, $earnedBadgeIds);
                            $colors = $badge->color_classes;
                        @endphp
                        <div class="badge-card glass-card rounded-2xl p-5 text-center {{ $isEarned ? 'earned' : '' }}"
                             style="--glow-color: {{ $isEarned ? 'rgba(99, 102, 241, 0.3)' : 'transparent' }}">

                            <!-- Badge Icon -->
                            <div class="relative inline-block mb-3">
                                <div class="w-16 h-16 {{ $colors[0] }} rounded-2xl flex items-center justify-center text-3xl shadow-lg {{ $colors[2] }} {{ !$isEarned ? 'badge-locked' : '' }}">
                                    {{ $badge->icon }}
                                </div>
                                @if($isEarned)
                                <div class="absolute -top-1 -right-1 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                @endif
                            </div>

                            <!-- Badge Info -->
                            <h3 class="font-bold text-white text-sm mb-1 {{ !$isEarned ? 'text-slate-400' : '' }}">{{ $badge->name }}</h3>
                            <p class="text-xs text-slate-500 mb-2 line-clamp-2">{{ $badge->description }}</p>

                            <!-- XP Reward -->
                            @if($badge->xp_reward > 0)
                            <div class="inline-flex items-center gap-1 bg-amber-500/20 text-amber-400 text-xs px-2 py-1 rounded-full">
                                <span>+{{ $badge->xp_reward }}</span>
                                <span>XP</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

            </div>

        </div>
    </main>

</body>
</html>

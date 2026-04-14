<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liderlik Tablosu | GeoKapsül</title>
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
        .rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); }
        .rank-3 { background: linear-gradient(135deg, #f97316, #ea580c); }
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
                    <a href="{{ route('badges') }}" class="px-4 py-2 text-slate-400 hover:text-white rounded-lg hover:bg-white/5 transition-all text-sm">🏆 Rozetler</a>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-slate-400 hover:text-white rounded-lg hover:bg-white/5 transition-all text-sm">📦 Kapsüllerim</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-8 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">

            <!-- Header -->
            <div class="text-center mb-10">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">🏅 Liderlik Tablosu</h1>
                <p class="text-slate-400">En iyi kaşifler burada!</p>
            </div>

            <!-- User Stats Card -->
            @auth
            <div class="glass-card rounded-2xl p-6 mb-8">
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover ring-2 ring-indigo-500/40">
                    @else
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-full flex items-center justify-center text-3xl font-bold text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 text-center sm:text-left">
                        <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                        <p class="text-indigo-400 text-sm">{{ $user->level_title }} • Seviye {{ $user->level }}</p>

                        <!-- XP Progress -->
                        <div class="mt-3 max-w-md">
                            <div class="flex justify-between text-xs text-slate-400 mb-1">
                                <span>{{ number_format($user->xp) }} XP</span>
                                <span>{{ number_format($user->xpForNextLevel()) }} XP</span>
                            </div>
                            <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full transition-all" style="width: {{ $user->levelProgress() }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="bg-white/5 rounded-xl px-4 py-2">
                            <div class="text-lg font-bold text-amber-400">#{{ $userRanks['xp'] }}</div>
                            <div class="text-xs text-slate-500">XP</div>
                        </div>
                        <div class="bg-white/5 rounded-xl px-4 py-2">
                            <div class="text-lg font-bold text-emerald-400">#{{ $userRanks['capsules'] }}</div>
                            <div class="text-xs text-slate-500">Kapsül</div>
                        </div>
                        <div class="bg-white/5 rounded-xl px-4 py-2">
                            <div class="text-lg font-bold text-cyan-400">#{{ $userRanks['explorer'] }}</div>
                            <div class="text-xs text-slate-500">Keşif</div>
                        </div>
                        <div class="bg-white/5 rounded-xl px-4 py-2">
                            <div class="text-lg font-bold text-violet-400">#{{ $userRanks['distance'] }}</div>
                            <div class="text-xs text-slate-500">Mesafe</div>
                        </div>
                    </div>
                </div>
            </div>
            @endauth

            <!-- Tabs -->
            <div x-data="{ tab: '{{ $currentType }}' }" class="space-y-6">
                <div class="flex gap-1 p-1 bg-slate-800/50 rounded-xl overflow-x-auto">
                    <button @click="tab = 'xp'" :class="tab === 'xp' ? 'bg-white/10 text-white' : 'text-slate-400'" class="flex-1 px-4 py-2.5 rounded-lg font-medium text-sm transition-all whitespace-nowrap">⭐ XP Puanı</button>
                    <button @click="tab = 'capsules'" :class="tab === 'capsules' ? 'bg-white/10 text-white' : 'text-slate-400'" class="flex-1 px-4 py-2.5 rounded-lg font-medium text-sm transition-all whitespace-nowrap">📦 Kapsüller</button>
                    <button @click="tab = 'explorer'" :class="tab === 'explorer' ? 'bg-white/10 text-white' : 'text-slate-400'" class="flex-1 px-4 py-2.5 rounded-lg font-medium text-sm transition-all whitespace-nowrap">🔍 Keşifler</button>
                    <button @click="tab = 'distance'" :class="tab === 'distance' ? 'bg-white/10 text-white' : 'text-slate-400'" class="flex-1 px-4 py-2.5 rounded-lg font-medium text-sm transition-all whitespace-nowrap">🚶 Mesafe</button>
                </div>

                <!-- XP Leaderboard -->
                <div x-show="tab === 'xp'" class="glass-card rounded-2xl overflow-hidden">
                    <div class="divide-y divide-white/5">
                        @foreach($leaderboards['xp'] as $index => $player)
                        <div class="flex items-center gap-4 p-4 hover:bg-white/5 transition-colors {{ auth()->id() === $player->id ? 'bg-indigo-500/10' : '' }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $index === 0 ? 'rank-1 text-white' : '' }}
                                {{ $index === 1 ? 'rank-2 text-white' : '' }}
                                {{ $index === 2 ? 'rank-3 text-white' : '' }}
                                {{ $index > 2 ? 'bg-slate-700 text-slate-300' : '' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-white">{{ $player->name }}</div>
                                <div class="text-xs text-slate-400">Seviye {{ $player->level }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-amber-400">{{ number_format($player->xp) }}</div>
                                <div class="text-xs text-slate-500">XP</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Capsules Leaderboard -->
                <div x-show="tab === 'capsules'" x-cloak class="glass-card rounded-2xl overflow-hidden">
                    <div class="divide-y divide-white/5">
                        @foreach($leaderboards['capsules'] as $index => $player)
                        <div class="flex items-center gap-4 p-4 hover:bg-white/5 transition-colors {{ auth()->id() === $player->id ? 'bg-indigo-500/10' : '' }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $index === 0 ? 'rank-1 text-white' : '' }}
                                {{ $index === 1 ? 'rank-2 text-white' : '' }}
                                {{ $index === 2 ? 'rank-3 text-white' : '' }}
                                {{ $index > 2 ? 'bg-slate-700 text-slate-300' : '' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-white">{{ $player->name }}</div>
                                <div class="text-xs text-slate-400">Seviye {{ $player->level }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-emerald-400">{{ number_format($player->capsules_created) }}</div>
                                <div class="text-xs text-slate-500">Kapsül</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Explorer Leaderboard -->
                <div x-show="tab === 'explorer'" x-cloak class="glass-card rounded-2xl overflow-hidden">
                    <div class="divide-y divide-white/5">
                        @foreach($leaderboards['explorer'] as $index => $player)
                        <div class="flex items-center gap-4 p-4 hover:bg-white/5 transition-colors {{ auth()->id() === $player->id ? 'bg-indigo-500/10' : '' }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $index === 0 ? 'rank-1 text-white' : '' }}
                                {{ $index === 1 ? 'rank-2 text-white' : '' }}
                                {{ $index === 2 ? 'rank-3 text-white' : '' }}
                                {{ $index > 2 ? 'bg-slate-700 text-slate-300' : '' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-white">{{ $player->name }}</div>
                                <div class="text-xs text-slate-400">Seviye {{ $player->level }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-cyan-400">{{ number_format($player->capsules_opened) }}</div>
                                <div class="text-xs text-slate-500">Keşif</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Distance Leaderboard -->
                <div x-show="tab === 'distance'" x-cloak class="glass-card rounded-2xl overflow-hidden">
                    <div class="divide-y divide-white/5">
                        @foreach($leaderboards['distance'] as $index => $player)
                        <div class="flex items-center gap-4 p-4 hover:bg-white/5 transition-colors {{ auth()->id() === $player->id ? 'bg-indigo-500/10' : '' }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $index === 0 ? 'rank-1 text-white' : '' }}
                                {{ $index === 1 ? 'rank-2 text-white' : '' }}
                                {{ $index === 2 ? 'rank-3 text-white' : '' }}
                                {{ $index > 2 ? 'bg-slate-700 text-slate-300' : '' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-white">{{ $player->name }}</div>
                                <div class="text-xs text-slate-400">Seviye {{ $player->level }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-violet-400">{{ number_format($player->total_distance_km, 1) }}</div>
                                <div class="text-xs text-slate-500">km</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>

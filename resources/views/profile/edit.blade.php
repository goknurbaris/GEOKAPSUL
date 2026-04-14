<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="theme-color" content="#020617">
    <title>Profilim | GeoKapsül</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        
        body {
            background: #020617;
            min-height: 100vh;
        }

        /* Animated gradient background */
        .gradient-bg {
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(ellipse at 0% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 100% 100%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Glass card */
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Form input */
        .form-input {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .form-input::placeholder {
            color: rgba(148, 163, 184, 0.5);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Avatar ring animation */
        @keyframes ring-pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.2; }
        }

        .avatar-ring {
            animation: ring-pulse 3s ease-in-out infinite;
        }

        /* Tab active indicator */
        .tab-active {
            position: relative;
        }

        .tab-active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            border-radius: 2px;
        }

        /* Stat card hover */
        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.3);
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-200">

    <!-- Background -->
    <div class="gradient-bg"></div>

    <!-- Navigation -->
    <nav class="glass-card sticky top-0 z-50 border-b border-white/5">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                        <span class="text-xl">🌍</span>
                    </div>
                    <span class="font-bold text-lg gradient-text hidden sm:block">GeoKapsül</span>
                </a>

                <!-- Nav Links -->
                <div class="flex items-center gap-2">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 px-4 py-2 text-slate-400 hover:text-white rounded-lg hover:bg-white/5 transition-all text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <span class="hidden sm:inline">Harita</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-slate-400 hover:text-white rounded-lg hover:bg-white/5 transition-all text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="hidden sm:inline">Kapsüllerim</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 text-rose-400 hover:text-rose-300 rounded-lg hover:bg-rose-500/10 transition-all text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="hidden sm:inline">Çıkış</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Toast Notifications -->
    @if(session('status') === 'profile-updated')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             class="fixed top-20 right-4 z-50 bg-emerald-500 text-white px-5 py-3 rounded-xl shadow-lg font-medium text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Profil güncellendi!
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             class="fixed top-20 right-4 z-50 bg-amber-500 text-white px-5 py-3 rounded-xl shadow-lg font-medium text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Şifre güncellendi!
        </div>
    @endif

    <!-- Main Content -->
    <main class="relative z-10 py-8 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">

            <!-- Profile Header -->
            <div class="glass-card rounded-2xl p-6 sm:p-8 mb-6">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    <!-- Avatar -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-full blur-xl opacity-50 avatar-ring"></div>
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover shadow-2xl ring-2 ring-indigo-400/30">
                        @else
                            <div class="relative w-24 h-24 sm:w-28 sm:h-28 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-full flex items-center justify-center text-white font-bold text-4xl sm:text-5xl shadow-2xl">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <!-- Level Badge -->
                        <div class="absolute -bottom-1 -right-1 w-10 h-10 bg-gradient-to-br from-amber-500 to-yellow-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg border-2 border-slate-900">
                            {{ $user->level ?? 1 }}
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1 text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">{{ $user->name }}</h1>
                        <p class="text-indigo-400 text-sm font-medium mb-1">{{ $user->level_title ?? 'Çaylak' }} • Seviye {{ $user->level ?? 1 }}</p>
                        <p class="text-slate-500 text-sm mb-3">{{ $user->email }}</p>
                        
                        <!-- XP Progress Bar -->
                        <div class="max-w-xs mx-auto sm:mx-0 mb-4">
                            <div class="flex justify-between text-xs text-slate-400 mb-1">
                                <span>{{ number_format($user->xp ?? 0) }} XP</span>
                                <span>{{ number_format($user->xpForNextLevel() ?? 100) }} XP</span>
                            </div>
                            <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full transition-all" style="width: {{ $user->levelProgress() ?? 0 }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="flex flex-wrap justify-center sm:justify-start gap-3">
                            <div class="stat-card bg-white/5 border border-white/10 rounded-xl px-4 py-2">
                                <div class="text-lg font-bold text-indigo-400">{{ $user->capsules_created ?? $user->capsules()->count() }}</div>
                                <div class="text-xs text-slate-500">Kapsül</div>
                            </div>
                            <div class="stat-card bg-white/5 border border-white/10 rounded-xl px-4 py-2">
                                <div class="text-lg font-bold text-cyan-400">{{ $user->capsules_opened ?? 0 }}</div>
                                <div class="text-xs text-slate-500">Keşif</div>
                            </div>
                            <div class="stat-card bg-white/5 border border-white/10 rounded-xl px-4 py-2">
                                <div class="text-lg font-bold text-emerald-400">{{ number_format($user->total_distance_km ?? 0, 1) }}</div>
                                <div class="text-xs text-slate-500">km</div>
                            </div>
                            <a href="{{ route('badges') }}" class="stat-card bg-white/5 border border-white/10 rounded-xl px-4 py-2 hover:border-amber-500/30 transition-colors">
                                <div class="text-lg font-bold text-amber-400">{{ $user->badges()->count() }}</div>
                                <div class="text-xs text-slate-500">Rozet</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Sections -->
            <div x-data="{ activeTab: 'profile' }" class="space-y-6">
                
                <!-- Tabs -->
                <div class="flex gap-1 p-1 bg-slate-800/50 rounded-xl overflow-x-auto">
                    <button @click="activeTab = 'profile'" 
                            :class="activeTab === 'profile' ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white'"
                            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-medium text-sm transition-all whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil
                    </button>
                    <button @click="activeTab = 'security'" 
                            :class="activeTab === 'security' ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white'"
                            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-medium text-sm transition-all whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Güvenlik
                    </button>
                    <button @click="activeTab = 'danger'" 
                            :class="activeTab === 'danger' ? 'bg-rose-500/20 text-rose-400' : 'text-slate-400 hover:text-rose-400'"
                            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-medium text-sm transition-all whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Hesap Sil
                    </button>
                </div>

                <!-- Profile Tab -->
                <div x-show="activeTab === 'profile'" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="glass-card rounded-2xl p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-indigo-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-white">Profil Bilgileri</h2>
                                <p class="text-sm text-slate-400">Hesap bilgilerini güncelle</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            @method('patch')

                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Profil Fotoğrafı</label>
                                    <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp"
                                           class="form-input w-full px-4 py-3 rounded-xl text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-500/20 file:px-3 file:py-2 file:text-indigo-300 file:cursor-pointer">
                                    <p class="mt-2 text-xs text-slate-500">JPG, PNG veya WEBP • Maksimum 2MB</p>
                                    @error('avatar')
                                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-end">
                                    @if ($user->avatar_path)
                                        <label class="inline-flex items-center gap-2 text-sm text-slate-300 cursor-pointer">
                                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-slate-600 bg-slate-700 text-rose-500 focus:ring-rose-500">
                                            Profil fotoğrafını kaldır
                                        </label>
                                    @endif
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Ad Soyad</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                           class="form-input w-full px-4 py-3 rounded-xl text-sm">
                                    @error('name')
                                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">E-posta Adresi</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                           class="form-input w-full px-4 py-3 rounded-xl text-sm">
                                    @error('email')
                                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-medium px-6 py-3 rounded-xl shadow-lg shadow-indigo-500/20 transition-all active:scale-[0.98]">
                                    Değişiklikleri Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Tab -->
                <div x-show="activeTab === 'security'" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="glass-card rounded-2xl p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-amber-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-white">Şifre Değiştir</h2>
                                <p class="text-sm text-slate-400">Güçlü bir şifre ile hesabını koru</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                            @csrf
                            @method('put')

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Mevcut Şifre</label>
                                <input type="password" name="current_password" required
                                       class="form-input w-full px-4 py-3 rounded-xl text-sm" placeholder="••••••••">
                                @error('current_password', 'updatePassword')
                                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Yeni Şifre</label>
                                    <input type="password" name="password" required
                                           class="form-input w-full px-4 py-3 rounded-xl text-sm" placeholder="••••••••">
                                    @error('password', 'updatePassword')
                                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Şifre Tekrar</label>
                                    <input type="password" name="password_confirmation" required
                                           class="form-input w-full px-4 py-3 rounded-xl text-sm" placeholder="••••••••">
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-medium px-6 py-3 rounded-xl shadow-lg shadow-amber-500/20 transition-all active:scale-[0.98]">
                                    Şifreyi Güncelle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Danger Tab -->
                <div x-show="activeTab === 'danger'" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="glass-card rounded-2xl p-6 sm:p-8 border-rose-500/20">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-rose-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-rose-400">Hesabı Sil</h2>
                                <p class="text-sm text-slate-400">Bu işlem geri alınamaz!</p>
                            </div>
                        </div>

                        <div class="bg-rose-500/10 border border-rose-500/20 rounded-xl p-4 mb-6">
                            <p class="text-sm text-slate-300">
                                Hesabını sildiğinde, tüm kapsüllerin, fotoğrafların ve ses kayıtların kalıcı olarak silinecek. Bu işlem geri alınamaz.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('profile.destroy') }}" 
                              onsubmit="return confirm('Hesabını ve tüm verilerini kalıcı olarak silmek istediğine emin misin?');">
                            @csrf
                            @method('delete')

                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <input type="password" name="password" required
                                           class="form-input w-full px-4 py-3 rounded-xl text-sm border-rose-500/30 focus:border-rose-500" 
                                           placeholder="Onaylamak için şifreni gir">
                                    @error('password', 'userDeletion')
                                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white font-medium px-6 py-3 rounded-xl shadow-lg shadow-rose-500/20 transition-all active:scale-[0.98] whitespace-nowrap">
                                    Hesabımı Sil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </main>

</body>
</html>

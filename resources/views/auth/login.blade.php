<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="theme-color" content="#020617">
    <title>Giriş Yap | GeoKapsül</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: #020617;
            min-height: 100vh;
            font-family: system-ui, -apple-system, sans-serif;
        }

        /* Animated gradient background */
        .gradient-bg {
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(6, 182, 212, 0.1) 0%, transparent 60%);
            animation: gradientMove 15s ease-in-out infinite;
        }

        @keyframes gradientMove {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.1) rotate(3deg); }
        }

        /* Grid pattern overlay */
        .grid-pattern {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(rgba(99, 102, 241, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            animation: orbFloat 20s infinite;
        }

        .orb-1 {
            width: 300px;
            height: 300px;
            background: rgba(99, 102, 241, 0.3);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: rgba(139, 92, 246, 0.2);
            bottom: 10%;
            right: 10%;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 200px;
            height: 200px;
            background: rgba(6, 182, 212, 0.25);
            top: 50%;
            right: 30%;
            animation-delay: -10s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.95); }
            75% { transform: translate(20px, 30px) scale(1.05); }
        }

        /* Card styles */
        .login-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(40px) saturate(150%);
            -webkit-backdrop-filter: blur(40px) saturate(150%);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Input styles */
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

        /* Button hover effect */
        .btn-primary {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        /* Logo pulse */
        .logo-container {
            animation: logoPulse 3s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Capsule icons floating */
        .floating-capsule {
            position: absolute;
            font-size: 24px;
            opacity: 0.15;
            animation: capsuleFloat 10s infinite;
        }

        @keyframes capsuleFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }

        /* Social divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        }
    </style>
</head>
<body class="antialiased">

    <!-- Background Effects -->
    <div class="gradient-bg"></div>
    <div class="grid-pattern"></div>
    
    <!-- Floating Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Floating Capsule Icons -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <span class="floating-capsule" style="top: 15%; left: 8%; animation-delay: 0s;">💎</span>
        <span class="floating-capsule" style="top: 70%; left: 5%; animation-delay: -2s;">🌍</span>
        <span class="floating-capsule" style="top: 25%; right: 10%; animation-delay: -4s;">📍</span>
        <span class="floating-capsule" style="top: 80%; right: 8%; animation-delay: -6s;">⏳</span>
        <span class="floating-capsule" style="top: 45%; left: 3%; animation-delay: -8s;">🔐</span>
    </div>

    <!-- Back Button -->
    <a href="{{ url('/') }}" class="fixed top-6 left-6 z-50 flex items-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span class="hidden sm:inline">Haritaya Dön</span>
    </a>

    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-[420px]">
            
            <!-- Login Card -->
            <div class="login-card rounded-3xl p-8 sm:p-10 shadow-2xl">
                
                <!-- Logo Section -->
                <div class="text-center mb-8">
                    <div class="logo-container inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/30 mb-4">
                        <span class="text-4xl">🌍</span>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-1">Tekrar Hoşgeldin</h1>
                    <p class="text-slate-400 text-sm">Dijital anılarına erişmek için giriş yap</p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">E-posta Adresi</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus 
                                   class="form-input w-full pl-12 pr-4 py-3.5 rounded-xl text-sm" 
                                   placeholder="ornek@email.com">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-rose-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-medium text-slate-300">Şifre</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">Şifremi Unuttum</a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input type="password" name="password" required 
                                   class="form-input w-full pl-12 pr-4 py-3.5 rounded-xl text-sm" 
                                   placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-rose-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="remember" id="remember_me" 
                               class="w-4 h-4 rounded border-slate-600 bg-slate-700/50 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 cursor-pointer">
                        <label for="remember_me" class="text-sm text-slate-400 cursor-pointer select-none">Beni hatırla</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold py-3.5 rounded-xl shadow-lg shadow-indigo-500/25 active:scale-[0.98] transition-transform">
                        Giriş Yap
                    </button>
                </form>

                <!-- Divider -->
                <div class="divider my-8">
                    <span class="text-slate-500 text-xs">veya</span>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-slate-400 text-sm mb-4">Henüz hesabın yok mu?</p>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center w-full gap-2 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white font-medium py-3.5 rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Yeni Hesap Oluştur
                        </a>
                    @endif
                </div>

            </div>

            <!-- Footer -->
            <p class="text-center text-slate-500 text-xs mt-6">
                Giriş yaparak <a href="#" class="text-indigo-400 hover:underline">Kullanım Şartları</a>'nı kabul etmiş olursun.
            </p>

        </div>
    </div>

</body>
</html>

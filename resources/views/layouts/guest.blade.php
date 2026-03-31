<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#020617">

        <title>{{ config('app.name', 'GeoKapsül') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Leaflet for map background -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            #bg-map {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 0;
                filter: brightness(0.3) contrast(1.1) saturate(0.8);
            }

            .glass-card {
                background: rgba(15, 23, 42, 0.75);
                backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
            }

            .glass-input {
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }

            .glass-input:focus {
                border-color: rgba(99, 102, 241, 0.5);
                box-shadow: 0 0 20px rgba(99, 102, 241, 0.15);
            }

            /* Animated gradient border */
            .animated-border::before {
                content: "";
                position: absolute;
                inset: -2px;
                background: linear-gradient(45deg,
                    rgba(99, 102, 241, 0.5),
                    rgba(139, 92, 246, 0.5),
                    rgba(168, 85, 247, 0.5),
                    rgba(99, 102, 241, 0.5)
                );
                border-radius: inherit;
                z-index: -1;
                animation: borderRotate 6s linear infinite;
                background-size: 300% 300%;
            }

            @keyframes borderRotate {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }

            /* Floating particles */
            .particle {
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(99, 102, 241, 0.4);
                border-radius: 50%;
                animation: float 8s infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0) translateX(0); opacity: 0; }
                10% { opacity: 1; }
                90% { opacity: 1; }
                100% { transform: translateY(-100vh) translateX(20px); opacity: 0; }
            }
        </style>
    </head>
    <body class="font-sans text-slate-200 antialiased bg-slate-950 min-h-screen overflow-x-hidden">

        <!-- Map Background -->
        <div id="bg-map"></div>

        <!-- Floating Particles -->
        <div class="fixed inset-0 z-[1] pointer-events-none overflow-hidden">
            @for($i = 0; $i < 15; $i++)
                <div class="particle" style="left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 8) }}s; animation-duration: {{ rand(6, 12) }}s;"></div>
            @endfor
        </div>

        <!-- Back to Map Link -->
        <a href="{{ url('/') }}" class="fixed top-4 left-4 sm:top-6 sm:left-6 z-[100] flex items-center gap-2 glass-card border border-white/10 text-white px-4 py-2.5 sm:px-5 sm:py-3 rounded-2xl text-xs sm:text-sm font-bold hover:bg-white/10 transition-all shadow-2xl group">
            <span class="text-lg group-hover:-translate-x-1 transition-transform">←</span>
            <span class="hidden sm:inline">Haritaya Dön</span>
        </a>

        <!-- Main Content -->
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-8 px-4 sm:py-12 relative z-10">

            <!-- Logo -->
            <a href="/" class="mb-6 sm:mb-8 flex flex-col items-center group">
                <span class="text-5xl sm:text-6xl mb-2 group-hover:scale-110 transition-transform filter drop-shadow-[0_0_30px_rgba(99,102,241,0.6)]">💎</span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                    <span class="text-white">GEO</span><span class="text-indigo-400">KAPSÜL</span>
                </h1>
            </a>

            <!-- Form Card -->
            <div class="w-full max-w-md relative">
                <div class="glass-card rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-white/10 shadow-2xl shadow-black/50 relative overflow-hidden animated-border">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <p class="mt-6 sm:mt-8 text-slate-500 text-xs sm:text-sm text-center">
                Dijital anılarını haritaya göm 🗺️
            </p>
        </div>

        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var map = L.map('bg-map', {
                    zoomControl: false,
                    attributionControl: false,
                    dragging: false,
                    scrollWheelZoom: false,
                    touchZoom: false,
                    doubleClickZoom: false
                }).setView([38.626, 34.714], 14);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);

                setTimeout(() => { map.invalidateSize(); }, 200);

                // Add some decorative markers
                var capsuleIcon = L.divIcon({
                    className: 'capsule-marker',
                    html: `<div class="w-3 h-3 bg-indigo-500/60 rounded-full shadow-[0_0_20px_rgba(99,102,241,0.8)] animate-pulse"></div>`,
                    iconSize: [12, 12]
                });

                L.marker([38.626, 34.714], {icon: capsuleIcon}).addTo(map);
                L.marker([38.632, 34.720], {icon: capsuleIcon}).addTo(map);
                L.marker([38.620, 34.708], {icon: capsuleIcon}).addTo(map);
            });
        </script>
    </body>
</html>

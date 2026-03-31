<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <title>GeoKapsül - Dijital Zaman Kapsülü | Anılarını Haritada Göm</title>
    <meta name="description" content="GeoKapsül ile dijital anılarını gerçek dünya konumlarına göm. Fotoğraf, ses ve mesajlarını GPS koordinatlarına sakla, gelecekte keşfet.">
    <meta name="keywords" content="zaman kapsülü, dijital anılar, gps, konum tabanlı, fotoğraf paylaşma, ses kaydı, hatıra">
    <meta name="author" content="GeoKapsül">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="GeoKapsül - Dijital Zaman Kapsülü">
    <meta property="og:description" content="Anılarını gerçek dünya konumlarına göm. Fotoğraf, ses ve mesajlarını GPS koordinatlarına sakla.">
    <meta property="og:locale" content="tr_TR">
    <meta property="og:site_name" content="GeoKapsül">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="GeoKapsül - Dijital Zaman Kapsülü">
    <meta name="twitter:description" content="Anılarını gerçek dünya konumlarına göm. Fotoğraf, ses ve mesajlarını GPS koordinatlarına sakla.">
    
    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 100vh; width: 100%; z-index: 1; }
        html, body { margin: 0; padding: 0; overflow: hidden; height: 100%; }
        .leaflet-popup-content { margin: 14px; }
        audio::-webkit-media-controls-panel { background-color: #f8fafc; border-radius: 12px; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
        
        /* Pulse animation for markers */
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(2); opacity: 0; }
        }
        .pulse-marker::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: inherit;
            animation: pulse-ring 1.5s ease-out infinite;
        }

        /* Glass morphism */
        .glass {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
        }

        /* Safe area for mobile */
        .safe-top { padding-top: env(safe-area-inset-top, 16px); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom, 16px); }
    </style>
</head>
<body class="antialiased relative bg-slate-950">

    {{-- Modern Header --}}
    <header class="absolute top-0 left-0 right-0 z-[1000] safe-top">
        <div class="flex items-center justify-between px-4 py-3 sm:px-6 sm:py-4">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="glass px-4 py-2 sm:px-5 sm:py-2.5 rounded-2xl border border-white/10 shadow-xl flex items-center gap-2 hover:border-indigo-500/50 transition-all group">
                <span class="text-xl sm:text-2xl">💎</span>
                <span class="font-black text-white text-sm sm:text-base tracking-tight hidden sm:inline">GeoKapsül</span>
            </a>

            {{-- Navigation --}}
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Gamification Links --}}
                <a href="{{ route('leaderboard') }}" class="glass px-3 py-2.5 sm:px-4 sm:py-3 rounded-2xl border border-white/10 text-white font-bold text-xs shadow-xl hover:bg-white/10 hover:border-amber-500/50 transition-all flex items-center gap-1.5" title="Liderlik">
                    <span>🏅</span>
                    <span class="hidden md:inline">Liderlik</span>
                </a>
                <a href="{{ route('badges') }}" class="glass px-3 py-2.5 sm:px-4 sm:py-3 rounded-2xl border border-white/10 text-white font-bold text-xs shadow-xl hover:bg-white/10 hover:border-violet-500/50 transition-all flex items-center gap-1.5" title="Rozetler">
                    <span>🏆</span>
                    <span class="hidden md:inline">Rozetler</span>
                </a>
                
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="glass px-4 py-2.5 sm:px-6 sm:py-3 rounded-2xl border border-indigo-500/50 text-white font-bold text-xs sm:text-sm shadow-xl hover:bg-indigo-600/50 hover:border-indigo-400 transition-all flex items-center gap-2">
                            <span class="hidden sm:inline">Panelim</span>
                            <span>🚀</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="glass px-4 py-2.5 sm:px-5 sm:py-3 rounded-2xl border border-white/10 text-white font-bold text-xs sm:text-sm shadow-xl hover:bg-white/10 transition-all">
                            Giriş
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-2.5 sm:px-5 sm:py-3 rounded-2xl text-white font-bold text-xs sm:text-sm shadow-xl hover:shadow-indigo-500/30 hover:scale-105 transition-all">
                                Kayıt Ol
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    {{-- Category Filter --}}
    <div class="absolute top-20 left-4 z-[1000] glass border border-white/10 rounded-2xl p-2 shadow-xl hidden sm:block">
        <div class="flex flex-col gap-1">
            <button onclick="filterCategory(null)" class="category-btn active px-3 py-2 rounded-xl text-xs font-bold text-white hover:bg-white/10 transition-all flex items-center gap-2" data-category="all">
                <span>🌍</span> Tümü
            </button>
            <button onclick="filterCategory('memory')" class="category-btn px-3 py-2 rounded-xl text-xs font-bold text-slate-400 hover:bg-white/10 hover:text-white transition-all flex items-center gap-2" data-category="memory">
                <span>💭</span> Anı
            </button>
            <button onclick="filterCategory('gift')" class="category-btn px-3 py-2 rounded-xl text-xs font-bold text-slate-400 hover:bg-white/10 hover:text-white transition-all flex items-center gap-2" data-category="gift">
                <span>🎁</span> Hediye
            </button>
            <button onclick="filterCategory('mystery')" class="category-btn px-3 py-2 rounded-xl text-xs font-bold text-slate-400 hover:bg-white/10 hover:text-white transition-all flex items-center gap-2" data-category="mystery">
                <span>🔮</span> Gizem
            </button>
            <button onclick="filterCategory('treasure')" class="category-btn px-3 py-2 rounded-xl text-xs font-bold text-slate-400 hover:bg-white/10 hover:text-white transition-all flex items-center gap-2" data-category="treasure">
                <span>💎</span> Hazine
            </button>
        </div>
    </div>

    {{-- Modern Radar Panel --}}
    <div id="radar-panel" class="absolute bottom-6 sm:bottom-10 left-1/2 -translate-x-1/2 z-[1000] glass border border-cyan-500/50 rounded-3xl px-5 py-3 sm:px-8 sm:py-4 shadow-[0_0_40px_rgba(6,182,212,0.2)] flex items-center gap-4 sm:gap-6 transition-all duration-500 hidden safe-bottom">
        <div class="relative">
            <span id="radar-icon" class="text-2xl sm:text-3xl block">📡</span>
            <div class="absolute inset-0 bg-cyan-500/20 rounded-full animate-ping"></div>
        </div>
        <div class="flex flex-col">
            <span id="radar-text" class="text-cyan-400 font-black tracking-widest text-[9px] sm:text-[10px] uppercase">Sinyal Aranıyor...</span>
            <span id="radar-distance" class="text-white font-black text-lg sm:text-xl tabular-nums">--- m</span>
        </div>
        <div id="radar-bars" class="flex items-end gap-1 h-6">
            <div class="w-1 bg-cyan-500/30 rounded-full h-2"></div>
            <div class="w-1 bg-cyan-500/30 rounded-full h-3"></div>
            <div class="w-1 bg-cyan-500/30 rounded-full h-4"></div>
            <div class="w-1 bg-cyan-500/30 rounded-full h-5"></div>
            <div class="w-1 bg-cyan-500/30 rounded-full h-6"></div>
        </div>
    </div>

    {{-- Floating Add Button (Mobile) --}}
    @auth
    <button id="add-capsule-btn" class="fixed bottom-24 right-4 z-[1000] sm:hidden w-14 h-14 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-full shadow-xl shadow-indigo-500/30 flex items-center justify-center text-2xl text-white active:scale-95 transition-all border-2 border-white/20">
        ✨
    </button>
    @endauth

    {{-- Map Container --}}
    <div id="map"></div>

    {{-- Stats Badge --}}
    <div class="absolute bottom-6 left-4 z-[1000] glass px-3 py-2 rounded-xl border border-white/10 text-[10px] sm:text-xs text-slate-400 font-bold hidden sm:flex items-center gap-2 safe-bottom">
        <span class="text-indigo-400">{{ count($capsules ?? []) }}</span> kapsül keşfedilmeyi bekliyor
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map', {
            zoomControl: false
        }).setView([38.626, 34.714], 13);

        // Zoom control sağ alta
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 20
        }).addTo(map);

        var userLocation = null;
        var userMarker = null;
        var capsules = @json($capsules ?? []);
        var isFirstLocationFound = false;

        var radarPanel = document.getElementById('radar-panel');
        var radarText = document.getElementById('radar-text');
        var radarDistance = document.getElementById('radar-distance');
        var radarIcon = document.getElementById('radar-icon');
        var radarBars = document.getElementById('radar-bars');
        var lastDistance = null;

        // Category icons and colors
        var categoryConfig = {
            memory: { icon: '💭', color: 'indigo' },
            gift: { icon: '🎁', color: 'rose' },
            mystery: { icon: '🔮', color: 'violet' },
            game: { icon: '🎮', color: 'emerald' },
            anniversary: { icon: '🎂', color: 'amber' },
            treasure: { icon: '💎', color: 'cyan' }
        };

        // Get capsule icon based on category
        function getCapsuleIcon(category) {
            var config = categoryConfig[category] || categoryConfig.memory;
            var colorClass = {
                indigo: 'from-indigo-500 to-violet-500 shadow-indigo-500/50 bg-indigo-500/30',
                rose: 'from-rose-500 to-pink-500 shadow-rose-500/50 bg-rose-500/30',
                violet: 'from-violet-500 to-purple-500 shadow-violet-500/50 bg-violet-500/30',
                emerald: 'from-emerald-500 to-green-500 shadow-emerald-500/50 bg-emerald-500/30',
                amber: 'from-amber-500 to-yellow-500 shadow-amber-500/50 bg-amber-500/30',
                cyan: 'from-cyan-500 to-blue-500 shadow-cyan-500/50 bg-cyan-500/30'
            }[config.color];
            
            return L.divIcon({
                className: 'capsule-marker',
                html: `<div class="relative flex items-center justify-center">
                         <div class="absolute w-8 h-8 ${colorClass.split(' ')[2]} rounded-full animate-ping"></div>
                         <div class="w-6 h-6 bg-gradient-to-br ${colorClass.split(' ').slice(0,2).join(' ')} rounded-full shadow-lg ${colorClass.split(' ')[2]} flex items-center justify-center text-xs">${config.icon}</div>
                       </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });
        }

        // Custom user icon
        var userIcon = L.divIcon({
            className: 'user-marker',
            html: `<div class="relative flex items-center justify-center">
                     <div class="absolute w-10 h-10 bg-emerald-500/20 rounded-full animate-ping"></div>
                     <div class="absolute w-6 h-6 bg-emerald-500/30 rounded-full animate-pulse"></div>
                     <div class="w-4 h-4 bg-gradient-to-br from-emerald-400 to-cyan-400 rounded-full shadow-lg shadow-emerald-500/50 border-2 border-white"></div>
                   </div>`,
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        // Capsule markers layer
        var capsuleMarkers = [];
        var currentFilter = null;

        // Filter capsules by category
        function filterCategory(category) {
            currentFilter = category;
            
            // Update button states
            document.querySelectorAll('.category-btn').forEach(btn => {
                var btnCat = btn.dataset.category;
                if ((category === null && btnCat === 'all') || btnCat === category) {
                    btn.classList.add('active', 'text-white', 'bg-white/10');
                    btn.classList.remove('text-slate-400');
                } else {
                    btn.classList.remove('active', 'text-white', 'bg-white/10');
                    btn.classList.add('text-slate-400');
                }
            });

            // Update markers visibility
            capsuleMarkers.forEach(function(item) {
                if (category === null || item.category === category) {
                    if (!map.hasLayer(item.marker)) {
                        item.marker.addTo(map);
                    }
                } else {
                    if (map.hasLayer(item.marker)) {
                        map.removeLayer(item.marker);
                    }
                }
            });
        }

        map.locate({watch: true, enableHighAccuracy: true});

        function updateRadarBars(level) {
            var bars = radarBars.children;
            var colors = {
                4: ['bg-emerald-500', 'bg-emerald-500', 'bg-emerald-500', 'bg-emerald-500', 'bg-emerald-500'],
                3: ['bg-orange-500', 'bg-orange-500', 'bg-orange-500', 'bg-orange-500/30', 'bg-orange-500/30'],
                2: ['bg-amber-500', 'bg-amber-500', 'bg-amber-500/30', 'bg-amber-500/30', 'bg-amber-500/30'],
                1: ['bg-cyan-500', 'bg-cyan-500/30', 'bg-cyan-500/30', 'bg-cyan-500/30', 'bg-cyan-500/30']
            };
            var colorSet = colors[level] || colors[1];
            for(var i = 0; i < bars.length; i++) {
                bars[i].className = 'w-1 rounded-full ' + colorSet[i];
                bars[i].style.height = ((i + 2) * 4) + 'px';
            }
        }

        map.on('locationfound', function(e) {
            userLocation = e.latlng;
            if(!isFirstLocationFound) {
                map.setView(e.latlng, 15);
                isFirstLocationFound = true;
                if(capsules.length > 0) radarPanel.classList.remove('hidden');
            }

            if (!userMarker) {
                userMarker = L.marker(e.latlng, { icon: userIcon }).addTo(map);
            } else {
                userMarker.setLatLng(e.latlng);
            }

            if (capsules.length > 0) {
                let minDistance = Infinity;
                capsules.forEach(function(c) {
                    let d = userLocation.distanceTo([c.latitude, c.longitude]);
                    if (d < minDistance) { minDistance = d; }
                });

                let mesafe = Math.round(minDistance);
                if (lastDistance === null || Math.abs(lastDistance - mesafe) > 4) {
                    lastDistance = mesafe;
                    radarDistance.innerText = mesafe >= 1000 ? (mesafe/1000).toFixed(1) + " km" : mesafe + " m";

                    if (mesafe <= 100) {
                        radarPanel.className = "absolute bottom-6 sm:bottom-10 left-1/2 -translate-x-1/2 z-[1000] glass border-2 border-emerald-500 rounded-3xl px-5 py-3 sm:px-8 sm:py-4 shadow-[0_0_60px_rgba(16,185,129,0.4)] flex items-center gap-4 sm:gap-6 transition-all duration-500 scale-105 safe-bottom";
                        radarText.innerText = "HEDEF BÖLGESİ!";
                        radarText.className = "text-emerald-400 font-black tracking-widest text-[9px] sm:text-[10px] uppercase";
                        radarIcon.innerText = "🎯";
                        updateRadarBars(4);
                    }
                    else if (mesafe <= 500) {
                        radarPanel.className = "absolute bottom-6 sm:bottom-10 left-1/2 -translate-x-1/2 z-[1000] glass border-2 border-orange-500 rounded-3xl px-5 py-3 sm:px-8 sm:py-4 shadow-[0_0_40px_rgba(249,115,22,0.3)] flex items-center gap-4 sm:gap-6 transition-all duration-500 safe-bottom";
                        radarText.innerText = "ÇOK SICAK!";
                        radarText.className = "text-orange-400 font-black tracking-widest text-[9px] sm:text-[10px] uppercase animate-pulse";
                        radarIcon.innerText = "🔥";
                        updateRadarBars(3);
                    }
                    else if (mesafe <= 2000) {
                        radarPanel.className = "absolute bottom-6 sm:bottom-10 left-1/2 -translate-x-1/2 z-[1000] glass border border-amber-500/50 rounded-3xl px-5 py-3 sm:px-8 sm:py-4 shadow-[0_0_30px_rgba(245,158,11,0.2)] flex items-center gap-4 sm:gap-6 transition-all duration-500 safe-bottom";
                        radarText.innerText = "YAKLAŞIYORSUN";
                        radarText.className = "text-amber-400 font-black tracking-widest text-[9px] sm:text-[10px] uppercase";
                        radarIcon.innerText = "🚶";
                        updateRadarBars(2);
                    }
                    else {
                        radarPanel.className = "absolute bottom-6 sm:bottom-10 left-1/2 -translate-x-1/2 z-[1000] glass border border-cyan-500/50 rounded-3xl px-5 py-3 sm:px-8 sm:py-4 shadow-[0_0_40px_rgba(6,182,212,0.2)] flex items-center gap-4 sm:gap-6 transition-all duration-500 safe-bottom";
                        radarText.innerText = "SİNYAL ZAYIF";
                        radarText.className = "text-cyan-400 font-black tracking-widest text-[9px] sm:text-[10px] uppercase";
                        radarIcon.innerText = "📡";
                        updateRadarBars(1);
                    }
                }
            }
        });

        map.on('locationerror', function(e) {
            console.log('Konum alınamadı:', e.message);
        });

        window.verifyPin = function(id, inputElement) {
            let pinValue = inputElement.value;
            let errorMsg = document.getElementById('pin-error-' + id);
            
            fetch('/kapsul/' + id + '?pin=' + encodeURIComponent(pinValue))
                .then(response => response.json())
                .then(data => {
                    if (data.locked && data.error) {
                        errorMsg.classList.remove('hidden');
                        inputElement.classList.add('border-rose-500', 'bg-rose-50');
                        inputElement.value = '';
                        setTimeout(() => {
                            inputElement.classList.remove('border-rose-500', 'bg-rose-50');
                        }, 500);
                    } else if (!data.locked) {
                        document.getElementById('pin-screen-' + id).classList.add('hidden');
                        showCapsuleContent(id, data.capsule);
                    }
                });
        }

        window.showCapsuleContent = function(id, capsule) {
            let contentDiv = document.getElementById('capsule-content-' + id);
            let audioHtml = capsule.audio ? `<audio controls class="w-full mt-3 h-12 rounded-xl"><source src="${capsule.audio}">Tarayıcınız sesi desteklemiyor.</audio>` : '';
            let imageHtml = capsule.image ? `<img src="${capsule.image}" alt="Kapsül Anısı" class="w-full h-40 object-cover rounded-2xl shadow-lg mt-3">` : '';
            
            contentDiv.innerHTML = `<div class="text-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-cyan-400 rounded-full flex items-center justify-center mx-auto mb-3 text-xl shadow-lg">🔓</div>
                                        <h3 class="text-emerald-500 font-black text-base mb-3 uppercase tracking-widest">Kapsül Açıldı!</h3>
                                        <p class="text-slate-700 font-semibold italic bg-slate-100 p-4 rounded-2xl text-sm leading-relaxed">"${capsule.message}"</p>
                                        ${audioHtml}
                                        ${imageHtml}
                                    </div>`;
            contentDiv.classList.remove('hidden');
        }

        window.loadCapsuleContent = function(id, marker) {
            fetch('/kapsul/' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.locked) {
                        if (data.lock_type === 'time') {
                            let html = `<div class="text-center min-w-[260px] p-2">
                                <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl shadow-lg">⏳</div>
                                <h3 class="text-amber-600 font-black text-base mb-1 uppercase tracking-widest">Zaman Kilidi</h3>
                                <p class="text-slate-500 font-medium text-xs mb-4">Bu kapsül henüz açılamaz</p>
                                <div class="bg-gradient-to-br from-slate-100 to-slate-50 rounded-2xl p-4 shadow-inner">
                                    <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mb-1">Açılış Tarihi</p>
                                    <p class="text-slate-800 font-black text-lg">${data.unlock_date}</p>
                                </div>
                            </div>`;
                            marker.bindPopup(html, {className: 'modern-popup'}).openPopup();
                        } else if (data.lock_type === 'pin') {
                            let html = `<div class="text-center min-w-[260px] p-2">
                                <div id="pin-screen-${id}">
                                    <div class="w-14 h-14 bg-gradient-to-br from-rose-400 to-pink-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl shadow-lg">🔐</div>
                                    <h3 class="text-rose-600 font-black text-base mb-1 uppercase tracking-widest">Şifreli Kapsül</h3>
                                    <p class="text-slate-500 font-medium text-xs mb-4">Bu anıyı görmek için PIN gir</p>
                                    <input type="password" id="pin-input-${id}" maxlength="4" placeholder="• • • •" class="w-32 text-center text-2xl tracking-[0.5em] bg-slate-100 border-2 border-slate-200 rounded-2xl p-3 shadow-inner focus:ring-2 focus:ring-rose-500 focus:border-rose-500 mx-auto block mb-2 font-black text-slate-700 transition-all outline-none">
                                    <p id="pin-error-${id}" class="text-rose-500 text-[10px] font-bold uppercase mb-3 hidden">Hatalı şifre!</p>
                                    <button onclick="verifyPin(${id}, document.getElementById('pin-input-${id}'))" class="w-full bg-gradient-to-r from-rose-500 to-pink-500 text-white py-3 rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-rose-500/30 active:scale-95 transition-all">Kilidi Aç</button>
                                </div>
                                <div id="capsule-content-${id}" class="hidden"></div>
                            </div>`;
                            marker.bindPopup(html, {className: 'modern-popup'}).openPopup();
                        }
                    } else {
                        let capsule = data.capsule;
                        let audioHtml = capsule.audio ? `<audio controls class="w-full mt-3 h-12 rounded-xl"><source src="${capsule.audio}">Tarayıcınız sesi desteklemiyor.</audio>` : '';
                        let imageHtml = capsule.image ? `<img src="${capsule.image}" alt="Kapsül Anısı" class="w-full h-40 object-cover rounded-2xl shadow-lg mt-3">` : '';
                        let html = `<div class="text-center min-w-[260px] p-2">
                            <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-cyan-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl shadow-lg">🔓</div>
                            <h3 class="text-emerald-600 font-black text-base mb-3 uppercase tracking-widest">Kapsül Açıldı!</h3>
                            <p class="text-slate-700 font-semibold italic bg-slate-100 p-4 rounded-2xl text-sm leading-relaxed">"${capsule.message}"</p>
                            ${audioHtml}
                            ${imageHtml}
                        </div>`;
                        marker.bindPopup(html, {className: 'modern-popup'}).openPopup();
                    }
                });
        }

        var popup = L.popup({ className: 'modern-popup', maxWidth: 320 });

        function openCapsuleForm(latlng) {
            const lat = latlng.lat;
            const lng = latlng.lng;
            const today = new Date().toISOString().split('T')[0];

            const formHtml = `
                <form action="/kapsul-kaydet" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3 min-w-[280px] p-1">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="latitude" value="${lat}">
                    <input type="hidden" name="longitude" value="${lng}">

                    <div class="text-center mb-1">
                        <span class="text-3xl">✨</span>
                        <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mt-1">Yeni Kapsül</h3>
                    </div>

                    <textarea name="message" required rows="2" class="border-2 border-slate-200 rounded-2xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 resize-none transition-all outline-none" placeholder="Anını buraya yaz..."></textarea>

                    <!-- Kategori Seçimi -->
                    <div>
                        <label class="font-bold text-slate-500 text-[9px] uppercase tracking-widest block mb-2 ml-1">🏷️ Kategori</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            <label class="cursor-pointer">
                                <input type="radio" name="category" value="memory" class="hidden peer" checked>
                                <div class="peer-checked:bg-indigo-100 peer-checked:border-indigo-400 peer-checked:text-indigo-600 bg-slate-50 border-2 border-slate-200 rounded-xl py-2 px-1 text-center text-[10px] font-bold text-slate-500 hover:border-indigo-300 transition-all">
                                    💭 Anı
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="category" value="gift" class="hidden peer">
                                <div class="peer-checked:bg-rose-100 peer-checked:border-rose-400 peer-checked:text-rose-600 bg-slate-50 border-2 border-slate-200 rounded-xl py-2 px-1 text-center text-[10px] font-bold text-slate-500 hover:border-rose-300 transition-all">
                                    🎁 Hediye
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="category" value="mystery" class="hidden peer">
                                <div class="peer-checked:bg-violet-100 peer-checked:border-violet-400 peer-checked:text-violet-600 bg-slate-50 border-2 border-slate-200 rounded-xl py-2 px-1 text-center text-[10px] font-bold text-slate-500 hover:border-violet-300 transition-all">
                                    🔮 Gizem
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="category" value="game" class="hidden peer">
                                <div class="peer-checked:bg-emerald-100 peer-checked:border-emerald-400 peer-checked:text-emerald-600 bg-slate-50 border-2 border-slate-200 rounded-xl py-2 px-1 text-center text-[10px] font-bold text-slate-500 hover:border-emerald-300 transition-all">
                                    🎮 Oyun
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="category" value="anniversary" class="hidden peer">
                                <div class="peer-checked:bg-amber-100 peer-checked:border-amber-400 peer-checked:text-amber-600 bg-slate-50 border-2 border-slate-200 rounded-xl py-2 px-1 text-center text-[10px] font-bold text-slate-500 hover:border-amber-300 transition-all">
                                    🎂 Yıldönümü
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="category" value="treasure" class="hidden peer">
                                <div class="peer-checked:bg-cyan-100 peer-checked:border-cyan-400 peer-checked:text-cyan-600 bg-slate-50 border-2 border-slate-200 rounded-xl py-2 px-1 text-center text-[10px] font-bold text-slate-500 hover:border-cyan-300 transition-all">
                                    💎 Hazine
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-500 text-[9px] uppercase tracking-widest block mb-1 ml-1">📅 Tarih Kilidi</label>
                            <input type="date" name="unlock_date" min="${today}" class="w-full border-2 border-slate-200 rounded-xl p-2.5 text-xs text-slate-600 focus:ring-2 focus:ring-indigo-500 bg-slate-50 transition-all outline-none">
                        </div>
                        <div>
                            <label class="font-bold text-slate-500 text-[9px] uppercase tracking-widest block mb-1 ml-1">🔐 PIN Kodu</label>
                            <input type="text" name="pin_code" maxlength="4" placeholder="••••" class="w-full border-2 border-slate-200 rounded-xl p-2.5 text-xs text-center tracking-widest font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 bg-slate-50 transition-all outline-none" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <label class="flex-1 cursor-pointer">
                            <input type="file" name="image" accept="image/*" class="hidden" onchange="this.parentElement.querySelector('span').innerHTML = '✅ Eklendi'">
                            <span class="w-full bg-slate-100 border-2 border-slate-200 text-slate-600 py-3 rounded-xl text-[10px] font-bold flex items-center justify-center gap-1 hover:border-indigo-300 hover:text-indigo-600 transition-all">📸 Fotoğraf</span>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="file" name="audio" accept="audio/*" class="hidden" onchange="this.parentElement.querySelector('span').innerHTML = '✅ Eklendi'">
                            <span class="w-full bg-slate-100 border-2 border-slate-200 text-slate-600 py-3 rounded-xl text-[10px] font-bold flex items-center justify-center gap-1 hover:border-indigo-300 hover:text-indigo-600 transition-all">🎙️ Ses</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 text-white py-3.5 rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-indigo-500/30 active:scale-[0.98] transition-all">
                        💎 Kapsülü Göm
                    </button>
                </form>
            `;
            popup.setLatLng(latlng).setContent(formHtml).openOn(map);
        }

        map.on('click', function(e) {
            @auth
                openCapsuleForm(e.latlng);
            @else
                popup.setLatLng(e.latlng).setContent(`
                    <div class='text-center p-4 min-w-[200px]'>
                        <span class='text-4xl block mb-3'>🔒</span>
                        <p class='font-bold text-slate-700 mb-3'>Kapsül gömmek için giriş yapmalısın</p>
                        <a href='/login' class='inline-block w-full bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-6 py-3 rounded-2xl font-bold text-sm shadow-lg hover:shadow-indigo-500/30 transition-all'>Giriş Yap</a>
                    </div>
                `).openOn(map);
            @endauth
        });

        // Mobile FAB handler
        @auth
        document.getElementById('add-capsule-btn')?.addEventListener('click', function() {
            if(userLocation) {
                openCapsuleForm(userLocation);
            } else {
                alert('Konum izni vermelisin!');
            }
        });
        @endauth

        capsules.forEach(function(capsule) {
            var icon = getCapsuleIcon(capsule.category || 'memory');
            var marker = L.marker([capsule.latitude, capsule.longitude], { icon: icon }).addTo(map);
            
            // Store marker with category for filtering
            capsuleMarkers.push({ marker: marker, category: capsule.category || 'memory' });
            
            marker.on('click', function() {
                if (!userLocation) {
                    marker.bindPopup(`<div class='text-center p-4'><span class='text-4xl block mb-2'>📍</span><p class='font-bold text-slate-700'>Konum izni vermelisin!</p></div>`).openPopup();
                    return;
                }

                var distance = userLocation.distanceTo([capsule.latitude, capsule.longitude]);
                if (distance <= 100) {
                    loadCapsuleContent(capsule.id, marker);
                } else {
                    let distText = distance >= 1000 ? (distance/1000).toFixed(1) + ' km' : Math.round(distance) + ' m';
                    var catInfo = categoryConfig[capsule.category] || categoryConfig.memory;
                    marker.bindPopup(`
                        <div class="text-center p-3 min-w-[220px]">
                            <div class="w-14 h-14 bg-gradient-to-br from-rose-400 to-pink-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl shadow-lg">🔒</div>
                            <h3 class="text-rose-600 font-black text-base mb-1 uppercase tracking-widest">Çok Uzaksın!</h3>
                            <p class="text-slate-500 font-medium text-xs mb-3">Kilidi kırmak için yaklaşmalısın</p>
                            <div class="flex items-center justify-center gap-2 mb-3">
                                <span class="text-lg">${catInfo.icon}</span>
                                <span class="text-slate-400 text-xs font-bold">${capsule.category ? capsule.category.charAt(0).toUpperCase() + capsule.category.slice(1) : 'Anı'} Kapsülü</span>
                            </div>
                            <div class="bg-gradient-to-br from-rose-100 to-pink-100 rounded-2xl p-3">
                                <span class="text-rose-600 font-black text-xl">${distText}</span>
                                <p class="text-rose-400 text-[10px] font-bold uppercase">uzaklık</p>
                            </div>
                        </div>
                    `).openPopup();
                }
            });
        });

        // Service Worker Kaydı (PWA)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW kayıtlı:', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('SW hatası:', error);
                    });
            });
        }
    </script>

    {{-- Onboarding Modal (İlk Ziyaretçiler İçin) --}}
    @guest
    <div x-data="{ show: !localStorage.getItem('onboardingDone') }" x-show="show" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[2000] flex items-center justify-center p-4">
        
        <div x-data="{ step: 1 }" class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-white/10">
            
            {{-- Step 1: Hoşgeldin --}}
            <div x-show="step === 1" class="text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                    <span class="text-5xl">🌍</span>
                </div>
                <h2 class="text-2xl font-black text-white mb-3">GeoKapsül'e Hoşgeldin!</h2>
                <p class="text-slate-400 mb-6">Dijital anılarını gerçek dünya konumlarına göm ve gelecekte keşfet.</p>
                <button @click="step = 2" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 text-white py-4 rounded-2xl font-bold shadow-lg hover:shadow-indigo-500/30 transition-all">
                    Nasıl Çalışır? →
                </button>
            </div>

            {{-- Step 2: Nasıl Çalışır --}}
            <div x-show="step === 2" x-cloak class="text-center">
                <div class="space-y-4 mb-6">
                    <div class="flex items-center gap-4 bg-slate-700/50 rounded-2xl p-4">
                        <span class="text-3xl">📍</span>
                        <div class="text-left">
                            <p class="text-white font-bold">Haritaya Tıkla</p>
                            <p class="text-slate-400 text-sm">İstediğin konuma kapsül göm</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-slate-700/50 rounded-2xl p-4">
                        <span class="text-3xl">🔐</span>
                        <div class="text-left">
                            <p class="text-white font-bold">Kilitle</p>
                            <p class="text-slate-400 text-sm">Tarih veya PIN ile koru</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-slate-700/50 rounded-2xl p-4">
                        <span class="text-3xl">🚶</span>
                        <div class="text-left">
                            <p class="text-white font-bold">Keşfet</p>
                            <p class="text-slate-400 text-sm">100m'ye yaklaş ve aç</p>
                        </div>
                    </div>
                </div>
                <button @click="step = 3" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 text-white py-4 rounded-2xl font-bold shadow-lg hover:shadow-indigo-500/30 transition-all">
                    Anladım! →
                </button>
            </div>

            {{-- Step 3: Konum İzni --}}
            <div x-show="step === 3" x-cloak class="text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                    <span class="text-5xl">📡</span>
                </div>
                <h2 class="text-2xl font-black text-white mb-3">Konum İzni</h2>
                <p class="text-slate-400 mb-6">Kapsülleri keşfetmek için konum iznine ihtiyacımız var. Verilerini asla paylaşmıyoruz.</p>
                <button @click="localStorage.setItem('onboardingDone', 'true'); show = false" 
                        class="w-full bg-gradient-to-r from-cyan-600 to-blue-600 text-white py-4 rounded-2xl font-bold shadow-lg hover:shadow-cyan-500/30 transition-all">
                    🚀 Başla!
                </button>
                <p class="text-slate-500 text-xs mt-4">Tarayıcı konum izni istediğinde "İzin Ver" seçeneğini tıkla.</p>
            </div>

        </div>
    </div>
    @endguest

</body>
</html>

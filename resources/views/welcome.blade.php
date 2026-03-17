<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GeoKapsül - Dijital Anılar</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 100vh; width: 100%; z-index: 1; }
        body { margin: 0; padding: 0; overflow: hidden; }
        .leaflet-popup-content { margin: 14px; }
        /* Ses oynatıcıyı modernleştiren küçük bir dokunuş */
        audio::-webkit-media-controls-panel { background-color: #f8fafc; }
    </style>
</head>
<body class="antialiased relative">

    <div class="absolute top-4 right-4 z-[1000] flex gap-2">
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-full font-bold shadow-lg hover:bg-indigo-700 transition-all border-2 border-indigo-400/50">Panelim 🚀</a>
            @else
                <a href="{{ route('login') }}" class="bg-white text-slate-800 px-6 py-2 rounded-full font-bold shadow-lg hover:bg-slate-100 transition-all border border-slate-200">Giriş Yap</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-full font-bold shadow-lg hover:bg-indigo-700 transition-all">Kayıt Ol</a>
                @endif
            @endauth
        @endif
    </div>

    <div id="radar-panel" class="absolute bottom-10 left-1/2 -translate-x-1/2 z-[1000] bg-slate-900/90 border-2 border-cyan-500 rounded-full px-6 py-3 shadow-[0_0_15px_rgba(6,182,212,0.3)] flex items-center gap-4 backdrop-blur-md transition-all duration-500 hidden">
        <span id="radar-icon" class="text-2xl animate-pulse">📡</span>
        <div class="flex flex-col">
            <span id="radar-text" class="text-cyan-400 font-black tracking-widest text-[10px] uppercase">Sinyal Aranıyor...</span>
            <span id="radar-distance" class="text-white font-black text-lg">--- m</span>
        </div>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([38.626, 34.714], 13);

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
        var lastDistance = null;

        map.locate({watch: true, enableHighAccuracy: true});

        map.on('locationfound', function(e) {
            userLocation = e.latlng;
            if(!isFirstLocationFound) {
                map.setView(e.latlng, 15);
                isFirstLocationFound = true;
                if(capsules.length > 0) radarPanel.classList.remove('hidden');
            }

            if (!userMarker) {
                userMarker = L.circleMarker(e.latlng, { radius: 8, fillColor: "#4f46e5", color: "#ffffff", weight: 2, opacity: 1, fillOpacity: 0.8 }).addTo(map).bindPopup("<b class='text-indigo-600'>Buradasın! 📍</b>");
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
                    radarDistance.innerText = mesafe + " Metre";

                    if (mesafe <= 100) {
                        radarPanel.className = "absolute bottom-10 left-1/2 -translate-x-1/2 z-[1000] bg-emerald-900/95 border-2 border-emerald-500 rounded-full px-6 py-3 shadow-[0_0_30px_rgba(16,185,129,0.6)] flex items-center gap-4 backdrop-blur-md transition-all duration-500 scale-110";
                        radarText.innerText = "HEDEF BÖLGESİ!";
                        radarText.className = "text-emerald-400 font-black tracking-widest text-[10px] uppercase";
                        radarIcon.innerText = "🔓";
                    }
                    else if (mesafe <= 500) {
                        radarPanel.className = "absolute bottom-10 left-1/2 -translate-x-1/2 z-[1000] bg-orange-900/95 border-2 border-orange-500 rounded-full px-6 py-3 shadow-[0_0_20px_rgba(249,115,22,0.5)] flex items-center gap-4 backdrop-blur-md transition-all duration-500";
                        radarText.innerText = "ÇOK SICAK...";
                        radarText.className = "text-orange-400 font-black tracking-widest text-[10px] uppercase animate-pulse";
                        radarIcon.innerText = "🔥";
                    }
                    else if (mesafe <= 2000) {
                        radarPanel.className = "absolute bottom-10 left-1/2 -translate-x-1/2 z-[1000] bg-amber-900/90 border-2 border-amber-500 rounded-full px-6 py-3 shadow-[0_0_15px_rgba(245,158,11,0.3)] flex items-center gap-4 backdrop-blur-md transition-all duration-500";
                        radarText.innerText = "YAKLAŞIYORSUN";
                        radarText.className = "text-amber-400 font-black tracking-widest text-[10px] uppercase";
                        radarIcon.innerText = "🚶‍♂️";
                    }
                    else {
                        radarPanel.className = "absolute bottom-10 left-1/2 -translate-x-1/2 z-[1000] bg-slate-900/90 border-2 border-cyan-500 rounded-full px-6 py-3 shadow-[0_0_15px_rgba(6,182,212,0.3)] flex items-center gap-4 backdrop-blur-md transition-all duration-500";
                        radarText.innerText = "SİNYAL ZAYIF";
                        radarText.className = "text-cyan-400 font-black tracking-widest text-[10px] uppercase";
                        radarIcon.innerText = "📡";
                    }
                }
            }
        });

        window.verifyPin = function(id, correctPin) {
            let inputElement = document.getElementById('pin-input-' + id);
            let inputValue = inputElement.value;
            let errorMsg = document.getElementById('pin-error-' + id);

            if (inputValue === correctPin) {
                document.getElementById('pin-screen-' + id).classList.add('hidden');
                document.getElementById('capsule-content-' + id).classList.remove('hidden');
            } else {
                errorMsg.classList.remove('hidden');
                inputElement.classList.add('border-rose-500', 'bg-rose-50');
                inputElement.value = '';
                setTimeout(() => {
                    inputElement.classList.remove('border-rose-500', 'bg-rose-50');
                }, 500);
            }
        }

        var popup = L.popup();

        map.on('click', function(e) {
            @auth
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                const today = new Date().toISOString().split('T')[0];

                const formHtml = `
                    <form action="/kapsul-kaydet" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2 min-w-[240px]">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="latitude" value="${lat}">
                        <input type="hidden" name="longitude" value="${lng}">

                        <label class="font-black text-slate-800 text-[11px] uppercase tracking-widest text-center mt-1">Dijital İzini Bırak</label>
                        <textarea name="message" required rows="2" class="border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 bg-slate-50 shadow-inner resize-none" placeholder="O anı kelimelere dök..."></textarea>

                        <div class="flex flex-col gap-2">
                            <div>
                                <label class="font-black text-slate-800 text-[9px] uppercase tracking-widest block mb-1">Tarih Kilidi (İsteğe Bağlı)</label>
                                <input type="date" name="unlock_date" min="${today}" class="border border-slate-200 rounded-xl p-2 text-xs text-slate-600 focus:ring-2 focus:ring-indigo-500 bg-slate-50 shadow-inner w-full cursor-pointer">
                            </div>

                            <div class="border border-slate-200 rounded-xl p-2 bg-slate-50 shadow-inner transition-all">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" onchange="let box = document.getElementById('pin_box'); let input = document.getElementById('pin_input'); if(this.checked) { box.classList.remove('hidden'); input.required = true; } else { box.classList.add('hidden'); input.required = false; input.value = ''; }" class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300 cursor-pointer">
                                    <span class="font-black text-slate-800 text-[10px] uppercase tracking-widest cursor-pointer group-hover:text-indigo-600 transition-colors">Gizli PIN Ekle</span>
                                </label>

                                <div id="pin_box" class="hidden mt-3">
                                    <input type="text" id="pin_input" name="pin_code" maxlength="4" placeholder="****" class="border border-slate-300 rounded-lg p-2 text-xl text-center tracking-[0.4em] font-black text-slate-600 focus:ring-2 focus:ring-indigo-500 w-full" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-2">
                            <div class="flex-1 relative">
                                <input type="file" name="image" accept="image/*" class="hidden" onchange="let btn = this.nextElementSibling; btn.innerHTML = '✅ FOTO'; btn.classList.replace('bg-slate-50', 'bg-emerald-50'); btn.classList.replace('text-slate-600', 'text-emerald-600'); btn.classList.replace('border-slate-200', 'border-emerald-200');">
                                <button type="button" onclick="this.previousElementSibling.click();" class="w-full bg-slate-50 border-2 border-slate-200 text-slate-600 py-2.5 rounded-xl text-[10px] font-black transition-all shadow-sm flex items-center justify-center hover:border-indigo-300 hover:text-indigo-600">📸 FOTO</button>
                            </div>

                            <div class="flex-1 relative">
                                <input type="file" name="audio" accept="audio/*" capture="microphone" class="hidden" onchange="let btn = this.nextElementSibling; btn.innerHTML = '✅ SES'; btn.classList.replace('bg-slate-50', 'bg-emerald-50'); btn.classList.replace('text-slate-600', 'text-emerald-600'); btn.classList.replace('border-slate-200', 'border-emerald-200');">
                                <button type="button" onclick="this.previousElementSibling.click();" class="w-full bg-slate-50 border-2 border-slate-200 text-slate-600 py-2.5 rounded-xl text-[10px] font-black transition-all shadow-sm flex items-center justify-center hover:border-indigo-300 hover:text-indigo-600">🎙️ SES</button>
                            </div>

                            <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-[10px] font-black transition-all shadow-md hover:bg-indigo-700 flex items-center justify-center active:scale-95 border-2 border-indigo-600">💎 EKLE</button>
                        </div>
                    </form>
                `;
                popup.setLatLng(e.latlng).setContent(formHtml).openOn(map);
            @else
                popup.setLatLng(e.latlng).setContent("<div class='text-center font-bold p-3'>Kapsül gömmek için <br><a href='/login' class='inline-block mt-2 bg-indigo-600 text-white px-4 py-1.5 rounded-lg'>Giriş Yapmalısın 🔒</a></div>").openOn(map);
            @endauth
        });

        capsules.forEach(function(capsule) {
            var marker = L.marker([capsule.latitude, capsule.longitude]).addTo(map);
            marker.on('click', function() {
                if (!userLocation) {
                    marker.bindPopup("<div class='text-center p-3'><span class='text-3xl block mb-2'>📡</span><b>Konum izni vermelisin!</b></div>").openPopup();
                    return;
                }

                var distance = userLocation.distanceTo([capsule.latitude, capsule.longitude]);
                if (distance <= 100) {
                    let isLockedByTime = false;
                    let formattedDate = "";

                    if (capsule.unlock_date) {
                        let unlockDate = new Date(capsule.unlock_date);
                        let today = new Date();
                        today.setHours(0,0,0,0);
                        unlockDate.setHours(0,0,0,0);
                        if (today < unlockDate) {
                            isLockedByTime = true;
                            formattedDate = unlockDate.toLocaleDateString('tr-TR');
                        }
                    }

                    let contentHtml = `<div class="text-center min-w-[240px]">`;

                    // Ses ve Fotoğraf HTML Hazırlığı
                    let audioHtml = capsule.audio ? `<audio controls class="w-full mt-3 h-10 outline-none rounded-xl shadow-sm"><source src="/storage/${capsule.audio}" type="audio/mpeg">Tarayıcınız sesi desteklemiyor.</audio>` : '';
                    let imageHtml = capsule.image ? `<img src="/storage/${capsule.image}" alt="Kapsül Anısı" class="w-full h-48 object-cover rounded-2xl shadow-md border-2 border-slate-100 mt-3">` : '';

                    if (isLockedByTime) {
                        contentHtml += `<div class="w-16 h-16 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl shadow-inner border border-amber-200">⏳</div>
                                        <h3 class="text-amber-600 font-black text-lg mb-1 uppercase tracking-widest">Zaman Kilidi!</h3>
                                        <p class="text-slate-600 font-bold text-sm mb-3">Bu kapsül henüz açılamaz.</p>
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 shadow-sm">
                                            <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest mb-1">Mühür Kırılma Tarihi</p>
                                            <p class="text-slate-800 font-black text-lg">${formattedDate}</p>
                                        </div>`;
                    }
                    else if (capsule.pin_code) {
                        contentHtml += `<div id="pin-screen-${capsule.id}">
                                            <div class="w-16 h-16 bg-rose-100 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl shadow-inner border border-rose-200">🔒</div>
                                            <h3 class="text-rose-600 font-black text-lg mb-1 uppercase tracking-widest">Gizli Kapsül</h3>
                                            <p class="text-slate-500 font-bold text-[11px] mb-3">Bu anıyı görmek için şifreyi girmelisin.</p>

                                            <input type="password" id="pin-input-${capsule.id}" maxlength="4" placeholder="****" class="w-24 text-center text-2xl tracking-[0.4em] bg-slate-50 border border-slate-200 rounded-xl p-2 shadow-inner focus:ring-2 focus:ring-rose-500 mx-auto block mb-1 font-black text-slate-700 transition-colors">
                                            <p id="pin-error-${capsule.id}" class="text-rose-500 text-[10px] font-black uppercase mb-3 hidden transition-all">Hatalı Şifre!</p>

                                            <button onclick="verifyPin(${capsule.id}, '${capsule.pin_code}')" class="w-full bg-rose-600 text-white py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-md hover:bg-rose-700 active:scale-95 border-2 border-rose-600 hover:border-rose-700">KİLİDİ KIR</button>
                                        </div>`;

                        contentHtml += `<div id="capsule-content-${capsule.id}" class="hidden">
                                            <h3 class="text-emerald-500 font-black text-lg mb-3 uppercase tracking-widest">Kapsül Açıldı 🔓</h3>
                                            <p class="text-slate-800 font-bold italic bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-0 shadow-sm">"${capsule.message}"</p>
                                            ${audioHtml}
                                            ${imageHtml}
                                        </div>`;
                    }
                    else {
                        contentHtml += `<h3 class="text-emerald-500 font-black text-lg mb-3 uppercase tracking-widest">Kapsül Açıldı 🔓</h3>
                                        <p class="text-slate-800 font-bold italic bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-0 shadow-sm">"${capsule.message}"</p>
                                        ${audioHtml}
                                        ${imageHtml}`;
                    }

                    contentHtml += `</div>`;
                    marker.bindPopup(contentHtml).openPopup();

                } else {
                    marker.bindPopup(`<div class="text-center p-3 min-w-[200px]"><div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">🔒</div><h3 class="text-rose-600 font-black text-lg mb-1 uppercase tracking-widest">Çok Uzaksın!</h3><p class="text-slate-600 font-bold text-sm">Kilidi kırmak için <br><b class="text-rose-500 text-lg">${Math.round(distance)}m</b><br> daha yaklaşmalısın.</p></div>`).openPopup();
                }
            });
        });
    </script>
</body>
</html>

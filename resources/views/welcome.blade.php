<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeoKapsül - Gizli Anılarını Keşfet</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; }
        #map { height: 100vh; width: 100%; z-index: 10; }
        .leaflet-popup-content p { margin: 0 !important; }
    </style>
</head>
<body class="bg-slate-900 text-white antialiased">

    <div class="fixed top-0 left-0 w-full p-6 z-50 flex justify-between items-center pointer-events-none">
        <h1 class="text-3xl md:text-4xl font-black text-indigo-500 drop-shadow-[0_5px_5px_rgba(0,0,0,0.8)] pointer-events-auto tracking-tighter italic">
            GeoKapsül 📍
        </h1>

        <div class="pointer-events-auto flex gap-3">
            @auth
                <a href="{{ url('/dashboard') }}" class="bg-indigo-600/90 backdrop-blur-md hover:bg-indigo-500 text-white px-6 py-2.5 rounded-2xl font-bold shadow-lg transition-all hover:scale-105 border border-indigo-400/30">
                    Panelim →
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-slate-800/90 backdrop-blur-md hover:bg-slate-700 text-white px-6 py-2.5 rounded-2xl font-bold shadow-lg transition-all border border-slate-600/50">
                    Giriş Yap
                </a>
                <a href="{{ route('register') }}" class="bg-indigo-600/90 backdrop-blur-md hover:bg-indigo-500 text-white px-6 py-2.5 rounded-2xl font-bold shadow-lg transition-all hover:scale-105 border border-indigo-400/30">
                    Kayıt Ol
                </a>
            @endauth
        </div>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // 1. Haritayı başlat (Kapadokya Merkezi)
        var map = L.map('map', { zoomControl: false }).setView([38.6244, 34.7142], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; GeoKapsül',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // --- GPS VE CANLI KONUM TAKİBİ ---
        var userLocation = null; // Kullanıcının anlık konumu burada tutulacak
        var userMarker = null;   // Kullanıcıyı gösteren mavi nokta

        // Tarayıcıdan sürekli konum izni iste ve takip et
        map.locate({setView: false, watch: true, enableHighAccuracy: true});

        map.on('locationfound', function(e) {
            userLocation = e.latlng;

            // Kullanıcıyı haritada ufak mavi bir nokta olarak göster
            if (userMarker) {
                userMarker.setLatLng(userLocation);
            } else {
                userMarker = L.circleMarker(userLocation, {
                    color: '#4f46e5', fillColor: '#4f46e5', fillOpacity: 1, radius: 6, weight: 2, opacity: 0.5
                }).addTo(map);
            }
        });

        // ----------------------------------------------

        // --- KIRMIZI KAPSÜL İKONU TASARIMI ---
        var redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        // --- VERİTABANINDAN GELEN KAPSÜLLERİ ÇİZME ---
        var savedCapsules = @json($capsules ?? []);

        if(savedCapsules.length > 0) {
            savedCapsules.forEach(function(capsule) {
                var capsuleLatLng = L.latLng(capsule.latitude, capsule.longitude);
                var savedMarker = L.marker(capsuleLatLng, {icon: redIcon}).addTo(map);

                // Kapsüle tıklandığında Mesafe Kontrolü Yap
                savedMarker.on('click', function(e) {

                    // Eğer kullanıcı konum izni vermediyse
                    if (!userLocation) {
                        savedMarker.bindPopup(`
                            <div class="text-center p-2 min-w-[150px]">
                                <div class="text-3xl mb-2">📡</div>
                                <p class="text-xs font-bold text-slate-800">Kapsülü açmak için tarayıcıdan konum izni vermelisiniz!</p>
                            </div>
                        `).openPopup();
                        return;
                    }

                    // Leaflet'in sihirli formülü: İki nokta arası mesafeyi (metre) hesapla
                    var distance = userLocation.distanceTo(capsuleLatLng);

                    // MESAFE KİLİDİ ALGORİTMASI (50 Metre)
                    if (distance <= 50) {
                        // Kilit Açıldı (50 metreden yakın)
                        savedMarker.bindPopup(`
                            <div class="text-center p-2 min-w-[150px]">
                                <div class="text-3xl mb-2 drop-shadow-md">🔓</div>
                                <p class="text-sm font-bold text-slate-800 italic">"${capsule.message}"</p>
                                <div class="text-[10px] text-emerald-600 mt-2 font-black uppercase tracking-widest border-t border-slate-100 pt-2">
                                    Kapsül Açıldı!
                                </div>
                            </div>
                        `).openPopup();
                    } else {
                        // Kilitli (Çok uzak)
                        var distanceInt = Math.round(distance);
                        savedMarker.bindPopup(`
                            <div class="text-center p-2 min-w-[150px]">
                                <div class="text-3xl mb-2 drop-shadow-md opacity-60">🔒</div>
                                <p class="text-xs font-bold text-slate-800">Bu kapsül için çok uzaksın.</p>
                                <div class="text-[10px] text-rose-500 mt-2 font-black uppercase tracking-widest border-t border-slate-100 pt-2">
                                    ${distanceInt} metre yaklaşmalısın
                                </div>
                            </div>
                        `).openPopup();
                    }
                });
            });
        }
        // ----------------------------------------------

        // --- YENİ KAPSÜL GÖMME ÖZELLİĞİ ---
        var tempMarker;

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            if (tempMarker) map.removeLayer(tempMarker);

            tempMarker = L.marker([lat, lng]).addTo(map);
            map.panTo([lat, lng]);

            var formHtml = `
                <div class="p-1 text-center w-48">
                    <div class="text-3xl mb-1">💎</div>
                    <h3 class="font-bold text-slate-800 text-sm mb-2">Yeni Kapsül Bırak</h3>

                    @auth
                        <form action="/kapsul-kaydet" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" value="${lat}">
                            <input type="hidden" name="longitude" value="${lng}">
                            <textarea name="message" rows="3" class="w-full text-xs p-2 border border-slate-200 rounded-xl mb-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none transition-colors" placeholder="Buraya gizli bir anı, not veya ipucu bırak..." required></textarea>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2.5 rounded-xl transition-colors shadow-md">Kapsülü Göm 📍</button>
                        </form>
                    @else
                        <p class="text-xs text-slate-500 mb-3 leading-relaxed">Kapsül bırakmak için önce giriş yapmalısın.</p>
                        <a href="{{ route('login') }}" class="block w-full bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold py-2.5 rounded-xl transition-colors shadow-md text-center" style="text-decoration: none;">Giriş Yap</a>
                    @endauth
                </div>
            `;

            tempMarker.bindPopup(formHtml, { closeButton: false, minWidth: 200 }).openPopup();
        });
    </script>
</body>
</html>

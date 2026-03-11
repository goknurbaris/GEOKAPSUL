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
        body { margin: 0; padding: 0; }
        .leaflet-popup-content { margin: 14px; }
    </style>
</head>
<body class="antialiased">

    <div class="absolute top-4 right-4 z-50 flex gap-2">
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

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([38.626, 34.714], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 20
        }).addTo(map);

        var userLocation = null;
        map.locate({setView: true, maxZoom: 15});

        map.on('locationfound', function(e) {
            userLocation = e.latlng;
            L.circleMarker(e.latlng, { radius: 8, fillColor: "#4f46e5", color: "#ffffff", weight: 2, opacity: 1, fillOpacity: 0.8 }).addTo(map).bindPopup("<b class='text-indigo-600'>Buradasın! 📍</b>");
        });

        var popup = L.popup();

        map.on('click', function(e) {
            @auth
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                // TERTEMİZ FORMU BURAYA EKLEDİK (Sadece Galeriden Seç)
                const formHtml = `
                    <form action="/kapsul-kaydet" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3 min-w-[240px]">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="latitude" value="${lat}">
                        <input type="hidden" name="longitude" value="${lng}">

                        <label class="font-black text-slate-800 text-xs uppercase tracking-widest">Gizli Mesajın</label>
                        <textarea name="message" required rows="3" class="border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 bg-slate-50 shadow-inner" placeholder="O anı kelimelere dök..."></textarea>

                        <label class="font-black text-slate-800 text-xs uppercase tracking-widest mt-1">Görsel Ekle (Opsiyonel)</label>

                        <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-xl bg-white shadow-sm transition-all">

                        <button type="submit" class="bg-indigo-600 text-white font-black py-3 px-4 rounded-xl hover:bg-indigo-700 mt-2 shadow-lg shadow-indigo-500/30 uppercase tracking-widest transition-transform active:scale-95">
                            Kapsülü Göm 💎
                        </button>
                    </form>
                `;
                popup.setLatLng(e.latlng).setContent(formHtml).openOn(map);
            @else
                popup.setLatLng(e.latlng).setContent("<div class='text-center font-bold p-3'>Kapsül gömmek için <br><a href='/login' class='inline-block mt-2 bg-indigo-600 text-white px-4 py-1.5 rounded-lg'>Giriş Yapmalısın 🔒</a></div>").openOn(map);
            @endauth
        });

        // Veritabanındaki Kapsülleri Göster
        var capsules = @json($capsules ?? []);
        capsules.forEach(function(capsule) {
            var marker = L.marker([capsule.latitude, capsule.longitude]).addTo(map);
            marker.on('click', function() {
                if (!userLocation) {
                    marker.bindPopup("<div class='text-center p-3'><span class='text-3xl block mb-2'>📡</span><b>Konum izni vermelisin!</b></div>").openPopup();
                    return;
                }
                var distance = userLocation.distanceTo([capsule.latitude, capsule.longitude]);
                if (distance <= 50) {
                    let contentHtml = `<div class="text-center min-w-[220px]">`;
                    contentHtml += `<h3 class="text-emerald-500 font-black text-lg mb-3 uppercase tracking-widest">Kapsül Açıldı 🔓</h3>`;
                    if (capsule.image) {
                        contentHtml += `<img src="/storage/${capsule.image}" class="w-full h-40 object-cover rounded-2xl mb-4 shadow-md border-2 border-slate-100">`;
                    }
                    contentHtml += `<p class="text-slate-800 font-bold italic bg-slate-50 p-4 rounded-2xl border border-slate-200">"${capsule.message}"</p></div>`;
                    marker.bindPopup(contentHtml).openPopup();
                } else {
                    marker.bindPopup(`<div class="text-center p-3 min-w-[200px]"><div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">🔒</div><h3 class="text-rose-600 font-black text-lg mb-1 uppercase tracking-widest">Çok Uzaksın!</h3><p class="text-slate-600 font-bold text-sm">Kilidi kırmak için <br><b class="text-rose-500 text-lg">${Math.round(distance)}m</b><br> daha yaklaşmalısın.</p></div>`).openPopup();
                }
            });
        });
    </script>
</body>
</html>

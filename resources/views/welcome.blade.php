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

    <div id="webcamModal" class="fixed inset-0 bg-black/90 z-[9999] hidden flex-col items-center justify-center backdrop-blur-sm">
        <div class="relative bg-slate-900 p-4 rounded-3xl border-2 border-indigo-500 shadow-[0_0_50px_rgba(79,70,229,0.5)]">
            <h3 class="text-white text-center font-black mb-4 uppercase tracking-widest text-sm">Canlı Kamera 🔴</h3>

            <video id="webcamVideo" autoplay playsinline muted class="w-full max-w-lg h-auto bg-black rounded-2xl mb-4 transform scale-x-[-1]"></video>

            <div class="flex gap-4 justify-center">
                <button type="button" onclick="captureWebcam()" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-black text-lg hover:bg-indigo-500 shadow-lg transition-transform active:scale-95 flex items-center gap-2 uppercase tracking-widest">
                    📸 Çek
                </button>
                <button type="button" onclick="stopWebcam()" class="bg-rose-600 text-white px-8 py-3 rounded-xl font-black text-lg hover:bg-rose-500 shadow-lg transition-transform active:scale-95 uppercase tracking-widest">
                    İptal
                </button>
            </div>
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

                const formHtml = `
                    <form action="/kapsul-kaydet" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3 min-w-[240px]">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="latitude" value="${lat}">
                        <input type="hidden" name="longitude" value="${lng}">

                        <label class="font-black text-slate-800 text-xs uppercase tracking-widest">Gizli Mesajın</label>
                        <textarea name="message" required rows="3" class="border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 bg-slate-50" placeholder="O anı kelimelere dök..."></textarea>

                        <label class="font-black text-slate-800 text-xs uppercase tracking-widest mt-1">Görsel Ekle</label>

                        <div class="flex gap-2">
                            <input type="file" id="cameraInput" class="hidden">
                            <input type="file" id="galleryInput" accept="image/*" class="hidden" onchange="document.getElementById('cameraInput').name=''; this.name='image'; document.getElementById('fileStatus').innerHTML = '✅ Galeriden Seçildi!';">

                            <button type="button" onclick="startWebcam()" class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 py-2.5 rounded-xl text-xs font-black transition-all shadow-sm uppercase">
                                📸 KAMERA
                            </button>

                            <button type="button" onclick="document.getElementById('galleryInput').click();" class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 py-2.5 rounded-xl text-xs font-black transition-all shadow-sm uppercase">
                                🖼️ GALERİ
                            </button>
                        </div>
                        <p id="fileStatus" class="text-[10px] text-emerald-600 font-black text-center mt-[-4px] h-3 uppercase tracking-widest"></p>

                        <button type="submit" class="bg-indigo-600 text-white font-black py-3 px-4 rounded-xl hover:bg-indigo-700 mt-2 shadow-lg shadow-indigo-500/30 uppercase tracking-widest">
                            Kapsülü Göm 💎
                        </button>
                    </form>
                `;
                popup.setLatLng(e.latlng).setContent(formHtml).openOn(map);
            @else
                popup.setLatLng(e.latlng).setContent("<div class='text-center font-bold p-3'>Kapsül gömmek için <br><a href='/login' class='inline-block mt-2 bg-indigo-600 text-white px-4 py-1.5 rounded-lg'>Giriş Yapmalısın 🔒</a></div>").openOn(map);
            @endauth
        });

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

        // 🔴 KAMERA (WEBRTC) SİSTEMİ 🔴
        let videoStream = null;

        function startWebcam() {
            // Güvenlik Kontrolü: Tarayıcılar kamerayı sadece HTTPS üzerinden açar!
            if (!window.isSecureContext) {
                alert("Kameranın açılabilmesi için sitenin 'HTTPS' güvenli bağlantısına sahip olması zorunludur! Lütfen adres çubuğuna https://geokapsul.test yazarak siteye tekrar girin.");
                return;
            }

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert("Tarayıcınız kamera kullanımını desteklemiyor.");
                return;
            }

            const video = document.getElementById('webcamVideo');
            const modal = document.getElementById('webcamModal');

            // Bilgisayarın kamerasını açma isteği
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    videoStream = stream;
                    video.srcObject = stream;
                    video.play();
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                })
                .catch(function(err) {
                    alert("Kamera izni reddedildi veya başka bir uygulama (Zoom, Discord vb.) kameranızı şu an kullanıyor! Lütfen izinleri kontrol edin.");
                    console.error("Kamera Hatası:", err);
                });
        }

        function stopWebcam() {
            const modal = document.getElementById('webcamModal');
            if(videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
            }
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function captureWebcam() {
            const video = document.getElementById('webcamVideo');

            if(video.videoWidth === 0) {
                alert("Kamera görüntüsü henüz hazır değil, bir saniye bekle!");
                return;
            }

            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');

            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            const arr = dataUrl.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while(n--){ u8arr[n] = bstr.charCodeAt(n); }
            const file = new File([u8arr], "webcam_foto.jpg", {type: mime});

            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('galleryInput').name = '';
            const camInput = document.getElementById('cameraInput');
            camInput.name = 'image';
            camInput.files = dt.files;

            document.getElementById('fileStatus').innerHTML = '✅ Fotoğraf Çekildi!';
            stopWebcam();
        }
    </script>
</body>
</html>

const CACHE_NAME = 'geokapsul-v1';
const OFFLINE_URL = '/offline.html';

// Önbelleğe alınacak kaynaklar
const PRECACHE_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
];

// Service Worker kurulumu
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Önbellek açıldı');
                return cache.addAll(PRECACHE_ASSETS);
            })
            .then(() => self.skipWaiting())
    );
});

// Eski cache temizliği
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Eski cache siliniyor:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch stratejisi: Network First, Cache Fallback
self.addEventListener('fetch', (event) => {
    // POST isteklerini atla
    if (event.request.method !== 'GET') return;

    // API istekleri için network-only
    if (event.request.url.includes('/kapsul/') ||
        event.request.url.includes('/api/')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Geçerli yanıtı önbelleğe al
                if (response.status === 200) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(async () => {
                // Önbellekten dene
                const cachedResponse = await caches.match(event.request);
                if (cachedResponse) {
                    return cachedResponse;
                }

                // HTML istekleri için offline sayfası
                if (event.request.headers.get('accept').includes('text/html')) {
                    return caches.match(OFFLINE_URL);
                }
            })
    );
});

// Push notification
self.addEventListener('push', (event) => {
    if (!event.data) return;

    const data = event.data.json();
    const options = {
        body: data.body || 'Yakınında bir kapsül var!',
        icon: '/images/icon-192.png',
        badge: '/images/icon-72.png',
        vibrate: [100, 50, 100],
        data: {
            url: data.url || '/'
        },
        actions: [
            { action: 'open', title: 'Aç' },
            { action: 'close', title: 'Kapat' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'GeoKapsül', options)
    );
});

// Notification tıklama
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'close') return;

    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/')
    );
});

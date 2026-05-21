/**
 * sw.js — Production-ready Service Worker
 * Network-first stratejisi + offline fallback.
 * Subfolder deploy uyumlu (scope-based path).
 */

var CACHE_NAME = 'psk-dk-v2';

// Install: offline sayfasını cache'le (scope-relative)
self.addEventListener('install', function (event) {
    var scope = self.registration.scope; // e.g. https://example.com/ or https://example.com/subfolder/
    var offlineUrl = scope + 'offline';

    event.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.addAll([offlineUrl]);
        })
    );
    self.skipWaiting();
});

// Activate: eski cache'leri temizle
self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (names) {
            return Promise.all(
                names
                    .filter(function (n) { return n !== CACHE_NAME; })
                    .map(function (n) { return caches.delete(n); })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

// Fetch: navigate isteklerinde network-first, başarısızsa offline page
self.addEventListener('fetch', function (event) {
    if (event.request.mode !== 'navigate') {
        return;
    }

    event.respondWith(
        fetch(event.request).catch(function () {
            var scope = self.registration.scope;
            return caches.match(scope + 'offline');
        })
    );
});

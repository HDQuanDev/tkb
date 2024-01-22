// Đặt tên cho bộ nhớ cache
var CACHE_NAME = 'my-site-cache-v1';
// Thêm vào danh sách các tài nguyên cần được lưu trong bộ nhớ cache
var urlsToCache = [
    '/',
    '/index.php',
    '/assets/css/general.css',
    '/assets/css/contentbox.css',
    '/assets/css/bootstrap-grid.min.css',
    '/assets/js/main.js?v=1',
    '/assets/img/TKB.QDEVS.TECH.png',
];

// Sự kiện install - lưu các tài nguyên vào bộ nhớ cache
self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
        .then(function (cache) {
            console.log('Opened cache');
            return cache.addAll(urlsToCache);
        })
    );
});

// Sự kiện fetch - phục vụ các yêu cầu từ bộ nhớ cache
self.addEventListener('fetch', function (event) {
    event.respondWith(
        caches.match(event.request)
        .then(function (response) {
            // Cache hit - return response
            if (response) {
                return response;
            }
            return fetch(event.request);
        })
    );
});
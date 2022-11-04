const CACHE_VERSION = 2.12;
const file_ext = [".jpg",".png",".svg",".bmp",".ttf",".eot",".woff",".woff2","css",".js",".map"];
let Cache = 'static-cache-v' + CACHE_VERSION;
let strategy = "cache-first";
const filesToCache = [
    `/sw.js?v=${CACHE_VERSION}`,
    `/css/app.css?v=${CACHE_VERSION}`,
    `/js/app.js?v=${CACHE_VERSION}`
];
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(Cache)
            .then((cache) => {
                return cache.addAll(filesToCache);
            })

    )
});

self.addEventListener('activate', event => {
    this.skipWaiting();
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("static-cache-v") || cacheName.startsWith("pwa-v")))
                    .filter(cacheName => (cacheName !== Cache))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

self.addEventListener('fetch', (event) => {
    if (file_ext.some(v => event.request.url.toLowerCase().includes(v)) && strategy === "cache-first") {
        event.respondWith(caches.open(Cache).then((cache) => {
            event.request.url = event.request.url + `?v=${CACHE_VERSION}`;
            return cache.match(event.request.url).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request).then((fetchedResponse) => {
                    cache.put(event.request , fetchedResponse.clone());
                    return fetchedResponse;
                }).catch(err => {return caches.match('offline');});
            });
        }))
    }
    else {
        event.respondWith(
            fetch(event.request).then((fetchedResponse) => {
                return fetchedResponse;
            }).catch(err => {return caches.match('offline');})
        )
    }
});

self.addEventListener("notificationclick",(event) => {
    event.waitUntil(() => {self.clients.openWindow(event.notification.data.action_route);});
});

self.addEventListener('push', (event) => {
    let msg = event.data.json();
    const channel = new BroadcastChannel('NewMessage');
    channel.postMessage({'type':msg.data.type,'message':msg.body,'route': msg.data.action_route});
    event.waitUntil(self.registration.showNotification(msg.title, {
        body: msg.body,
        icon: msg.icon,
        badge: msg.badge,
        data: msg.data,
        tag: new Date().getTime() + (Math.random() * 100),
        //renotify: true
    }));
})

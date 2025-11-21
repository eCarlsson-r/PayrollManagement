"use strict";

const CACHE_NAME = "offline-cache-v1";
const OFFLINE_URL = '/offline.html';

const filesToCache = [
    OFFLINE_URL
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(filesToCache))
    );
});

self.addEventListener("fetch", (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    return caches.match(OFFLINE_URL);
                })
        );
    } else {
        event.respondWith(
            caches.match(event.request)
                .then((response) => {
                    return response || fetch(event.request);
                })
        );
    }
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        //notifications aren't supported or permission not granted!
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        if (msg.type == "App\\Notifications\\FeedbackSent") {
            new Audio('/audio/feedback-bell.mp3').play();
        } else if (msg.type == "App\\Notifications\\DocumentUpload") {
            new Audio('/audio/document-bell.mp3').play();
        }
        
        e.waitUntil(self.registration.showNotification(msg.title, {
            body: msg.body,
            icon: msg.icon || '/logo.png',
            actions: msg.actions,
            badge: msg.icon || '/logo.png'
        }));
    }
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close(); // Close the notification

    var url = (event.notification.actions.length > 0) ? event.notification.actions[0].action : '/';

    // This looks to see if the current is already open and focuses if it is
    event.waitUntil(
        clients.matchAll({
            type: "window",
        }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.startsWith(self.location.origin) && "focus" in client) {
                    client.focus().then((windowClient) => {
                        if (windowClient) {
                            return windowClient.navigate(url);
                        }
                    });
                }
            }
            if (clients.openWindow) return clients.openWindow(url);
        }),
    );
});
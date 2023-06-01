var CACHE_NAME = 'my-pwa';
var urlsToCache = [
    '/logo_abeja_blanco.svg',
	'/Seekitup_Isologo.svg',
	'/logo_blanco.svg',
	'/linkedin.html',
	'/embed.html',
	'/Trama%20-%20Light.svg',
	'/favicon.ico',
	'/logo_abeja.svg',
	'/logo.svg',
	'/fondo.svg',
	'/Trama%20-%20Dark.svg',
	'/pwa/style.css',
	'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&display=swap',
	'https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css'
];

self.addEventListener('install', (event) => {
  console.log('👷', 'install', event);
  // Perform install steps
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function (cache) {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('activate', (event) => {
  console.log('👷', 'activate', event);
  return self.clients.claim();
});

self.addEventListener('fetch', function(event) {
  console.log('👷', 'fetch', event);
  event.respondWith(
        caches.match(event.request)
            .then(function (response) {
                // Cache hit - return response
                if (response) {
                    return response;
                }
                return fetch(event.request);
            }
            )
    );
});
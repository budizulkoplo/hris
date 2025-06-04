self.addEventListener('install', function (e) {
  console.log('Service Worker: Installed');
});

self.addEventListener('fetch', function (event) {
  // Bisa ditambahkan cache logic jika mau offline mode
});

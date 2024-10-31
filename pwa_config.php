<?php
class lum_pwa_config{
    static function pwa_installed_hook($key = false){
        if (!!!$key){
            file_put_contents("file", "");
        }
        else {
            file_put_contents($key, "");
        }
    }

    static $pwa_unique_get_key = 'lumetas_pwa_unique_key';
    static $install_pwa_function_name = 'install_pwa';

    static $pwa_service_worker_code = <<<swcode
let contentToCache = JSON.parse('%files_json%');

self.addEventListener("install", (e) => {
    console.log("[Service Worker] Install");
    e.waitUntil(
      (async () => {
        const cache = await caches.open(`%cache_version%`);
        console.log("[Service Worker] Caching all: app shell and content");
        await cache.addAll(contentToCache);
      })(),
    );
});

swcode;

    static $pwa_script_file = <<<script
if ("serviceWorker" in navigator) {
  navigator.serviceWorker
    .register(`%url%`)
    .then((registration) => {
      registration.addEventListener("updatefound", () => {
      
    
        const installingWorker = registration.installing;
        console.log(
          "A new service worker is being installed:",
          installingWorker,
        );

       
        
      });
    })
    .catch((error) => {
      console.error(`Service worker registration failed:` + error);
    });
} else {
  console.error("Service workers are not supported.");
}


let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
});

function %install_function%() {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted PWA installation');
            } else {
                console.log('User dismissed PWA installation');
            }
            deferredPrompt = null;
        });
    } else {
        console.log('PWA install prompt is not available');
    }
}

window.addEventListener('appinstalled', async () => {
    fetch(`%hook%`);
});
script;
}
<?php
$arr = explode('
---
', file_get_contents(__DIR__.'/config'));
if (isset($_GET['manifest'])){
header('Content-Type: application/json; charset=utf-8');
echo $arr[0];
    }
if (isset($_GET['sw'])){
header('Content-Type: application/javascript; charset=utf-8');
$o = json_decode(file_get_contents(__DIR__.'/config'));
$files = $arr[1];
echo <<<OUT
self.addEventListener("install", function(event) {
	event.waitUntil(
		caches.open("pwa").then(function(cache) {
			return cache.addAll($files);
		})
	);
});

self.addEventListener("fetch", function(event) {
	event.respondWith(
		caches.open("pwa").then(function(cache) {
			return cache.match(event.request).then(function(response) {
				cache.addAll([event.request.url]);

				if(response) {
					return response;
				}

				return fetch(event.request);
			});
		})
	);
});
    

OUT;


}
if (isset($_GET['js'])){
header('Content-Type: application/javascript; charset=utf-8');


echo <<<OUT
    window.addEventListener("load", () => {


	window.addEventListener("beforeinstallprompt", (e) => {
		e.preventDefault();
		console.log("Ready to install...");
		installEvent = e;
		document.getElementById("install").style.display = "initial";
		cacheLinks();
		
	});

	
		navigator.serviceWorker.register("/pwa/pwalib.php?sw")
		.then(registration => {
			console.log("Service Worker is registered", registration);
			enableButton.parentNode.remove();
		})
		.catch(err => {
			console.error("Registration failed:", err);
		});



	
	});

OUT;
}





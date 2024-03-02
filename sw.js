const addResourcesToCache = async (resources) => {
  const cache = await caches.open("v1");
  await cache.addAll(resources);
};

self.addEventListener('install', (event) => {
  event.waitUntil(
    addResourcesToCache([
      '/'
    ])
  );
});

self.addEventListener('fetch', function(event) {
    if(event.request.url.startsWith('chrome-extension')){
        //skip request
    }
    else {
        event.respondWith(async function() {
            try{
                var res = await fetch(event.request);
                var cache = await caches.open('cache');
                cache.put(event.request.url, res.clone());
                return res;
            }
            catch(error){
                return caches.match(event.request);
            }
        }());   
    }
});
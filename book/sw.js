let cacheData = "appV1";
this.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(cacheData).then((cache) => {
            cache.addAll([
                '/static/js/main.chunk.js',
                '/static/js/0.chunk.js',
                '/static/js/bundle.js',
                '/static/js/bundle.worker.js',
                '/static/css/main.chunk.css',
                '/bootstrap.min.css',
                '/manifest.json',
                'https://cdn.jsdelivr.net/pyodide/v0.21.3/full/pyodide.js',
                '/static/media/fontawesome-webfont.1e59d2330b4c6deb84b3.ttf',
                '/logo192.png',
                'https://cdn.jsdelivr.net/pyodide/v0.21.3/full/pyodide_py.tar',
                'https://cdn.jsdelivr.net/pyodide/v0.21.3/full/pyodide.asm.js',
                'https://cdn.jsdelivr.net/pyodide/v0.21.3/full/pyodide.asm.wasm',
                'https://cdn.jsdelivr.net/pyodide/v0.21.3/full/repodata.json',
                'https://cdn.jsdelivr.net/pyodide/v0.21.3/full/pyodide.asm.data',
                'https://cdn.jsdelivr.net/pyodide/v0.21.3/full/distutils.tar',
                'https://cdn.jsdelivr.net/pyodide/v0.17.0/full/pyodide.js',
                'https://cdn.jsdelivr.net/pyodide/v0.17.0/full/pyodide.asm.js',
                'https://cdn.jsdelivr.net/pyodide/v0.17.0/full/pyodide.asm.data',
                'https://cdn.jsdelivr.net/pyodide/v0.17.0/full/packages.json',
                'https://cdn.jsdelivr.net/pyodide/v0.17.0/full/numpy.js',
                'https://cdn.jsdelivr.net/pyodide/v0.17.0/full/pyodide.asm.wasm',
                'https://cdn.jsdelivr.net/pyodide/v0.17.0/full/distutils.tar',
                'https://cdn.jsdelivr.net/gh/qubits-platform/sqlite-wasm@master/sqlite3.js',
                '/index.js',
                '/',
                "/chapter1",
                "/chapter2",
                "/chapter3",
                '/ws',
                '/jswasm/sqlite3.js',
                '/jswasm/sqlite3.wasm',
                '/favicon.png'
            ])
        })
    )
})
this.addEventListener("fetch", (event) => {


    // console.warn("url",event.request.url)


    if (!navigator.onLine) {
        if (event.request.url === "http://localhost:3000/static/js/main.chunk.js") {
            event.waitUntil(
                this.registration.showNotification("Internet", {
                    body: "internet not working",
                })
            )
        }
        event.respondWith(
            caches.match(event.request).then((resp) => {
                if (resp) {
                    return resp
                }
                let requestUrl = event.request.clone();
                fetch(requestUrl)
            })
        )
    }
}) 
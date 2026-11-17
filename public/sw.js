/*
 * 歩数ランキングの Service Worker（D-036。registerApp と同じくホーム画面から単独のアプリとして起動するため）。
 *
 * - 画面（HTML）はキャッシュしない。ログイン中の画面・CSRF トークン入りの HTML を端末に残さないため。
 *   通信できないときだけ offline.html を返す
 * - build/assets/ のファイル名はビルドごとにハッシュが変わる（中身が変われば別ファイル）ので、一度取ったら端末の控えを使う
 * - アイコン・manifest は通信優先（差し替えを反映するため）
 * - POST などの GET 以外、/api/、他のオリジンには関わらない
 *
 * 置き場所は public/ 直下。scope はこのファイルのあるフォルダ（本番 /steps/、ローカル /）になる。
 * キャッシュの中身の扱いを変えたら VERSION を上げる（古いキャッシュは activate で消える）。
 */
const VERSION = 'v1';
const ASSET_CACHE = `steps-assets-${VERSION}`;
const SHELL_CACHE = `steps-shell-${VERSION}`;

const scopePath = new URL(self.registration.scope).pathname; // 例：'/steps/' または '/'
const OFFLINE_URL = `${scopePath}offline.html`;

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(SHELL_CACHE)
            .then((cache) => cache.addAll([OFFLINE_URL]))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key.startsWith('steps-') && key !== ASSET_CACHE && key !== SHELL_CACHE)
                    .map((key) => caches.delete(key)),
            ))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin || !url.pathname.startsWith(scopePath)) return;

    const path = url.pathname.slice(scopePath.length); // scope からの相対パス
    if (path.startsWith('api/')) return;

    // 画面の移動：通信のみ。失敗したときだけオフラインの案内
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL).then((res) => res || Response.error())),
        );
        return;
    }

    // ハッシュ付きのビルド成果物：控えがあればそれを使い、無ければ取って控える
    if (path.startsWith('build/assets/')) {
        event.respondWith(
            caches.open(ASSET_CACHE).then((cache) => cache.match(request).then((cached) => {
                if (cached) return cached;
                return fetch(request).then((res) => {
                    if (res.ok) cache.put(request, res.clone());
                    return res;
                });
            })),
        );
        return;
    }

    // アイコン：通信優先、通信できなければ控え
    if (path.startsWith('icons/')) {
        event.respondWith(
            fetch(request)
                .then((res) => {
                    if (res.ok) {
                        const copy = res.clone();
                        caches.open(SHELL_CACHE).then((cache) => cache.put(request, copy));
                    }
                    return res;
                })
                .catch(() => caches.match(request).then((res) => res || Response.error())),
        );
    }
});

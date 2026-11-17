<?php

return [
    'headers' => [
        'X-Frame-Options' => 'DENY',
        // D-033：自ドメインのみ。'unsafe-eval' は Alpine.js 標準版が x-data 等の式評価に使う。
        // 'unsafe-inline'（style のみ）は既存の style 属性（dropdown・modal・ランキングの棒グラフ等）のため。
        // 外部のフォント・画像・スクリプトを足すときは該当ディレクティブにオリジンを足す（docs/deploy-notes.md）
        'Content-Security-Policy' => implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data:",
            "font-src 'self'",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]),
        'X-Content-Type-Options' => 'nosniff',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        // D-034：検索エンジンの登録・リンク追跡の対象外（HTML 以外の応答にも付ける）
        'X-Robots-Tag' => 'noindex, nofollow',
    ],

    // 0 で無効。includeSubDomains / preload は付けない
    'hsts_max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 86400),
];

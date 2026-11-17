{{-- D-036 ホーム画面に追加して単独のアプリとして起動する（PWA）。layouts/app・layouts/guest の head で読み込む。
     URL は url() で組む（サブフォルダ公開 /steps/ でも正しいパスになる）。Service Worker の登録は resources/js/app.js --}}
<meta name="theme-color" content="#2f8f5b">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="歩数">
<meta name="format-detection" content="telephone=no">
<meta name="sw-url" content="{{ url('sw.js') }}">
<meta name="sw-scope" content="{{ url('/') }}/">
<link rel="manifest" href="{{ url('manifest.webmanifest') }}">
<link rel="icon" href="{{ url('favicon.ico') }}" sizes="32x32">
<link rel="icon" type="image/png" sizes="192x192" href="{{ url('icons/icon-192.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ url('icons/apple-touch-icon.png') }}">

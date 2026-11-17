<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        @include('layouts.pwa-head')

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-app font-sans text-ink antialiased">
        <div class="mx-auto min-h-screen max-w-[440px] px-4">
            <div class="mb-2 mt-10 text-center text-6xl">👟</div>
            <h1 class="m-0 text-center text-xl font-bold">{{ config('app.name') }}</h1>
            <p class="mt-1 text-center text-sm text-sub">iPhoneの歩数で、仲間と競おう</p>

            <div class="mt-6 rounded-2xl border border-line bg-card p-4">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

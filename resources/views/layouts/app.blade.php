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
        <div class="relative mx-auto min-h-screen max-w-[440px] pb-24">
            @include('layouts.navigation')

            <main class="px-4">
                @if (session('status'))
                    <div class="mb-3 rounded-xl bg-accent-weak p-3 text-sm text-accent">{{ session('status') }}</div>
                @endif

                {{ $slot }}
            </main>

            @include('layouts.tabbar')
        </div>
    </body>
</html>

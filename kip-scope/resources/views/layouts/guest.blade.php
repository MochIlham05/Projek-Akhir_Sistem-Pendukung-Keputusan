<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KIP-SCOPE') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS CDN Fallback -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Scripts & Styles from Vite -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-800">
        <div class="min-h-screen flex flex-col justify-center items-center py-8 px-4 bg-gradient-to-b from-blue-950 via-blue-900 to-slate-900">
            <div class="w-full sm:max-w-md bg-white shadow-2xl rounded-2xl p-8 border-t-4 border-yellow-400">
                {{ $slot }}
            </div>

            <div class="mt-6 text-center text-xs text-blue-200">
                KIP-SCOPE &bull; Politeknik Elektronika Negeri Surabaya (PENS)
            </div>
        </div>
    </body>
</html>

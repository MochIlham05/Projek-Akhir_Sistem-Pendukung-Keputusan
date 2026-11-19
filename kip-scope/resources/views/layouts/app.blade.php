<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KIP-SCOPE') }} - SPK Evaluasi Kelayakan Beasiswa KIP-K</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS CDN Fallback & Config -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                blue: '#1e40af',       // Royal Blue
                                navy: '#0f172a',       // Deep Navy
                                sky: '#0284c7',        // Sky Blue
                                yellow: '#eab308',     // Gold / Yellow
                                amber: '#d97706',      // Warm Amber
                                lightyellow: '#fef9c3', // Pale Yellow
                            }
                        }
                    }
                }
            }
        </script>

        <!-- Scripts & Styles from Vite -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-screen flex flex-col">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white border-b border-slate-200 shadow-sm">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-2">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <span class="font-bold text-blue-950">KIP-SCOPE</span>
                    <span class="text-slate-400">|</span>
                    <span>Sistem Pendukung Keputusan AHP-MOORA</span>
                </div>
                <div>
                    Politeknik Elektronika Negeri Surabaya (PENS) &copy; {{ date('Y') }}
                </div>
            </div>
        </footer>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name= "csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Al Akhawayn University') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
        {{-- <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            university: {
                                primary: '#0F5832',
                                secondary: '#0D4A2A',
                                accent: '#C8A24A',
                                light: '#F4F9F6',
                            },
                        },
                    },
                },
            }
        </script> --}}
    </head>
    <body class="font-sans text-gray-900 antialiased bg-university-light/60">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-6 text-center">
                <a href="/" class="flex flex-col items-center group">
                    <img src="{{ asset('images/logo-university.webp') }}" alt="Al Akhawayn University" class="h-24 w-auto mb-3 transition-transform group-hover:scale-105 duration-300">
                    <span class="text-university-primary font-extrabold text-2xl tracking-wide uppercase">Al Akhawayn University</span>
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-widest mt-0.5">Secure Credentials Portal</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 bg-white rounded-xl shadow-md border border-gray-100">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
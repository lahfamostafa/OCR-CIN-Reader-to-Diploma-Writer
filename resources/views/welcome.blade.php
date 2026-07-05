<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Al Akhawayn University - Système de Diplômes</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans antialiased bg-gray-50">

        <div class="min-h-screen">
            <!-- Navigation -->
            <nav class="bg-white shadow-md border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <a href="/" class="flex items-center space-x-3">
                                <img src="{{ asset('images/logo-university.webp') }}" alt="Al Akhawayn University" class="h-12 w-auto">
                                <span class="text-university-primary font-bold text-xl hidden sm:block">Al Akhawayn University</span>
                            </a>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-university-primary text-white rounded-lg hover:bg-university-secondary transition-colors duration-200 font-medium">Tableau de bord</a>
                                @else
                                    <a href="{{ route('login') }}" class="px-4 py-2 bg-university-primary text-white rounded-lg hover:bg-university-secondary transition-colors duration-200 font-medium">Connexion</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="px-4 py-2 border-2 border-university-primary text-university-primary rounded-lg hover:bg-university-light transition-colors duration-200 font-medium">Inscription</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <div class="relative overflow-hidden">
                <div class="absolute inset-0">
                    <img src="{{ asset('images/home-university.webp') }}" alt="University Campus" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-university-primary/90 to-university-primary/70"></div>
                </div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
                    <div class="text-white">
                        <h1 class="text-4xl md:text-5xl font-bold mb-6">Système de Gestion des Diplômes</h1>
                        <p class="text-xl md:text-2xl mb-8 opacity-90">Plateforme numérique pour la gestion et la vérification des diplômes universitaires</p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="px-8 py-3 bg-white text-university-primary rounded-lg hover:bg-gray-100 transition-colors duration-200 font-semibold text-center">Accéder au Tableau de Bord</a>
                                @else
                                    <a href="{{ route('login') }}" class="px-8 py-3 bg-white text-university-primary rounded-lg hover:bg-gray-100 transition-colors duration-200 font-semibold text-center">Se Connecter</a>
                                    <a href="{{ route('register') }}" class="px-8 py-3 border-2 border-white text-white rounded-lg hover:bg-white/10 transition-colors duration-200 font-semibold text-center">Créer un Compte</a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <h2 class="text-3xl font-bold text-center text-university-primary mb-12">Fonctionnalités Principales</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
                        <div class="w-16 h-16 bg-university-light rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-8 h-8 stroke-university-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Gestion des Diplômes</h3>
                        <p class="text-gray-600">Créez et gérez facilement les diplômes des lauréats avec une interface intuitive.</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
                        <div class="w-16 h-16 bg-university-light rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-8 h-8 stroke-university-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Gestion des Lauréats</h3>
                        <p class="text-gray-600">Maintenez une base de données complète des étudiants et de leurs informations académiques.</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
                        <div class="w-16 h-16 bg-university-light rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-8 h-8 stroke-university-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Modèles Personnalisables</h3>
                        <p class="text-gray-600">Créez des modèles de diplômes personnalisés selon les besoins de l'université.</p>
                    </div>
                </div>
            </div>

            <!-- Campus Images Section -->
            <div class="bg-university-light py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-3xl font-bold text-center text-university-primary mb-12">Notre Campus</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="relative rounded-xl overflow-hidden shadow-lg">
                            <img src="{{ asset('images/space-university.webp') }}" alt="University Space" class="w-full h-64 object-cover hover:scale-105 transition-transform duration-300">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                                <p class="text-white font-semibold">Espaces d'apprentissage modernes</p>
                            </div>
                        </div>
                        <div class="relative rounded-xl overflow-hidden shadow-lg">
                            <img src="{{ asset('images/space-university2.webp') }}" alt="University Space 2" class="w-full h-64 object-cover hover:scale-105 transition-transform duration-300">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                                <p class="text-white font-semibold">Environnement académique exceptionnel</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-university-primary text-white py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <div class="flex items-center space-x-3 mb-4">
                                <img src="{{ asset('images/logo-university.webp') }}" alt="Al Akhawayn University" class="h-10 w-auto">
                                <span class="font-bold text-lg">Al Akhawayn University</span>
                            </div>
                            <p class="text-white/80">Excellence académique et innovation depuis 1993.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-lg mb-4">Liens Rapides</h4>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-white/80 hover:text-white transition-colors">À propos</a></li>
                                <li><a href="#" class="text-white/80 hover:text-white transition-colors">Programmes</a></li>
                                <li><a href="#" class="text-white/80 hover:text-white transition-colors">Recherche</a></li>
                                <li><a href="#" class="text-white/80 hover:text-white transition-colors">Contact</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-lg mb-4">Contact</h4>
                            <p class="text-white/80">Ifrane, Maroc</p>
                            <p class="text-white/80">info@aui.ma</p>
                            <p class="text-white/80">+212 535 86 24 00</p>
                        </div>
                    </div>
                    <div class="border-t border-white/20 mt-8 pt-8 text-center text-white/80">
                        <p>&copy; {{ date('Y') }} Al Akhawayn University. Tous droits réservés.</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>

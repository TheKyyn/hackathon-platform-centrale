<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plateforme Centrale</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col justify-center items-center px-4">
        <div class="max-w-3xl w-full">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-blue-600 mb-2">Plateforme Centrale</h1>
                <p class="text-xl text-gray-600">Gestion centralisée des leads pour les énergies renouvelables</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Bienvenue sur la plateforme centrale</h2>
                        <p class="text-gray-600">Cette plateforme permet de centraliser et gérer tous les leads provenant de différents sites spécialisés dans les énergies renouvelables.</p>
                    </div>

                    <div class="text-gray-700 mb-8">
                        <h3 class="text-lg font-semibold mb-2">Fonctionnalités principales :</h3>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Centralisation des leads de multiples sites</li>
                            <li>Suivi du statut des leads en temps réel</li>
                            <li>Filtrage avancé par type d'énergie, statut, etc.</li>
                            <li>Statistiques et tableaux de bord</li>
                            <li>API sécurisée pour l'intégration avec des sites externes</li>
                        </ul>
                    </div>

                    <div class="flex justify-center space-x-4">
                    @auth
                            <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm">
                                Accéder au tableau de bord
                        </a>
                    @else
                            <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm">
                                Se connecter
                            </a>
                            <a href="{{ route('register') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-md shadow-sm">
                                S'inscrire
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Plateforme Centrale. Tous droits réservés.
                </div>
        </div>
    </div>
    </body>
</html>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du site') }} : {{ $site->name }}
            </h2>
            <div>
                <a href="{{ route('admin.sites.edit', $site->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                    Éditer
                </a>
                <a href="{{ route('admin.sites.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages de succès -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Informations du site -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Informations du site') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nom</p>
                            <p class="font-medium">{{ $site->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">URL</p>
                            <p class="font-medium">
                                <a href="{{ $site->url }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                    {{ $site->url }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Statut</p>
                            <p class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $site->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $site->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nombre de leads</p>
                            <p class="font-medium">{{ $leadsCount }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Description</p>
                            <p class="font-medium">{{ $site->description ?? 'Aucune description' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Date de création</p>
                            <p class="font-medium">{{ $site->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Token API -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">{{ __('Token API') }}</h3>
                        <form method="POST" action="{{ route('admin.sites.regenerate-token', $site->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Êtes-vous sûr de vouloir régénérer le token API ? L\'ancien token ne fonctionnera plus.');">
                                Régénérer
                            </button>
                        </form>
                    </div>

                    <div class="p-4 bg-gray-100 rounded-md text-sm font-mono break-all">
                        @if ($site->api_token)
                            {{ $site->api_token }}
                        @else
                            <span class="text-gray-500">Aucun token généré. Cliquez sur "Régénérer" pour en créer un.</span>
                        @endif
                    </div>

                    @if ($site->api_token)
                        <div class="mt-4">
                            <h4 class="font-semibold mb-2">Comment utiliser ce token :</h4>
                            <div class="text-sm text-gray-700">
                                <p class="mb-1">1. Ajoutez le token comme Bearer Token dans vos requêtes API :</p>
                                <div class="p-3 bg-gray-100 rounded-md font-mono mb-2">
                                    Authorization: Bearer {{ $site->api_token }}
                                </div>
                                <p class="mb-1">2. Exemple avec cURL :</p>
                                <div class="p-3 bg-gray-100 rounded-md font-mono">
                                    curl -X POST "{{ url('api/v1/leads') }}" \<br>
                                    -H "Authorization: Bearer {{ $site->api_token }}" \<br>
                                    -H "Content-Type: application/json" \<br>
                                    -d '{"original_id": 1, "first_name": "Jean", "last_name": "Dupont", "email": "jean.dupont@example.com", "phone": "0612345678", ...}'
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Derniers leads du site -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">{{ __('Derniers leads') }}</h3>
                        <a href="{{ route('admin.leads.index', ['site_id' => $site->id]) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Voir tous les leads
                        </a>
                    </div>

                    @if($site->leads->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($site->leads as $lead)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $lead->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $lead->full_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $lead->email }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $lead->phone }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $lead->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-500">
                            Aucun lead pour ce site.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@extends('layouts.admin')

@section('title', 'Gestion des Leads')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestion des Leads</h1>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md shadow-sm">
            Retour au tableau de bord
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Filtres</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.leads.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Filtre par site -->
                    <div>
                        <label for="site_id" class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                        <select id="site_id" name="site_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">Tous les sites</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre par type d'énergie -->
                    <div>
                        <label for="energy_type" class="block text-sm font-medium text-gray-700 mb-1">Type d'énergie</label>
                        <select id="energy_type" name="energy_type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">Tous les types</option>
                            @foreach($energyTypes as $type)
                                <option value="{{ $type }}" {{ request('energy_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre par statut -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select id="status" name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">Tous les statuts</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre par statut de vente -->
                    <div>
                        <label for="sale_status" class="block text-sm font-medium text-gray-700 mb-1">Statut de vente</label>
                        <select id="sale_status" name="sale_status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">Tous les statuts de vente</option>
                            @foreach($saleStatuses as $status)
                                <option value="{{ $status }}" {{ request('sale_status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Recherche par mot-clé -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            placeholder="Rechercher par nom, email, téléphone..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <!-- Options de tri -->
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Trier par</label>
                        <div class="flex space-x-2">
                            <select id="sort_by" name="sort_by" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                                <option value="first_name" {{ request('sort_by') == 'first_name' ? 'selected' : '' }}>Prénom</option>
                                <option value="last_name" {{ request('sort_by') == 'last_name' ? 'selected' : '' }}>Nom</option>
                                <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                            </select>
                            <select id="sort_order" name="sort_order" class="w-40 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Croissant</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md shadow-sm">
                        Réinitialiser
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des leads -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Liste des leads</h2>
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-600">
                    {{ $leads->total() }} résultat(s) trouvé(s)
                </div>
                <a href="{{ route('admin.leads.export', request()->query()) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exporter CSV
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type d'énergie</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leads as $lead)
                    <tr>
                        <td class="py-3 px-4 text-sm text-gray-500">{{ $lead->id }}</td>
                        <td class="py-3 px-4 text-sm">
                            <div class="font-medium text-gray-900">{{ $lead->first_name }} {{ $lead->last_name }}</div>
                            @if($lead->property_type)
                                <div class="text-xs text-gray-500">{{ $lead->property_type }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm">
                            <div>{{ $lead->email }}</div>
                            <div>{{ $lead->phone }}</div>
                        </td>
                        <td class="py-3 px-4 text-sm">{{ $lead->energy_type }}</td>
                        <td class="py-3 px-4 text-sm">{{ $lead->site->name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($lead->status == 'nouveau') bg-green-100 text-green-800
                                @elseif($lead->status == 'qualifié') bg-blue-100 text-blue-800
                                @elseif($lead->status == 'converti') bg-purple-100 text-purple-800
                                @elseif($lead->status == 'rejeté') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $lead->status }}
                            </span>
                            @if($lead->sale_status)
                                <div class="mt-1 text-xs">
                                    <span class="px-2 py-1 rounded-full
                                        @if($lead->sale_status == 'vendu') bg-green-100 text-green-800
                                        @elseif($lead->sale_status == 'en_cours') bg-yellow-100 text-yellow-800
                                        @elseif($lead->sale_status == 'perdu') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $lead->sale_status }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-500">{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-sm space-x-2">
                            <a href="{{ route('admin.leads.edit', $lead->id) }}" class="text-blue-600 hover:text-blue-900">Éditer</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-4 px-4 text-sm text-center text-gray-500">Aucun lead trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-gray-200 px-4 py-3">
            {{ $leads->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

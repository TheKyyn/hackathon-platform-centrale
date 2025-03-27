@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Modifier le lead</h1>
        <p class="mt-1 text-sm text-gray-600">
            ID: {{ $lead->original_id }} | Créé le: {{ $lead->created_at->format('d/m/Y H:i') }}
        </p>
    </div>
    <div class="mt-4 md:mt-0 flex space-x-3">
        <a href="{{ route('admin.leads.show', $lead->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Retour aux détails
        </a>
    </div>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 bg-gray-50">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Mise à jour du lead</h3>
        <p class="mt-1 text-sm text-gray-600">
            Modifiez les informations de suivi pour ce lead.
        </p>
    </div>
    <div class="border-t border-gray-200">
        <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Statut -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="new" {{ $lead->status == 'new' ? 'selected' : '' }}>Nouveau</option>
                        <option value="contacted" {{ $lead->status == 'contacted' ? 'selected' : '' }}>Contacté</option>
                        <option value="qualified" {{ $lead->status == 'qualified' ? 'selected' : '' }}>Qualifié</option>
                        <option value="unqualified" {{ $lead->status == 'unqualified' ? 'selected' : '' }}>Non qualifié</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut de vente -->
                <div>
                    <label for="sale_status" class="block text-sm font-medium text-gray-700">Statut de vente</label>
                    <select id="sale_status" name="sale_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Non défini</option>
                        <option value="pending" {{ $lead->sale_status == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="won" {{ $lead->sale_status == 'won' ? 'selected' : '' }}>Gagné</option>
                        <option value="lost" {{ $lead->sale_status == 'lost' ? 'selected' : '' }}>Perdu</option>
                    </select>
                    @error('sale_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Commentaire -->
            <div class="mb-6">
                <label for="comment" class="block text-sm font-medium text-gray-700">Commentaire</label>
                <textarea id="comment" name="comment" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ $lead->comment }}</textarea>
                @error('comment')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 bg-gray-50">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Informations du lead</h3>
        <p class="mt-1 text-sm text-gray-600">
            Ces informations sont en lecture seule et ne peuvent pas être modifiées directement.
        </p>
    </div>
    <div class="border-t border-gray-200">
        <dl>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $lead->first_name }} {{ $lead->last_name }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Email</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $lead->email }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $lead->phone }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Type d'énergie</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @if($lead->energy_type == 'pompe_a_chaleur')
                        Pompe à chaleur
                    @elseif($lead->energy_type == 'panneaux_photovoltaiques')
                        Panneaux photovoltaïques
                    @elseif($lead->energy_type == 'les_deux')
                        Pompe à chaleur et panneaux photovoltaïques
                    @else
                        {{ $lead->energy_type ?? 'Non spécifié' }}
                    @endif
                </dd>
            </div>
            @if($lead->address)
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $lead->address }}<br>
                    @if($lead->postal_code || $lead->city)
                        {{ $lead->postal_code }} {{ $lead->city }}
                    @endif
                </dd>
            </div>
            @endif
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Site</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $lead->site->name ?? 'Site inconnu' }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection

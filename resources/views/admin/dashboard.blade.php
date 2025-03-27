@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Tableau de bord</h1>
        <div>
            <a href="{{ route('admin.leads.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                Voir tous les leads
            </a>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Carte: Total des leads -->
        <div class="bg-white rounded-lg shadow p-6 stat-card border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total des leads</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_leads'] }}</p>
                    <div class="flex items-center mt-1">
                        @if($stats['leads_growth'] > 0)
                            <span class="text-green-500 text-xs font-medium">+{{ $stats['leads_growth'] }}%</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-500 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        @elseif($stats['leads_growth'] < 0)
                            <span class="text-red-500 text-xs font-medium">{{ $stats['leads_growth'] }}%</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-500 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        @else
                            <span class="text-gray-500 text-xs font-medium">0%</span>
                        @endif
                        <span class="text-gray-400 text-xs ml-1">vs mois précédent</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte: Taux de conversion -->
        <div class="bg-white rounded-lg shadow p-6 stat-card border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Taux de conversion</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['conversion_rate'] }}%</p>
                    <p class="text-gray-400 text-xs mt-1">Leads qualifiés / Total</p>
                </div>
            </div>
        </div>

        <!-- Carte: Nouveaux leads aujourd'hui -->
        <div class="bg-white rounded-lg shadow p-6 stat-card border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['leads_today'] }}</p>
                    <div class="flex gap-2 mt-1">
                        <span class="text-gray-400 text-xs">Cette semaine: {{ $stats['leads_this_week'] }}</span>
                        <span class="text-gray-400 text-xs">Ce mois: {{ $stats['leads_this_month'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte: Énergie la plus demandée -->
        <div class="bg-white rounded-lg shadow p-6 stat-card border-l-4 border-indigo-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Énergie la plus demandée</p>
                    @php
                        $topEnergy = !empty($stats['leads_by_energy']) ? array_search(max($stats['leads_by_energy']), $stats['leads_by_energy']) : 'Aucune';
                        $topEnergyCount = !empty($stats['leads_by_energy']) ? max($stats['leads_by_energy']) : 0;
                    @endphp
                    <p class="text-lg font-bold text-gray-800">
                        {{ $topEnergy ?? 'Aucune' }}
                    </p>
                    <p class="text-gray-400 text-xs mt-1">
                        {{ $topEnergyCount }} leads ({{ $stats['total_leads'] > 0 ? number_format(($topEnergyCount / $stats['total_leads']) * 100, 1) : 0 }}%)
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Graphique: Évolution des leads -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Évolution des leads (30 derniers jours)</h2>
            <div class="chart-container">
                <canvas id="leadsTimelineChart"></canvas>
            </div>
        </div>

        <!-- Graphique: Répartition par heure -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Répartition par heure de la journée</h2>
            <div class="chart-container">
                <canvas id="leadsHourChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Répartition par type d'énergie -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Répartition par type d'énergie</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-500">Type d'énergie</th>
                            <th class="text-right py-2 px-3 text-sm font-medium text-gray-500">Nombre</th>
                            <th class="text-right py-2 px-3 text-sm font-medium text-gray-500">Pourcentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            arsort($stats['leads_by_energy']);
                        @endphp
                        @forelse($stats['leads_by_energy'] as $energy => $count)
                            <tr class="border-t border-gray-200">
                                <td class="py-2 px-3 text-sm">{{ $energy }}</td>
                                <td class="py-2 px-3 text-sm text-right">{{ $count }}</td>
                                <td class="py-2 px-3 text-sm text-right">
                                    {{ number_format(($count / $stats['total_leads']) * 100, 1) }}%
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-gray-200">
                                <td colspan="3" class="py-2 px-3 text-sm text-center text-gray-500">Aucune donnée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Répartition par site -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Répartition par site</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-500">Site</th>
                            <th class="text-right py-2 px-3 text-sm font-medium text-gray-500">Nombre</th>
                            <th class="text-right py-2 px-3 text-sm font-medium text-gray-500">Pourcentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            arsort($stats['leads_by_site']);
                        @endphp
                        @forelse($stats['leads_by_site'] as $site => $count)
                            <tr class="border-t border-gray-200">
                                <td class="py-2 px-3 text-sm">{{ $site }}</td>
                                <td class="py-2 px-3 text-sm text-right">{{ $count }}</td>
                                <td class="py-2 px-3 text-sm text-right">
                                    {{ number_format(($count / $stats['total_leads']) * 100, 1) }}%
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-gray-200">
                                <td colspan="3" class="py-2 px-3 text-sm text-center text-gray-500">Aucune donnée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Actions rapides</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.leads.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <p class="font-medium">Gérer les leads</p>
                    <p class="text-sm text-gray-500">Voir tous les leads</p>
                </div>
            </a>
            <a href="#" onclick="exportLeads(event)" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </div>
                <div>
                    <p class="font-medium">Exporter les leads</p>
                    <p class="text-sm text-gray-500">Format CSV</p>
                </div>
            </a>
            <a href="{{ route('admin.sites.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-medium">Gérer les sites</p>
                    <p class="text-sm text-gray-500">Configurer les sites</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Derniers leads -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Derniers leads</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type d'énergie</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentLeads as $lead)
                    <tr>
                        <td class="py-3 px-4 text-sm text-gray-500">{{ $lead->id }}</td>
                        <td class="py-3 px-4 text-sm">{{ $lead->first_name }} {{ $lead->last_name }}</td>
                        <td class="py-3 px-4 text-sm">{{ $lead->email }}</td>
                        <td class="py-3 px-4 text-sm">{{ $lead->phone }}</td>
                        <td class="py-3 px-4 text-sm">{{ $lead->energy_type }}</td>
                        <td class="py-3 px-4 text-sm">{{ $lead->site->name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-sm">{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-sm">
                            <a href="{{ route('admin.leads.edit', $lead->id) }}" class="text-blue-600 hover:text-blue-900">Éditer</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-4 px-4 text-sm text-center text-gray-500">Aucun lead récent</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            <a href="{{ route('admin.leads.index') }}" class="text-blue-600 hover:text-blue-900">Voir tous les leads</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration du graphique d'évolution des leads
        const timelineCtx = document.getElementById('leadsTimelineChart').getContext('2d');
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['leads_timeline']['labels']),
                datasets: [{
                    label: 'Leads',
                    data: @json($chartData['leads_timeline']['values']),
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Configuration du graphique de répartition par heure
        const hourCtx = document.getElementById('leadsHourChart').getContext('2d');
        new Chart(hourCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['leads_by_hour']['labels']),
                datasets: [{
                    label: 'Leads',
                    data: @json($chartData['leads_by_hour']['values']),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });

    // Fonction pour exporter les leads au format CSV
    function exportLeads(event) {
        event.preventDefault();
        window.location.href = "{{ route('admin.leads.export') }}";
    }
</script>
@endsection

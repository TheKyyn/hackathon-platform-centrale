<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentralizedLead;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    /**
     * Affiche le tableau de bord avec les statistiques et les derniers leads
     */
    public function dashboard()
    {
        // Statistiques globales
        $stats = [
            'total_leads' => CentralizedLead::count(),
            'leads_by_energy' => CentralizedLead::select('energy_type', DB::raw('count(*) as total'))
                ->groupBy('energy_type')
                ->get()
                ->pluck('total', 'energy_type')
                ->toArray(),
            'leads_by_status' => CentralizedLead::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->pluck('total', 'status')
                ->toArray(),
            'leads_by_site' => CentralizedLead::select('site_id', DB::raw('count(*) as total'))
                ->groupBy('site_id')
                ->get()
                ->map(function ($item) {
                    $site = Site::find($item->site_id);
                    return [
                        'site_name' => $site ? $site->name : 'Inconnu',
                        'total' => $item->total
                    ];
                })
                ->pluck('total', 'site_name')
                ->toArray(),
            // Nouvelles statistiques
            'conversion_rate' => $this->calculateConversionRate(),
            'leads_today' => CentralizedLead::whereDate('created_at', today())->count(),
            'leads_this_week' => CentralizedLead::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
            'leads_this_month' => CentralizedLead::whereMonth('created_at', now()->month)->count(),
            'leads_growth' => $this->calculateLeadsGrowth(),
        ];

        // Données pour les graphiques
        $chartData = [
            'leads_timeline' => $this->getLeadsTimeline(),
            'leads_by_hour' => $this->getLeadsByHour(),
        ];

        // Derniers leads (limités à 10)
        $recentLeads = CentralizedLead::with('site')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentLeads', 'chartData'));
    }

    /**
     * Calcule le taux de conversion (leads qualifiés / total)
     */
    private function calculateConversionRate()
    {
        $total = CentralizedLead::count();
        if ($total === 0) return 0;

        $qualified = CentralizedLead::where('status', 'qualifie')->count();
        return round(($qualified / $total) * 100, 1);
    }

    /**
     * Calcule la croissance des leads par rapport au mois précédent
     */
    private function calculateLeadsGrowth()
    {
        $thisMonth = CentralizedLead::whereMonth('created_at', now()->month)->count();
        $lastMonth = CentralizedLead::whereMonth('created_at', now()->subMonth()->month)->count();

        if ($lastMonth === 0) return 100;

        $growth = (($thisMonth - $lastMonth) / $lastMonth) * 100;
        return round($growth, 1);
    }

    /**
     * Récupère les données pour le graphique d'évolution des leads dans le temps
     */
    private function getLeadsTimeline()
    {
        // Récupérer les leads des 30 derniers jours, groupés par jour
        $data = CentralizedLead::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $values = [];

        // Créer un tableau avec tous les jours, même ceux sans leads
        $period = new \DatePeriod(
            now()->subDays(30),
            new \DateInterval('P1D'),
            now()->addDay()
        );

        $dateValues = $data->pluck('count', 'date')->toArray();

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $values[] = $dateValues[$dateString] ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    /**
     * Récupère la répartition des leads par heure de la journée
     */
    private function getLeadsByHour()
    {
        $data = CentralizedLead::select(
                DB::raw('cast(strftime(\'%H\', created_at) as integer) as hour'),
                DB::raw('count(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        $labels = [];
        $values = [];

        for ($hour = 0; $hour < 24; $hour++) {
            $labels[] = sprintf('%02d:00', $hour);
            $values[] = $data[$hour] ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    /**
     * Affiche la liste des leads avec filtrage
     */
    public function index(Request $request)
    {
        $query = CentralizedLead::with('site');

        // Application des filtres
        if ($request->filled('energy_type')) {
            $query->where('energy_type', $request->energy_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sale_status')) {
            $query->where('sale_status', $request->sale_status);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Options de tri
        $sortField = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $leads = $query->paginate(15);

        // Liste des sites pour le filtre
        $sites = Site::all();

        // Liste des types d'énergie et statuts pour les filtres
        $energyTypes = CentralizedLead::distinct()->pluck('energy_type')->filter();
        $statuses = CentralizedLead::distinct()->pluck('status')->filter();
        $saleStatuses = CentralizedLead::distinct()->pluck('sale_status')->filter();

        return view('admin.leads.index', compact(
            'leads',
            'sites',
            'energyTypes',
            'statuses',
            'saleStatuses'
        ));
    }

    /**
     * Affiche le formulaire d'édition d'un lead
     */
    public function edit($id)
    {
        $lead = CentralizedLead::with('site')->findOrFail($id);
        return view('admin.leads.edit', compact('lead'));
    }

    /**
     * Met à jour les informations d'un lead
     */
    public function update(Request $request, $id)
    {
        $lead = CentralizedLead::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string',
            'sale_status' => 'nullable|string',
            'comment' => 'nullable|string'
        ]);

        $lead->update($validated);

        return redirect()
            ->route('admin.leads.edit', $lead->id)
            ->with('success', 'Lead mis à jour avec succès.');
    }

    /**
     * Affiche les détails d'un lead spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $lead = CentralizedLead::with('site')->findOrFail($id);
        return view('admin.leads.show', compact('lead'));
    }

    /**
     * Exporte les leads au format CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        $query = CentralizedLead::with('site');

        // Application des filtres identiques à ceux de la méthode index
        if ($request->filled('energy_type')) {
            $query->where('energy_type', $request->energy_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sale_status')) {
            $query->where('sale_status', $request->sale_status);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Trier les résultats
        $sortField = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        // Récupérer tous les leads pour l'export
        $leads = $query->get();

        // Générer le nom du fichier
        $fileName = 'leads_export_' . date('Y-m-d_His') . '.csv';

        // Créer une réponse streaming pour gérer de grandes quantités de données
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Adresse', 'Code Postal',
            'Ville', "Type d'énergie", 'Type de propriété', 'Propriétaire', 'Projet',
            'Opt-in', 'Statut', 'Statut de vente', 'Site', 'Commentaire', 'Créé le', 'Mis à jour le'
        ];

        $callback = function() use($leads, $columns) {
            $file = fopen('php://output', 'w');

            // Ajouter l'en-tête UTF-8 BOM pour une meilleure compatibilité Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Écrire les en-têtes des colonnes
            fputcsv($file, $columns);

            // Écrire les données
            foreach ($leads as $lead) {
                $row = [
                    $lead->id,
                    $lead->first_name,
                    $lead->last_name,
                    $lead->email,
                    $lead->phone,
                    $lead->address,
                    $lead->postal_code,
                    $lead->city,
                    $lead->energy_type,
                    $lead->property_type,
                    $lead->is_owner ? 'Oui' : 'Non',
                    $lead->has_project ? 'Oui' : 'Non',
                    $lead->optin ? 'Oui' : 'Non',
                    $lead->status,
                    $lead->sale_status,
                    $lead->site->name ?? 'N/A',
                    $lead->comment,
                    $lead->created_at->format('d/m/Y H:i'),
                    $lead->updated_at->format('d/m/Y H:i')
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

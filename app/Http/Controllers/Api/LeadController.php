<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentralizedLead;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    /**
     * Stocke un nouveau lead dans la base centralisée.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Valider le token API
        $apiToken = $request->bearerToken();
        if (!$apiToken) {
            return response()->json(['error' => 'Token API manquant'], 401);
        }

        $site = Site::where('api_token', $apiToken)->where('is_active', true)->first();
        if (!$site) {
            return response()->json(['error' => 'Token API invalide ou site inactif'], 401);
        }

        // Valider les données du lead
        $validator = Validator::make($request->all(), [
            'original_id' => 'required|integer',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Vérifier si ce lead existe déjà (par site_id et original_id)
        $existingLead = CentralizedLead::where('site_id', $site->id)
            ->where('original_id', $request->original_id)
            ->first();

        if ($existingLead) {
            // Mettre à jour le lead existant
            $existingLead->update(array_merge(
                $request->all(),
                ['synced_at' => now(), 'origin_url' => $request->header('Referer')]
            ));

            return response()->json([
                'message' => 'Lead mis à jour avec succès',
                'data' => $existingLead
            ], 200);
        }

        // Créer un nouveau lead centralisé
        $leadData = array_merge($request->all(), [
            'site_id' => $site->id,
            'synced_at' => now(),
            'origin_url' => $request->header('Referer')
        ]);

        $lead = CentralizedLead::create($leadData);

        return response()->json([
            'message' => 'Lead créé avec succès',
            'data' => $lead
        ], 201);
    }

    /**
     * Récupère tous les leads pour un site spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Valider le token API
        $apiToken = $request->bearerToken();
        if (!$apiToken) {
            return response()->json(['error' => 'Token API manquant'], 401);
        }

        $site = Site::where('api_token', $apiToken)->where('is_active', true)->first();
        if (!$site) {
            return response()->json(['error' => 'Token API invalide ou site inactif'], 401);
        }

        // Appliquer les filtres
        $query = CentralizedLead::where('site_id', $site->id);

        // Filtre par date
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filtre par type de lead
        if ($request->has('energy_type')) {
            $query->where('energy_type', $request->energy_type);
        }

        // Filtre par statut
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $leads = $query->paginate($perPage);

        return response()->json([
            'data' => $leads,
            'message' => 'Leads récupérés avec succès'
        ]);
    }

    /**
     * Récupère un lead spécifique par son ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        // Valider le token API
        $apiToken = $request->bearerToken();
        if (!$apiToken) {
            return response()->json(['error' => 'Token API manquant'], 401);
        }

        $site = Site::where('api_token', $apiToken)->where('is_active', true)->first();
        if (!$site) {
            return response()->json(['error' => 'Token API invalide ou site inactif'], 401);
        }

        // Récupérer le lead
        $lead = CentralizedLead::where('site_id', $site->id)->where('id', $id)->first();

        if (!$lead) {
            return response()->json(['error' => 'Lead non trouvé'], 404);
        }

        return response()->json([
            'data' => $lead,
            'message' => 'Lead récupéré avec succès'
        ]);
    }

    /**
     * Met à jour un lead spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Valider le token API
        $apiToken = $request->bearerToken();
        if (!$apiToken) {
            return response()->json(['error' => 'Token API manquant'], 401);
        }

        $site = Site::where('api_token', $apiToken)->where('is_active', true)->first();
        if (!$site) {
            return response()->json(['error' => 'Token API invalide ou site inactif'], 401);
        }

        // Récupérer le lead
        $lead = CentralizedLead::where('site_id', $site->id)->where('id', $id)->first();

        if (!$lead) {
            return response()->json(['error' => 'Lead non trouvé'], 404);
        }

        // Mettre à jour le lead
        $lead->update(array_merge(
            $request->all(),
            ['synced_at' => now()]
        ));

        return response()->json([
            'data' => $lead,
            'message' => 'Lead mis à jour avec succès'
        ]);
    }

    /**
     * Point d'entrée pour la synchronisation des leads.
     * Permet aux sites externes d'envoyer leurs leads.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request): JsonResponse
    {
        // Valider le token API
        $apiToken = $request->bearerToken();
        if (!$apiToken) {
            return response()->json(['error' => 'Token API manquant'], 401);
        }

        $site = Site::where('api_token', $apiToken)->where('is_active', true)->first();
        if (!$site) {
            return response()->json(['error' => 'Token API invalide ou site inactif'], 401);
        }

        try {
            // Valider les données du lead
            $validator = Validator::make($request->all(), [
                'original_id' => 'required|integer',
                'first_name' => 'string|max:255',
                'last_name' => 'string|max:255',
                'email' => 'email|max:255',
                'phone' => 'string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Vérifier si ce lead existe déjà (par site_id et original_id)
            $existingLead = CentralizedLead::where('site_id', $site->id)
                ->where('original_id', $request->original_id)
                ->first();

            if ($existingLead) {
                // Assurer que nous n'avons que les champs valides dans notre tableau
                $validData = array_filter($request->all(), function($key) {
                    return in_array($key, (new CentralizedLead())->getFillable());
                }, ARRAY_FILTER_USE_KEY);

                // Mettre à jour le lead existant avec les données valides
                $existingLead->update(array_merge(
                    $validData,
                    [
                        'synced_at' => now(),
                        'origin_url' => $request->header('Referer') ?? 'sync_api'
                    ]
                ));

                Log::info("Lead #{$existingLead->id} (original #{$request->original_id}) du site #{$site->id} mis à jour avec succès");

                return response()->json([
                    'success' => true,
                    'message' => 'Lead mis à jour avec succès',
                    'data' => $existingLead
                ], 200);
            }

            // Préparer les données valides pour la création
            $validData = array_filter($request->all(), function($key) {
                return in_array($key, (new CentralizedLead())->getFillable());
            }, ARRAY_FILTER_USE_KEY);

            // Créer un nouveau lead centralisé
            $leadData = array_merge($validData, [
                'site_id' => $site->id,
                'synced_at' => now(),
                'origin_url' => $request->header('Referer') ?? 'sync_api'
            ]);

            $lead = CentralizedLead::create($leadData);

            Log::info("Nouveau lead #{$lead->id} (original #{$request->original_id}) du site #{$site->id} créé avec succès");

            return response()->json([
                'success' => true,
                'message' => 'Lead créé avec succès',
                'data' => $lead
            ], 201);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la synchronisation d'un lead: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la synchronisation: ' . $e->getMessage()
            ], 500);
        }
    }
}

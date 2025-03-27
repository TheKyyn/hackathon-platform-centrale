<?php

/*
|--------------------------------------------------------------------------
| Test Manuel de la Plateforme Centrale
|--------------------------------------------------------------------------
|
| Ce script permet de tester manuellement que la plateforme centrale
| fonctionne correctement et peut recevoir des leads.
|
| Exécutez ce script avec:
| $ php tests/manual_central_platform_test.php
|
*/

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CentralizedLead;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

// Configuration du test
echo "=== CONFIGURATION DU TEST ===\n";

// 1. Vérifier que la base de données est accessible
echo "\n> Vérification de la base de données...\n";
try {
    $connection = DB::connection()->getPdo();
    echo "✓ Connexion à la base de données établie : " . DB::connection()->getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "✗ ERREUR: Impossible de se connecter à la base de données : " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Créer ou récupérer un site de test
echo "\n> Préparation du site de test...\n";
$site = Site::firstOrCreate(
    ['name' => 'Site de Test Manuel'],
    [
        'url' => 'https://test-manuel.example.com',
        'api_token' => 'test_token_' . Str::random(32),
        'is_active' => true,
        'description' => 'Site créé pour les tests manuels',
    ]
);
echo "✓ Site de test prêt (ID: {$site->id}, Token: {$site->api_token})\n";

// 3. Tester la réception d'un lead via API locale
echo "\n=== TEST DE RÉCEPTION D'UN LEAD VIA API LOCALE ===\n";
$leadData = [
    'original_id' => 999,
    'first_name' => 'Test_' . uniqid(),
    'last_name' => 'Manuel',
    'email' => 'test.manuel.' . uniqid() . '@example.com',
    'phone' => '0601020304',
    'energy_type' => 'pompe_a_chaleur',
    'property_type' => 'maison_individuelle',
    'is_owner' => true,
    'has_project' => true,
];

try {
    // Simuler une requête API interne
    $request = Request::create('/api/v1/leads/sync', 'POST', $leadData, [], [], [
        'HTTP_AUTHORIZATION' => 'Bearer ' . $site->api_token,
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = app()->handle($request);
    $responseContent = json_decode($response->getContent(), true);

    if ($response->getStatusCode() === 201) {
        echo "✓ Lead créé avec succès via API locale\n";
        echo "  ID du lead centralisé: {$responseContent['data']['id']}\n";
    } else {
        echo "✗ ERREUR: La création du lead a échoué - Code: {$response->getStatusCode()}\n";
        echo "  Message: " . ($responseContent['error'] ?? 'Inconnu') . "\n";
    }
} catch (\Exception $e) {
    echo "✗ EXCEPTION: " . $e->getMessage() . "\n";
}

// 4. Vérifier que le lead existe dans la base de données
echo "\n> Vérification dans la base de données...\n";
try {
    $lead = CentralizedLead::where('email', $leadData['email'])->first();

    if ($lead) {
        echo "✓ Lead trouvé dans la base de données (ID: {$lead->id})\n";
        echo "  Prénom: {$lead->first_name}\n";
        echo "  Nom: {$lead->last_name}\n";
        echo "  Email: {$lead->email}\n";
        echo "  Site: {$lead->site_id} (doit correspondre à {$site->id})\n";
    } else {
        echo "✗ ERREUR: Lead non trouvé dans la base de données\n";
    }
} catch (\Exception $e) {
    echo "✗ EXCEPTION: " . $e->getMessage() . "\n";
}

// 5. Tester une mise à jour du lead
echo "\n=== TEST DE MISE À JOUR D'UN LEAD ===\n";
if (isset($lead)) {
    $updateData = [
        'original_id' => 999,
        'status' => 'qualified',
        'comment' => 'Lead qualifié par test manuel le ' . date('Y-m-d H:i:s'),
    ];

    try {
        $request = Request::create('/api/v1/leads/sync', 'POST', $updateData, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $site->api_token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $response = app()->handle($request);
        $responseContent = json_decode($response->getContent(), true);

        if ($response->getStatusCode() === 200) {
            echo "✓ Lead mis à jour avec succès\n";
            echo "  Nouveau statut: {$responseContent['data']['status']}\n";
        } else {
            echo "✗ ERREUR: La mise à jour du lead a échoué - Code: {$response->getStatusCode()}\n";
            echo "  Message: " . ($responseContent['error'] ?? 'Inconnu') . "\n";
        }
    } catch (\Exception $e) {
        echo "✗ EXCEPTION: " . $e->getMessage() . "\n";
    }

    // Vérifier la mise à jour
    $lead->refresh();
    echo "\n> Vérification de la mise à jour...\n";
    echo "  Statut: {$lead->status} (doit être 'qualified')\n";
    echo "  Commentaire: {$lead->comment}\n";
} else {
    echo "✗ Impossible de tester la mise à jour car aucun lead n'a été créé\n";
}

// 6. Tester la récupération des leads via API
echo "\n=== TEST DE RÉCUPÉRATION DES LEADS VIA API ===\n";
try {
    $request = Request::create('/api/v1/leads', 'GET', [], [], [], [
        'HTTP_AUTHORIZATION' => 'Bearer ' . $site->api_token,
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = app()->handle($request);
    $responseContent = json_decode($response->getContent(), true);

    if ($response->getStatusCode() === 200) {
        $leadsCount = count($responseContent['data']['data'] ?? []);
        echo "✓ Récupération des leads réussie\n";
        echo "  Nombre de leads récupérés: {$leadsCount}\n";
    } else {
        echo "✗ ERREUR: La récupération des leads a échoué - Code: {$response->getStatusCode()}\n";
        echo "  Message: " . ($responseContent['error'] ?? 'Inconnu') . "\n";
    }
} catch (\Exception $e) {
    echo "✗ EXCEPTION: " . $e->getMessage() . "\n";
}

echo "\n=== TEST MANUEL TERMINÉ ===\n";
echo "La plateforme centrale est " . (isset($lead) ? "prête à recevoir des leads" : "en état d'erreur") . "\n";

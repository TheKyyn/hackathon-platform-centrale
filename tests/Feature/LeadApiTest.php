<?php

namespace Tests\Feature;

use App\Models\CentralizedLead;
use App\Models\Site;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LeadApiTest extends TestCase
{
    use DatabaseMigrations;

    protected $site;
    protected $apiToken;

    /**
     * Configuration du test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Désactiver temporairement la contrainte de clé étrangère pour les tests
        \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = OFF;');

        // Désactiver les routes d'authentification pour les tests
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class);

        // Créer un site pour les tests avec un token API
        $this->apiToken = 'test_api_token_' . uniqid();
        $this->site = Site::create([
            'name' => 'Site de Test',
            'url' => 'https://test-site.example.com',
            'api_token' => $this->apiToken,
            'is_active' => true,
        ]);
    }

    /**
     * Nettoyage après le test
     */
    protected function tearDown(): void
    {
        // Réactiver la contrainte de clé étrangère
        \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = ON;');

        parent::tearDown();
    }

    /**
     * Teste l'endpoint de synchronisation des leads.
     */
    public function test_sync_endpoint_creates_new_lead(): void
    {
        // Données du lead
        $leadData = [
            'original_id' => 123,
            'first_name' => 'Thomas',
            'last_name' => 'Leroy',
            'email' => 'thomas.leroy@example.com',
            'phone' => '0601020304',
            'address' => '123 Rue du Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'energy_type' => 'pompe_a_chaleur',
            'property_type' => 'maison_individuelle',
            'is_owner' => true,
            'has_project' => true,
        ];

        // Appeler l'API avec notre token de test
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Accept' => 'application/json',
        ])->post('/api/v1/leads/sync', $leadData);

        // Si la réponse n'est pas celle attendue, afficher son contenu pour le debug
        if ($response->getStatusCode() !== 201) {
            dump($response->getContent());
        }

        // Vérifier la réponse
        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Lead créé avec succès',
                 ]);

        // Vérifier que le lead existe dans la base de données
        $this->assertDatabaseHas('centralized_leads', [
            'site_id' => $this->site->id,
            'original_id' => 123,
            'email' => 'thomas.leroy@example.com',
        ]);
    }

    /**
     * Teste la mise à jour d'un lead existant.
     */
    public function test_sync_endpoint_updates_existing_lead(): void
    {
        // Créer un lead existant
        $existingLead = CentralizedLead::create([
            'site_id' => $this->site->id,
            'original_id' => 456,
            'first_name' => 'Emma',
            'last_name' => 'Petit',
            'email' => 'emma.petit@example.com',
            'phone' => '0607080910',
        ]);

        // Données de mise à jour
        $updateData = [
            'original_id' => 456,
            'phone' => '0612345678',
            'address' => 'Nouvelle adresse',
            'status' => 'qualified',
        ];

        // Appeler l'API pour mettre à jour
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Accept' => 'application/json',
        ])->post('/api/v1/leads/sync', $updateData);

        // Vérifier la réponse
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Lead mis à jour avec succès',
                 ]);

        // Vérifier que le lead a été mis à jour
        $this->assertDatabaseHas('centralized_leads', [
            'id' => $existingLead->id,
            'phone' => '0612345678',
            'address' => 'Nouvelle adresse',
            'status' => 'qualified',
        ]);
    }

    /**
     * Teste que l'authentification est requise.
     */
    public function test_sync_endpoint_requires_authentication(): void
    {
        // Appeler l'API sans token
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/v1/leads/sync', ['original_id' => 789]);

        // Vérifier que l'accès est refusé
        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Token API manquant',
                 ]);
    }
}

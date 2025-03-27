<?php

namespace Tests\Feature;

use App\Models\CentralizedLead;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CentralPlatformTest extends TestCase
{
    use DatabaseMigrations;

    protected $site;
    protected $user;

    /**
     * Configuration du test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Désactiver temporairement la contrainte de clé étrangère pour les tests
        DB::statement('PRAGMA foreign_keys = OFF;');

        // Désactiver les routes d'authentification pour les tests
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class);

        // Créer un site pour les tests
        $this->site = Site::create([
            'name' => 'Site Test Hackathon LF',
            'url' => 'https://lf.example.com',
            'api_token' => 'test_token_' . uniqid(),
            'is_active' => true,
        ]);

        // Créer un utilisateur administrateur
        $this->user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * Nettoyage après le test
     */
    protected function tearDown(): void
    {
        // Réactiver la contrainte de clé étrangère
        DB::statement('PRAGMA foreign_keys = ON;');

        parent::tearDown();
    }

    /**
     * Teste la réception d'un nouveau lead via l'API.
     */
    public function test_platform_receives_lead_via_api(): void
    {
        // Données du lead à envoyer
        $leadData = [
            'original_id' => 123,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'phone' => '0601020304',
            'energy_type' => 'pompe_a_chaleur',
        ];

        // Envoyer les données via l'API
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->site->api_token,
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
            'email' => 'jean.dupont@example.com',
        ]);
    }

    /**
     * Teste l'affichage des leads dans le tableau de bord.
     */
    public function test_dashboard_displays_leads(): void
    {
        // Créer plusieurs leads pour le site
        for ($i = 0; $i < 5; $i++) {
            CentralizedLead::create([
                'site_id' => $this->site->id,
                'energy_type' => 'pompe_a_chaleur',
                'first_name' => 'Test' . $i,
                'last_name' => 'User' . $i,
                'email' => 'test' . $i . '@example.com',
                'phone' => '060102030' . $i,
                'original_id' => $i + 100,
            ]);
        }

        // Se connecter en tant qu'administrateur
        $response = $this->actingAs($this->user)
                        ->get('/admin/dashboard');

        // Vérifier que la page s'affiche correctement
        $response->assertStatus(200);

        // Vérifier que les leads sont présents dans la réponse
        $response->assertSee('pompe_a_chaleur');
    }

    /**
     * Teste le filtrage des leads par type d'énergie.
     */
    public function test_leads_can_be_filtered_by_energy_type(): void
    {
        // Créer des leads de différents types
        for ($i = 0; $i < 3; $i++) {
            CentralizedLead::create([
                'site_id' => $this->site->id,
                'energy_type' => 'pompe_a_chaleur',
                'first_name' => 'Test' . $i,
                'last_name' => 'User' . $i,
                'email' => 'test' . $i . '@example.com',
                'phone' => '060102030' . $i,
                'original_id' => $i + 200,
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            CentralizedLead::create([
                'site_id' => $this->site->id,
                'energy_type' => 'panneaux_photovoltaiques',
                'first_name' => 'Test' . ($i + 3),
                'last_name' => 'User' . ($i + 3),
                'email' => 'test' . ($i + 3) . '@example.com',
                'phone' => '060102030' . ($i + 3),
                'original_id' => $i + 300,
            ]);
        }

        // Se connecter et accéder à la page avec filtre
        $response = $this->actingAs($this->user)
                        ->get('/admin/leads?energy_type=pompe_a_chaleur');

        // Vérifier que la page s'affiche correctement
        $response->assertStatus(200);

        // Cette assertion est simplifiée - dans un cas réel, vous vérifieriez le contenu exact
        $response->assertSee('pompe_a_chaleur');
        $response->assertDontSee('panneaux_photovoltaiques');
    }

    /**
     * Teste l'API pour récupérer les leads d'un site spécifique.
     */
    public function test_api_returns_site_specific_leads(): void
    {
        // Créer un autre site
        $anotherSite = Site::create([
            'name' => 'Autre Site',
            'url' => 'https://autre-site.example.com',
            'api_token' => 'test_token_' . uniqid(),
            'is_active' => true,
        ]);

        // Créer des leads pour les deux sites
        for ($i = 0; $i < 3; $i++) {
            CentralizedLead::create([
                'site_id' => $this->site->id,
                'first_name' => 'Test' . $i,
                'last_name' => 'User' . $i,
                'email' => 'test' . $i . '@example.com',
                'phone' => '060102030' . $i,
                'original_id' => $i + 400,
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            CentralizedLead::create([
                'site_id' => $anotherSite->id,
                'first_name' => 'Test' . ($i + 3),
                'last_name' => 'User' . ($i + 3),
                'email' => 'test' . ($i + 3) . '@example.com',
                'phone' => '060102030' . ($i + 3),
                'original_id' => $i + 500,
            ]);
        }

        // Appeler l'API pour récupérer les leads du premier site
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->site->api_token,
            'Accept' => 'application/json',
        ])->get('/api/v1/leads');

        // Vérifier la réponse
        $response->assertStatus(200);

        // Vérifier que seuls les leads du site sont retournés
        $responseData = $response->json('data');
        $this->assertCount(3, $responseData['data']); // Pagination
    }

    /**
     * Teste la mise à jour d'un lead existant.
     */
    public function test_existing_lead_can_be_updated(): void
    {
        // Créer un lead existant
        $lead = CentralizedLead::create([
            'site_id' => $this->site->id,
            'original_id' => 456,
            'status' => 'new',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'phone' => '0601020304',
        ]);

        // Données de mise à jour
        $updateData = [
            'original_id' => 456,
            'status' => 'qualified',
            'comment' => 'Lead qualifié',
        ];

        // Envoyer la mise à jour via l'API
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->site->api_token,
            'Accept' => 'application/json',
        ])->post('/api/v1/leads/sync', $updateData);

        // Vérifier la réponse
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Lead mis à jour avec succès',
                 ]);

        // Vérifier que le lead a bien été mis à jour
        $this->assertDatabaseHas('centralized_leads', [
            'id' => $lead->id,
            'status' => 'qualified',
            'comment' => 'Lead qualifié',
        ]);
    }
}

<?php

namespace Tests\Unit;

use App\Models\CentralizedLead;
use App\Models\Site;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CentralizedLeadTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        // Désactiver temporairement la contrainte de clé étrangère pour les tests
        DB::statement('PRAGMA foreign_keys = OFF;');
    }

    protected function tearDown(): void
    {
        // Réactiver la contrainte de clé étrangère
        DB::statement('PRAGMA foreign_keys = ON;');

        parent::tearDown();
    }

    /**
     * Teste la relation entre les leads et les sites.
     */
    public function test_lead_belongs_to_site(): void
    {
        // Créer un site
        $site = Site::create([
            'name' => 'Site Test',
            'url' => 'https://test.example.com',
            'api_token' => 'test_token_' . uniqid(),
            'is_active' => true,
        ]);

        // Créer un lead associé à ce site
        $lead = CentralizedLead::create([
            'site_id' => $site->id,
            'original_id' => 123,
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);

        // Vérifier que la relation fonctionne
        $this->assertInstanceOf(Site::class, $lead->site);
        $this->assertEquals($site->id, $lead->site->id);
    }

    /**
     * Test simplifié pour valider que le test unitaire fonctionne.
     */
    public function test_lead_can_be_created(): void
    {
        // Créer un site
        $site = Site::create([
            'name' => 'Site Test',
            'url' => 'https://test.example.com',
            'api_token' => 'test_token_' . uniqid(),
            'is_active' => true,
        ]);

        // Créer un lead
        $lead = CentralizedLead::create([
            'site_id' => $site->id,
            'original_id' => 123,
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);

        $this->assertNotNull($lead->id);
        $this->assertEquals('Test', $lead->first_name);
    }

    /**
     * Teste la méthode de qualification des leads.
     */
    public function test_lead_can_be_qualified(): void
    {
        // Créer un lead non qualifié
        $lead = CentralizedLead::create([
            'site_id' => 1,
            'original_id' => 789,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'phone' => '0601020304',
            'is_owner' => false,
            'property_type' => 'appartement',
            'has_project' => false,
            'status' => 'new',
        ]);

        // Vérifier qu'il n'est pas qualifié initialement
        $this->assertFalse($lead->isQualified());

        // Mettre à jour les attributs pour le qualifier
        $lead->update([
            'is_owner' => true,
            'property_type' => 'maison_individuelle',
            'has_project' => true,
        ]);

        // Rafraîchir l'instance
        $lead->refresh();

        // Vérifier qu'il est maintenant qualifié
        $this->assertTrue($lead->isQualified());
    }

    /**
     * Teste l'accesseur pour le nom complet.
     */
    public function test_lead_has_full_name_accessor(): void
    {
        // Créer un lead avec prénom et nom
        $lead = CentralizedLead::create([
            'site_id' => 1,
            'original_id' => 456,
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'marie@example.com',
            'phone' => '0601020304',
        ]);

        // Vérifier que l'accesseur fonctionne
        $this->assertEquals('Marie Dupont', $lead->full_name);
    }

    /**
     * Teste le filtrage des leads par type d'énergie.
     */
    public function test_leads_can_be_filtered_by_scope(): void
    {
        // Créer des leads de différents types
        for ($i = 0; $i < 3; $i++) {
            CentralizedLead::create([
                'site_id' => 1,
                'original_id' => 100 + $i,
                'first_name' => 'Test' . $i,
                'last_name' => 'User' . $i,
                'email' => 'test' . $i . '@example.com',
                'phone' => '060102030' . $i,
                'energy_type' => 'pompe_a_chaleur',
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            CentralizedLead::create([
                'site_id' => 1,
                'original_id' => 200 + $i,
                'first_name' => 'PV' . $i,
                'last_name' => 'User' . $i,
                'email' => 'pv' . $i . '@example.com',
                'phone' => '070102030' . $i,
                'energy_type' => 'panneaux_photovoltaiques',
            ]);
        }

        // Utiliser le scope pour filtrer
        $filteredLeads = CentralizedLead::whereEnergyType('pompe_a_chaleur')->get();

        // Vérifier que le filtre fonctionne
        $this->assertCount(3, $filteredLeads);
        $this->assertEquals('pompe_a_chaleur', $filteredLeads->first()->energy_type);
    }

    /**
     * Teste la recherche de leads par email.
     */
    public function test_leads_can_be_searched_by_email(): void
    {
        // Créer des leads avec différents emails
        CentralizedLead::create([
            'site_id' => 1,
            'original_id' => 300,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'phone' => '0601020304',
        ]);

        CentralizedLead::create([
            'site_id' => 1,
            'original_id' => 301,
            'first_name' => 'Autre',
            'last_name' => 'Utilisateur',
            'email' => 'autre@exemple.fr',
            'phone' => '0701020304',
        ]);

        // Rechercher par email
        $found = CentralizedLead::where('email', 'test@example.com')->first();

        // Vérifier que la recherche fonctionne
        $this->assertNotNull($found);
        $this->assertEquals('test@example.com', $found->email);
    }
}

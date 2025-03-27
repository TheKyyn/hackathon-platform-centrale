<?php

namespace App\Console\Commands;

use App\Models\CentralizedLead;
use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportLeadsFromLF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:import-from-lf {count=10 : Nombre de leads à importer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importe des leads de test depuis Hackathon LF vers la plateforme centrale';

    /**
     * Les types d'énergie disponibles
     */
    protected array $energyTypes = [
        'photovoltaique',
        'pompe_a_chaleur',
        'isolation',
        'chauffage',
        'climatisation'
    ];

    /**
     * Les types de propriété disponibles
     */
    protected array $propertyTypes = [
        'maison_individuelle',
        'appartement',
        'local_commercial',
        'immeuble'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $this->info("Importation de {$count} leads de test...");

        // Récupérer le site Hackathon LF
        $site = Site::where('name', 'Hackathon LF')->first();

        if (!$site) {
            $this->error("Le site 'Hackathon LF' n'existe pas. Création en cours...");
            $site = Site::create([
                'name' => 'Hackathon LF',
                'url' => 'http://localhost:8080',
                'api_token' => \Illuminate\Support\Str::random(60)
            ]);
            $this->info("Site 'Hackathon LF' créé avec succès !");
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $createdCount = 0;

        for ($i = 0; $i < $count; $i++) {
            $lead = $this->createFakeLead($site);
            if ($lead) {
                $createdCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$createdCount} leads importés avec succès !");
    }

    /**
     * Crée un lead de test
     */
    protected function createFakeLead(Site $site): ?CentralizedLead
    {
        try {
            $faker = \Faker\Factory::create('fr_FR');

            return CentralizedLead::create([
                'site_id' => $site->id,
                'original_id' => rand(1, 1000),
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'email' => $faker->email(),
                'phone' => $faker->phoneNumber(),
                'address' => $faker->streetAddress(),
                'postal_code' => $faker->postcode(),
                'city' => $faker->city(),
                'energy_type' => $faker->randomElement($this->energyTypes),
                'property_type' => $faker->randomElement($this->propertyTypes),
                'is_owner' => $faker->boolean(80), // 80% de chance d'être propriétaire
                'has_project' => $faker->boolean(70), // 70% de chance d'avoir un projet
                'optin' => $faker->boolean(60), // 60% de chance d'avoir optin
                'ip_address' => $faker->ipv4(),
                'utm_source' => $faker->randomElement(['google', 'facebook', 'instagram', 'direct']),
                'utm_medium' => $faker->randomElement(['cpc', 'organic', 'social', 'email']),
                'utm_campaign' => $faker->randomElement(['printemps2025', 'ete2025', 'automne2025']),
                'status' => $faker->randomElement(['nouveau', 'contacte', 'qualifie', 'converti', 'perdu']),
                'sale_status' => $faker->randomElement(['en_cours', 'vendu', 'annule', null]),
                'comment' => $faker->boolean(30) ? $faker->sentence(10) : null,
                'synced_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création d\'un lead de test', [
                'exception' => $e->getMessage()
            ]);
            return null;
        }
    }
}

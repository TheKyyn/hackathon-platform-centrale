<?php

namespace Database\Factories;

use App\Models\CentralizedLead;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CentralizedLead>
 */
class CentralizedLeadFactory extends Factory
{
    /**
     * Le modèle associé à la factory.
     *
     * @var string
     */
    protected $model = CentralizedLead::class;

    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $energyTypes = ['pompe_a_chaleur', 'panneaux_photovoltaiques', 'les_deux'];
        $propertyTypes = ['maison_individuelle', 'appartement', 'immeuble'];
        $statuses = ['new', 'contacted', 'qualified', 'converted', 'lost'];
        $saleStatuses = ['pending', 'closed', 'cancelled'];

        return [
            'site_id' => Site::factory(),
            'original_id' => $this->faker->numberBetween(1, 1000),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '06' . $this->faker->numerify('########'),
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'energy_type' => $this->faker->randomElement($energyTypes),
            'property_type' => $this->faker->randomElement($propertyTypes),
            'is_owner' => $this->faker->boolean(80),
            'has_project' => $this->faker->boolean(70),
            'appointment_date' => $this->faker->optional(0.3)->dateTimeBetween('now', '+2 months'),
            'appointment_id' => $this->faker->optional(0.3)->uuid(),
            'optin' => $this->faker->boolean(60),
            'ip_address' => $this->faker->ipv4(),
            'utm_source' => $this->faker->optional(0.5)->word(),
            'utm_medium' => $this->faker->optional(0.5)->word(),
            'utm_campaign' => $this->faker->optional(0.5)->word(),
            'status' => $this->faker->randomElement($statuses),
            'sale_status' => $this->faker->optional(0.4)->randomElement($saleStatuses),
            'comment' => $this->faker->optional(0.3)->paragraph(1),
            'airtable_id' => $this->faker->optional(0.2)->uuid(),
            'origin_url' => $this->faker->url(),
            'synced_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }

    /**
     * Indiquer que le lead est qualifié.
     */
    public function qualified(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_owner' => true,
                'property_type' => 'maison_individuelle',
                'has_project' => true,
                'status' => 'qualified',
            ];
        });
    }

    /**
     * Associer le lead à un site spécifique.
     */
    public function forSite(Site $site): Factory
    {
        return $this->state(function (array $attributes) use ($site) {
            return [
                'site_id' => $site->id,
            ];
        });
    }
}

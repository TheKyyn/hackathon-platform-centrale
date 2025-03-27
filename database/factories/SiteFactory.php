<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Site>
 */
class SiteFactory extends Factory
{
    /**
     * Le modèle associé à la factory.
     *
     * @var string
     */
    protected $model = Site::class;

    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' ' . $this->faker->companySuffix(),
            'url' => $this->faker->url(),
            'description' => $this->faker->optional()->paragraph(),
            'api_token' => 'site_token_' . Str::random(32),
            'is_active' => true,
            'settings' => json_encode([
                'allowed_ips' => [$this->faker->ipv4()],
                'notification_email' => $this->faker->safeEmail(),
                'sync_frequency' => 'hourly',
            ]),
        ];
    }

    /**
     * Indiquer que le site est inactif.
     */
    public function inactive(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}

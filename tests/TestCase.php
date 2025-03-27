<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Crée l'application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    /**
     * Configuration de base pour tous les tests
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Configurer sqlite en mémoire pour éviter les conflits
        config(['database.connections.sqlite.database' => ':memory:']);

        // Cette ligne est importante pour que les tests utilisent la bonne base de données
        $this->artisan('migrate:fresh');
    }
}

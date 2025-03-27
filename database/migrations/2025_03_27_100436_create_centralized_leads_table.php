<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table existe déjà pour éviter l'erreur dans les tests
        if (!Schema::hasTable('centralized_leads')) {
            Schema::create('centralized_leads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('site_id')->constrained()->onDelete('cascade');
                $table->string('original_id');
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('city')->nullable();
                $table->string('energy_type')->nullable();
                $table->string('property_type')->nullable();
                $table->boolean('is_owner')->nullable();
                $table->boolean('has_project')->nullable();
                $table->datetime('appointment_date')->nullable();
                $table->string('appointment_id')->nullable();
                $table->boolean('optin')->default(false);
                $table->string('ip_address')->nullable();
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('status')->default('new');
                $table->string('sale_status')->nullable();
                $table->text('comment')->nullable();
                $table->string('airtable_id')->nullable();
                $table->string('origin_url')->nullable();
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();

                // Index pour les recherches fréquentes
                $table->index(['site_id', 'original_id']);
                $table->index('email');
                $table->index('phone');
                $table->index('energy_type');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centralized_leads');
    }
};

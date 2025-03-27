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
                $table->unsignedBigInteger('original_id')->nullable(); // ID du lead dans le site d'origine
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('city')->nullable();
                $table->string('energy_type')->nullable(); // pompe à chaleur, panneaux photovoltaïques, etc.
                $table->string('property_type')->nullable(); // maison individuelle, appartement, etc.
                $table->boolean('is_owner')->nullable(); // propriétaire ou locataire
                $table->boolean('has_project')->nullable(); // a un projet en cours
                $table->date('appointment_date')->nullable(); // date de rendez-vous Calendly
                $table->string('appointment_id')->nullable(); // identifiant de rendez-vous Calendly
                $table->boolean('optin')->default(false); // Consentement RGPD
                $table->string('ip_address')->nullable(); // Adresse IP
                $table->string('utm_source')->nullable(); // Source UTM pour le tracking
                $table->string('utm_medium')->nullable(); // Medium UTM pour le tracking
                $table->string('utm_campaign')->nullable(); // Campaign UTM pour le tracking
                $table->string('status')->default('new'); // Statut du lead: nouveau, contacté, qualifié, etc.
                $table->string('sale_status')->nullable(); // Statut de vente: à vendre, vendu, annulé
                $table->text('comment')->nullable(); // Commentaires sur le lead
                $table->string('airtable_id')->nullable(); // ID dans Airtable
                $table->string('origin_url')->nullable(); // URL du site d'origine
                $table->timestamp('synced_at')->nullable(); // Date de dernière synchronisation
                $table->timestamps();
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

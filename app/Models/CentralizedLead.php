<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CentralizedLead extends Model
{
    use HasFactory;

    /**
     * Attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_id',
        'original_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'postal_code',
        'city',
        'energy_type',
        'property_type',
        'is_owner',
        'has_project',
        'appointment_date',
        'appointment_id',
        'optin',
        'ip_address',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'status',
        'sale_status',
        'comment',
        'airtable_id',
        'origin_url',
        'synced_at',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_owner' => 'boolean',
        'has_project' => 'boolean',
        'optin' => 'boolean',
        'appointment_date' => 'date',
        'synced_at' => 'datetime',
    ];

    /**
     * Obtenir le site associé à ce lead.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Retourne le nom complet du lead.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Vérifie si le lead est qualifié selon les critères du projet.
     *
     * @return bool
     */
    public function isQualified(): bool
    {
        // Critères de qualification: propriétaire, maison individuelle, a un projet
        return $this->is_owner &&
               $this->property_type === 'maison_individuelle' &&
               $this->has_project;
    }
}

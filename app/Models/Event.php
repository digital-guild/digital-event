<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    //

    protected $fillable = [
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'is_online',
        'organizer_name',
        'organizer_email',
        'max_attendees',
        'price',
        'currency',
        'status',
        'banner_image',
        'additional_info'
    ];

    // Définir les attributs à traiter comme des dates
    protected $dates = [
        'start_date',
        'end_date',
    ];

    // Par défaut, convertir les attributs de type JSON en tableau
    protected $casts = [
        'additional_info' => 'array',
        'is_online' => 'boolean',
        'price' => 'decimal:2', // format des prix avec deux décimales
    ];


    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'draft' => 'Brouillon',
            'published' => 'Publié',
            'cancelled' => 'Annulé',
        ];

        return $statusLabels[$this->status] ?? 'Inconnu';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

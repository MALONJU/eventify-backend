<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
        'capacity',
        'category_id',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
    ];

    // Relation avec la catégorie
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relation avec l'utilisateur (créateur)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec les réservations
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Relation avec les utilisateurs qui ont réservé
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'reservations');
    }
} 
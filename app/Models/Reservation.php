<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'status', // 'confirmed', 'cancelled', 'waiting_list'
        'number_of_tickets'
    ];

    // Relation avec l'événement
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
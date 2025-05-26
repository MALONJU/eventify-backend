<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Auth::user()->reservations()->with(['event.category'])->paginate(10);
        return response()->json($reservations);
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'number_of_tickets' => 'required|integer|min:1'
        ]);

        // Vérifier la disponibilité
        $totalReservations = $event->reservations()
            ->where('status', 'confirmed')
            ->sum('number_of_tickets');

        $remainingCapacity = $event->capacity - $totalReservations;

        if ($remainingCapacity < $validated['number_of_tickets']) {
            // Mettre en liste d'attente si pas assez de places
            $validated['status'] = 'waiting_list';
        }

        $reservation = $event->reservations()->create([
            ...$validated,
            'user_id' => Auth::id(),
            'status' => $validated['status'] ?? 'confirmed'
        ]);

        return response()->json($reservation, 201);
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        return response()->json($reservation->load('event'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        $validated = $request->validate([
            'number_of_tickets' => 'integer|min:1',
            'status' => 'in:confirmed,cancelled,waiting_list'
        ]);

        $reservation->update($validated);

        return response()->json($reservation);
    }

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);
        
        $reservation->delete();
        return response()->json(null, 204);
    }

    public function myReservations()
    {
        $reservations = Auth::user()
            ->reservations()
            ->with(['event.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reservations);
    }
} 
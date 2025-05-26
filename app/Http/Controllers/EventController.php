<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['category', 'user'])->paginate(10);
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'location' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id'
        ]);

        $event = Event::create([
            ...$validated,
            'user_id' => Auth::id()
        ]);

        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        return response()->json($event->load(['category', 'user', 'reservations.user']));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'date' => 'date',
            'time' => 'date_format:H:i',
            'location' => 'string',
            'capacity' => 'integer|min:1',
            'category_id' => 'exists:categories,id'
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        
        $event->delete();
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $query = Event::query();

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->category . '%');
            });
        }

        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        return response()->json($query->with(['category', 'user'])->paginate(10));
    }
} 
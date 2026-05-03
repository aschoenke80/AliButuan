<?php

namespace App\Http\Controllers;

use App\Models\Event;

class MapController extends Controller
{
    // Show the interactive map page
    public function index()
    {
        $categories = array_keys(Event::CATEGORIES);
        return view('map.index', compact('categories'));
    }

    // Return approved events as JSON for Leaflet markers
    public function events()
    {
        $events = Event::approved()
            ->select('id', 'title', 'category', 'location_name', 'latitude', 'longitude', 'start_datetime', 'image')
            ->get()
            ->map(function ($event) {
                return [
                    'id'            => $event->id,
                    'title'         => $event->title,
                    'category'      => $event->category,
                    'emoji'         => $event->category_emoji,
                    'location_name' => $event->location_name,
                    'latitude'      => $event->latitude,
                    'longitude'     => $event->longitude,
                    'start_date'    => $event->start_datetime->format('M d, Y'),
                    'url'           => route('events.show', $event->id),
                    'image_url'     => $event->image ? asset('storage/' . $event->image) : null,
                ];
            });

        return response()->json($events);
    }
}

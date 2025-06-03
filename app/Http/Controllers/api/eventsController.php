<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class eventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::select(
            "id",
            "name",
            "event_date",
            "description",
            "location",
            "poster",
            "is_paid",
            "price",
            "payment_methods",
            "unit_kegiatan_id"
        )
            ->with("unitKegiatan:id,name,logo")
            ->orderBy("event_date", "desc")
            ->get();

        // Check if the event exists
        if (!$events) {
            return response()->json(["message" => "Event not found"], 404);
        }

        // return the data as a JSON response
        return response()->json($events);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the event by ID
        $event = Event::select(
            "id",
            "name",
            "event_date",
            "description",
            "location",
            "poster",
            "is_paid",
            "price",
            "payment_methods",
            "unit_kegiatan_id"
        )
            ->with("unitKegiatan:id,name,logo")
            ->where("id", $id)
            ->first();

        // Check if the event exists
        if (!$event) {
            return response()->json(["message" => "Event not found"], 404);
        }

        // Return the event data as a JSON response
        return response()->json($event);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

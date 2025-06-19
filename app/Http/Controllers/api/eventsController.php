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

        // return the data as a JSON response
        return response()->json(
            [
                "status" => true,
                "message" => "Events retrieved successfully",
                "data" => $events,
            ],
            200
        );
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

        // Return the event data as a JSON response
        return response()->json(
            [
                "status" => true,
                "message" => "Event retrieved successfully",
                "data" => $event,
            ],
            200
        );
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranAnggota;
use App\Models\UnitKegiatan;
use Illuminate\Http\Request;

class ukmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ukmData = UnitKegiatan::select("id", "name", "logo")
            ->with([
                "unitKegiatanProfile" => function ($query) {
                    $query
                        ->select("id", "unit_kegiatan_id", "description")
                        ->latest()
                        ->take(1);
                },
            ])
            ->get();

        // Return the data as a JSON response
        return response()->json(
            [
                "status" => true,
                "message" => "UKM data retrieved successfully",
                "data" => $ukmData,
            ],
            200
        );
    }

    /**
     * Display profile ukm.
     */
    public function profile(string $id)
    {
        // Find the UKM by ID
        $ukm = UnitKegiatan::select("id", "name", "logo")
            ->with([
                "unitKegiatanProfile" => function ($query) {
                    $query->select("id", "unit_kegiatan_id", "description");
                },
            ])
            ->findOrFail($id);

        // Return the UKM profile as a JSON response
        return response()->json(
            [
                "status" => true,
                "message" => "UKM profile retrieved successfully",
                "data" => $ukm,
            ],
            200
        );
    }

    /**
     * Register a new member for a UKM
     */
    public function registerMember(Request $request, string $id)
    {
        // Get the authenticated user
        $user = $request->user();

        // Check if UKM exists
        $ukm = UnitKegiatan::findOrFail($id);

        // Check if the user is already registered for this UKM
        $existingRegistration = PendaftaranAnggota::where("user_id", $user->id)
            ->where("unit_kegiatan_id", $ukm->id)
            ->first();
        if ($existingRegistration) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "You are already registered for this UKM",
                ],
                400
            );
        }

        // try to create a new registration
        $registration = PendaftaranAnggota::create([
            "user_id" => $user->id,
            "unit_kegiatan_id" => $ukm->id,
        ]);

        // Check if the registration was successful
        if (!$registration) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Registration failed",
                ],
                500
            );
        }

        // Return a success response
        return response()->json(
            [
                "status" => true,
                "message" => "Registration submitted successfully",
                "data" => $registration,
            ],
            201
        );
    }
}

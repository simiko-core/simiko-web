<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\UnitKegiatan;
use Illuminate\Http\Request;

class ukmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ukmData = UnitKegiatan::select('id', 'name', 'logo')
        ->with(['unitKegiatanProfile' => function ($query) {
            $query->select('id', 'unit_kegiatan_id', 'description')
                  ->latest()
                  ->take(1);
        }])
        ->get();

        // Return the data as a JSON response
        return response()->json($ukmData);
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
        //
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

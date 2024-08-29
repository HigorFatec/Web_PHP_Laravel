<?php

namespace App\Http\Controllers;

use App\Models\Sobre;
use Illuminate\Http\Request;

class SobreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reserva.sobre');
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
    public function show(Sobre $sobre)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sobre $sobre)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sobre $sobre)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sobre $sobre)
    {
        //
    }
}

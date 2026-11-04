<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
            public function index()
    {
        $contenus = \App\Models\Publication::with(['categorie', 'auteur'])
            ->latest()
            ->paginate(9);

        return view('contenus.index', [
            'contenus' => $contenus,
            'tri' => 'Du plus récent au plus ancien' // On passe une chaîne pour le texte de la vue
        ]);
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
         public function show(\App\Models\Publication $publication)
    {
        $publication->load(['categorie', 'auteur', 'commentaires.auteur']);
        return view('contenus.show', ['publication' => $publication]);
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

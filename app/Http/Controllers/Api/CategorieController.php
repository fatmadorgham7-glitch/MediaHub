<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\JsonResponse;

class CategorieController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Categorie::withCount('publications')
            ->get()
            ->map(fn (Categorie $categorie) => [
                'id' => $categorie->id,
                'nom' => $categorie->nom,
                'slug' => $categorie->slug,
                'publications_count' => $categorie->publications_count,
            ]);

        return response()->json($categories);
    }
}

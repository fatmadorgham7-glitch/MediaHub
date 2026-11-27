<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicationController;
use App\Models\Categorie;
use Illuminate\Support\Facades\Route;

Route::post('/connexion', [AuthController::class, 'connecter']);

Route::apiResource('publications', PublicationController::class)
    ->only(['index', 'show']);

Route::get('/categories', function () {
    return response()->json(
        Categorie::withCount('publications')
            ->get()
            ->map(fn (Categorie $categorie) => [
                'id' => $categorie->id,
                'nom' => $categorie->nom,
                'slug' => $categorie->slug,
                'publications_count' => $categorie->publications_count,
            ])
    );
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('publications', PublicationController::class)
        ->except(['index', 'show']);
});

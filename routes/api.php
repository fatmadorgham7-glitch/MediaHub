<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\PublicationDocumentController;
use Illuminate\Support\Facades\Route;

Route::post('/connexion', [AuthController::class, 'connecter']);

Route::apiResource('publications', PublicationController::class)
    ->only(['index', 'show']);

Route::get('/categories', [CategorieController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/publications/{publication}/document', PublicationDocumentController::class)
        ->name('api.publications.document');

    Route::apiResource('publications', PublicationController::class)
        ->except(['index', 'show']);
});

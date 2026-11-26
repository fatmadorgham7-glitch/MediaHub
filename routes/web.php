<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContenuController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategorieController;

// 1. Page d'accueil
Route::get('/', [PageController::class, 'accueil'])->name('accueil');

// 2. Page À propos
Route::get('/a-propos', [PageController::class, 'aPropos'])->name('a-propos');

// 3. Routes pour les contenus (génère index, show, create, store, edit, update, destroy)
Route::resource('contenus', ContenuController::class);

// 4. Routes pour l'administration
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/tableau-de-bord', [AdminController::class, 'index'])->name('dashboard');
});

// 5. Route pour les catégories
Route::get('/categories', [CategorieController::class, 'index'])->name('categories.index');

Route::resource('publications', PublicationController::class)
    ->parameters(['publications' => 'contenu']);
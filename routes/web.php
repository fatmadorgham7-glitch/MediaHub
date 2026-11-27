<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ContenuController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// 1. Page d'accueil
Route::get('/', [PageController::class, 'accueil'])->name('accueil');

// 2. Page À propos
Route::get('/a-propos', [PageController::class, 'aPropos'])->name('a-propos');

// 3. Routes pour les contenus (génère index, show, create, store, edit, update, destroy)
Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'create'])->name('inscription');
    Route::post('/inscription', [AuthController::class, 'store']);
    Route::get('/connexion', [AuthController::class, 'creerConnexion'])->name('connexion');
    Route::get('/login', [AuthController::class, 'creerConnexion'])->name('login');
    Route::post('/connexion', [AuthController::class, 'connecter']);
});

Route::middleware('auth')->post('/deconnexion', [AuthController::class, 'deconnecter'])->name('deconnexion');

Route::resource('contenus', ContenuController::class)
    ->parameters(['contenus' => 'publication'])
    ->except(['create', 'store', 'edit', 'update', 'destroy']);

Route::middleware('auth')->group(function () {
    Route::resource('contenus', ContenuController::class)
        ->parameters(['contenus' => 'publication'])
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
});

// 4. Routes pour l'administration
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/tableau-de-bord', [AdminController::class, 'index'])->name('dashboard');
});

// 5. Route pour les catégories
Route::resource('categories', CategorieController::class)
    ->only(['index']);

Route::middleware(['auth'])->group(function () {
    Route::resource('categories', CategorieController::class)
        ->parameters(['categories' => 'categorie'])
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
});

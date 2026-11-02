<?php
use App\Http\Controllers\ContenuController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('Accueil de MediaHub');
});
Route::get('/contenus', function () {
    return view('Liste des contenus multimédias');
});
Route::get('/contenus/{id}', function ($id) {
    return 'Détail du contenu numéro ' . $id;
})->name('contenus.show');
Route::get('/a-propos', function () {
    return view('À propos de MediaHub ');
})->name('a-propos');

Route::get('/', [PageController::class, 'accueil']);
Route::get('/a-propos', [PageController::class, 'aPropos'])->name('a-propos');

Route::resource('contenus', ContenuController::class);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/tableau-de-bord', [AdminController::class, 'index'])->name('dashboard');
});
<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function index()
    {
        Gate::authorize('gerer-categories');

        return view('admin.dashboard', [
            'nombrePublications' => Publication::count(),
            'nombreCategories' => Categorie::count(),
            'nombreUtilisateurs' => User::count(),
        ]);
    }
}

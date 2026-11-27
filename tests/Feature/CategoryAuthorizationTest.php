<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_auteur_ne_peut_pas_editer_une_categorie(): void
    {
        $auteur = User::factory()->create();
        $categorie = Categorie::factory()->create();

        $this->actingAs($auteur)
            ->get("/categories/{$categorie->id}/edit")
            ->assertForbidden();
    }

    public function test_un_admin_peut_editer_une_categorie(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $categorie = Categorie::factory()->create();

        $this->actingAs($admin)
            ->get("/categories/{$categorie->id}/edit")
            ->assertOk();
    }
}

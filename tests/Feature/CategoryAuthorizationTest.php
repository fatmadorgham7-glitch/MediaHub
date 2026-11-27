<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Publication;
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

    public function test_une_categorie_avec_une_publication_supprimee_logiquement_ne_peut_pas_etre_supprimee(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $categorie = Categorie::factory()->create();
        $publication = Publication::factory()->create(['categorie_id' => $categorie->id]);
        $publication->delete();

        $this->actingAs($admin)
            ->delete("/categories/{$categorie->id}")
            ->assertRedirect('/categories')
            ->assertSessionHas('erreur');

        $this->assertDatabaseHas('categories', ['id' => $categorie->id]);
    }
}

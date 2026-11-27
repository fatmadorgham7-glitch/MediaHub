<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visiteur_ne_peut_pas_creer_de_publication(): void
    {
        $this->post('/contenus', [])
            ->assertRedirect('/connexion');
    }

    public function test_un_auteur_ne_peut_pas_modifier_la_publication_dun_tiers(): void
    {
        $tiers = User::factory()->create();
        $auteur = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $publication = Publication::factory()->create([
            'categorie_id' => $categorie->id,
            'user_id' => $tiers->id,
        ]);

        $this->actingAs($auteur)
            ->put("/contenus/{$publication->id}", [
                'titre' => 'Titre modifie',
                'contenu' => str_repeat('a', 30),
                'categorie_id' => $categorie->id,
            ])
            ->assertForbidden();
    }

    public function test_un_admin_peut_modifier_une_publication(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $categorie = Categorie::factory()->create();
        $publication = Publication::factory()->create(['categorie_id' => $categorie->id]);

        $this->actingAs($admin)
            ->put("/contenus/{$publication->id}", [
                'titre' => 'Titre modifie par admin',
                'contenu' => str_repeat('b', 30),
                'categorie_id' => $categorie->id,
            ])
            ->assertRedirect("/contenus/{$publication->id}");
    }
}

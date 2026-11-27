<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visiteur_ne_peut_pas_creer_de_publication(): void
    {
        $this->post('/contenus', [])
            ->assertRedirect('/connexion');
    }

    public function test_un_visiteur_est_redirige_vers_la_connexion_pour_ajouter_une_publication(): void
    {
        $this->get('/contenus/create')
            ->assertRedirect('/connexion');
    }

    public function test_un_auteur_peut_ouvrir_le_formulaire_de_creation(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/contenus/create')
            ->assertOk()
            ->assertSee('Créer une publication');
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

    public function test_un_televersement_remplace_le_fichier_apres_la_mise_a_jour(): void
    {
        Storage::fake('public');

        $auteur = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $ancienChemin = UploadedFile::fake()
            ->create('ancienne.jpg', 10, 'image/jpeg')
            ->store('publications', 'public');
        $publication = Publication::factory()->create([
            'categorie_id' => $categorie->id,
            'user_id' => $auteur->id,
            'image' => $ancienChemin,
        ]);

        $this->actingAs($auteur)
            ->put("/contenus/{$publication->id}", [
                'titre' => 'Publication avec une nouvelle image',
                'contenu' => str_repeat('contenu ', 5),
                'categorie_id' => $categorie->id,
                'image' => UploadedFile::fake()->create('nouvelle.jpg', 10, 'image/jpeg'),
            ])
            ->assertRedirect("/contenus/{$publication->id}");

        $publication->refresh();
        $this->assertNotSame($ancienChemin, $publication->image);
        Storage::disk('public')->assertMissing($ancienChemin);
        Storage::disk('public')->assertExists($publication->image);
    }

    public function test_la_suppression_logique_conserve_le_media_jusqua_la_suppression_definitive(): void
    {
        Storage::fake('public');

        $chemin = UploadedFile::fake()
            ->create('couverture.jpg', 10, 'image/jpeg')
            ->store('publications', 'public');
        $publication = Publication::factory()->create(['image' => $chemin]);

        $publication->delete();
        Storage::disk('public')->assertExists($chemin);

        $publication->forceDelete();
        Storage::disk('public')->assertMissing($chemin);
    }

    public function test_une_publication_invalide_le_cache_des_compteurs_de_categories(): void
    {
        Cache::put('categories.liste', collect(), 3600);

        Publication::factory()->create();

        $this->assertFalse(Cache::has('categories.liste'));
    }
}

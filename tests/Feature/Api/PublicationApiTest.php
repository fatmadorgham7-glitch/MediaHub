<?php

namespace Tests\Feature\Api;

use App\Models\Categorie;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_liste_publique_des_publications(): void
    {
        Publication::factory()->count(5)->create();

        $this->getJson('/api/publications')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('data.0.commentaires_count', 0);
    }

    public function test_creation_sans_jeton_est_refusee(): void
    {
        $this->postJson('/api/publications', [])
            ->assertUnauthorized();
    }

    public function test_connexion_api_retourne_un_jeton_personnel(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->postJson('/api/connexion', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertOk()
            ->assertJsonStructure(['token']);
    }

    public function test_creation_api_stocke_une_image_validee(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/publications', [
            'titre' => 'Publication avec image',
            'contenu' => str_repeat('contenu ', 5),
            'categorie_id' => $categorie->id,
            'image' => UploadedFile::fake()->create('couverture.jpg', 10, 'image/jpeg'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.titre', 'Publication avec image');

        $publication = Publication::where('titre', 'Publication avec image')->firstOrFail();
        Storage::disk('public')->assertExists($publication->image);
    }

    public function test_mise_a_jour_api_supprime_un_media_explicitement_efface(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $chemin = UploadedFile::fake()
            ->create('ancienne.jpg', 10, 'image/jpeg')
            ->store('publications', 'public');
        $publication = Publication::factory()->create([
            'categorie_id' => $categorie->id,
            'user_id' => $user->id,
            'image' => $chemin,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->putJson("/api/publications/{$publication->id}", [
            'titre' => 'Publication sans image',
            'contenu' => str_repeat('contenu ', 5),
            'categorie_id' => $categorie->id,
            'image' => null,
        ])
            ->assertOk()
            ->assertJsonPath('data.image', null);

        Storage::disk('public')->assertMissing($chemin);
    }
}

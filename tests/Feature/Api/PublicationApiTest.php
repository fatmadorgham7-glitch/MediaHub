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
            ->assertJsonPath('data.0.commentaires_count', 0)
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'titre',
                    'contenu',
                    'categorie',
                    'auteur',
                    'image',
                    'fichier_media',
                    'type_media',
                    'document_url',
                    'commentaires_count',
                    'publie_le',
                ]],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_filtre_pagination_et_categories_api(): void
    {
        $categorie = Categorie::factory()->create();
        $autreCategorie = Categorie::factory()->create();
        Publication::factory()->count(12)->create(['categorie_id' => $categorie->id]);
        Publication::factory()->count(2)->create(['categorie_id' => $autreCategorie->id]);

        $this->getJson("/api/publications?categorie_id={$categorie->id}&page=2")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 12);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('0.id', $categorie->id)
            ->assertJsonPath('0.publications_count', 12);
    }

    public function test_detail_public_d_une_publication(): void
    {
        $publication = Publication::factory()->create();

        $this->getJson("/api/publications/{$publication->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $publication->id)
            ->assertJsonPath('data.titre', $publication->titre);
    }

    public function test_creation_sans_jeton_est_refusee(): void
    {
        $this->postJson('/api/publications', [])
            ->assertUnauthorized();

        $publication = Publication::factory()->create();

        $this->deleteJson("/api/publications/{$publication->id}")
            ->assertUnauthorized();
    }

    public function test_validation_api_invalide_retourne_422(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/publications', [
            'titre' => 'Court',
            'contenu' => 'Trop court',
            'categorie_id' => 99999,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['titre', 'contenu', 'categorie_id']);
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

    public function test_connexion_api_refuse_un_mot_de_passe_incorrect(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->postJson('/api/connexion', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Identifiants incorrects.');
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

    public function test_un_autre_utilisateur_ne_peut_pas_supprimer_une_publication_api(): void
    {
        $auteur = User::factory()->create();
        $autreUtilisateur = User::factory()->create();
        $publication = Publication::factory()->create(['user_id' => $auteur->id]);
        $jetonAutreUtilisateur = $autreUtilisateur->createToken('autre')->plainTextToken;

        $this->withToken($jetonAutreUtilisateur)
            ->deleteJson("/api/publications/{$publication->id}")
            ->assertForbidden();
    }

    public function test_le_proprietaire_peut_supprimer_une_publication_api(): void
    {
        $auteur = User::factory()->create();
        $publication = Publication::factory()->create(['user_id' => $auteur->id]);
        $jetonAuteur = $auteur->createToken('auteur')->plainTextToken;

        $this->withToken($jetonAuteur)
            ->deleteJson("/api/publications/{$publication->id}")
            ->assertNoContent();

        $this->assertSoftDeleted($publication);
    }

    public function test_le_document_api_est_interdit_aux_utilisateurs_non_autorises(): void
    {
        Storage::fake('local');

        $auteur = User::factory()->create();
        $autreUtilisateur = User::factory()->create();
        $document = 'documents/guide.pdf';
        Storage::disk('local')->put($document, 'contenu prive');
        $publication = Publication::factory()->create([
            'user_id' => $auteur->id,
            'document' => $document,
        ]);

        $this->getJson("/api/publications/{$publication->id}/document")
            ->assertUnauthorized();

        $autreJeton = $autreUtilisateur->createToken('autre')->plainTextToken;
        $this->withToken($autreJeton)
            ->getJson("/api/publications/{$publication->id}/document")
            ->assertForbidden();
    }

    public function test_l_auteur_peut_telecharger_le_document_depuis_l_api(): void
    {
        Storage::fake('local');

        $auteur = User::factory()->create();
        $document = 'documents/guide.pdf';
        Storage::disk('local')->put($document, 'contenu prive');
        $publication = Publication::factory()->create([
            'user_id' => $auteur->id,
            'document' => $document,
        ]);

        $jetonAuteur = $auteur->createToken('auteur')->plainTextToken;
        $this->withToken($jetonAuteur)
            ->get("/api/publications/{$publication->id}/document")
            ->assertOk()
            ->assertDownload('guide.pdf');

        $this->getJson("/api/publications/{$publication->id}")
            ->assertOk()
            ->assertJsonPath('data.document_url', route('api.publications.document', $publication))
            ->assertJsonMissingPath('data.document');
    }
}

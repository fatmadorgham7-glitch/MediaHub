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

    public function test_la_liste_affiche_l_image_associee_a_la_publication(): void
    {
        Storage::fake('public');

        $image = 'publications/couverture.jpg';
        Storage::disk('public')->put($image, 'image');
        Publication::factory()->create(['image' => $image]);

        $this->get('/contenus')
            ->assertOk()
            ->assertSee(Storage::url($image), false);
    }

    public function test_une_publication_refuse_deux_medias_publics(): void
    {
        Storage::fake('public');

        $auteur = User::factory()->create();
        $categorie = Categorie::factory()->create();

        $this->actingAs($auteur)
            ->from('/contenus/create')
            ->post('/contenus', [
                'titre' => 'Publication avec deux médias',
                'contenu' => str_repeat('contenu ', 5),
                'categorie_id' => $categorie->id,
                'image' => UploadedFile::fake()->create('couverture.jpg', 10, 'image/jpeg'),
                'fichier_media' => UploadedFile::fake()->create('audio.mp3', 10, 'audio/mpeg'),
            ])
            ->assertRedirect('/contenus/create')
            ->assertSessionHasErrors('fichier_media');

        $this->assertDatabaseCount('publications', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_une_image_svg_n_est_pas_acceptee_sur_le_disque_public(): void
    {
        Storage::fake('public');

        $auteur = User::factory()->create();
        $categorie = Categorie::factory()->create();

        $this->actingAs($auteur)
            ->from('/contenus/create')
            ->post('/contenus', [
                'titre' => 'Publication avec image non autorisee',
                'contenu' => str_repeat('contenu ', 5),
                'categorie_id' => $categorie->id,
                'image' => UploadedFile::fake()->create('image.svg', 10, 'image/svg+xml'),
            ])
            ->assertRedirect('/contenus/create')
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('publications', 0);
    }

    public function test_un_auteur_peut_remplacer_un_media_en_supprimant_lancien(): void
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
                'titre' => 'Publication avec un seul média audio',
                'contenu' => str_repeat('contenu ', 5),
                'categorie_id' => $categorie->id,
                'retirer_image' => '1',
                'fichier_media' => UploadedFile::fake()->create('audio.mp3', 10, 'audio/mpeg'),
            ])
            ->assertRedirect("/contenus/{$publication->id}");

        $publication->refresh();
        $this->assertNull($publication->image);
        $this->assertSame('audio', $publication->type_media);
        Storage::disk('public')->assertMissing($ancienChemin);
        Storage::disk('public')->assertExists($publication->fichier_media);
    }

    public function test_une_mise_a_jour_refuse_un_second_media_sans_supprimer_le_premier(): void
    {
        Storage::fake('public');

        $auteur = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $image = UploadedFile::fake()
            ->create('couverture.jpg', 10, 'image/jpeg')
            ->store('publications', 'public');
        $publication = Publication::factory()->create([
            'categorie_id' => $categorie->id,
            'user_id' => $auteur->id,
            'image' => $image,
        ]);

        $this->actingAs($auteur)
            ->from("/contenus/{$publication->id}/edit")
            ->put("/contenus/{$publication->id}", [
                'titre' => 'Publication avec media en trop',
                'contenu' => str_repeat('contenu ', 5),
                'categorie_id' => $categorie->id,
                'fichier_media' => UploadedFile::fake()->create('audio.mp3', 10, 'audio/mpeg'),
            ])
            ->assertRedirect("/contenus/{$publication->id}/edit")
            ->assertSessionHasErrors('fichier_media');

        $publication->refresh();
        $this->assertSame($image, $publication->image);
        $this->assertNull($publication->fichier_media);
        $this->assertSame([$image], Storage::disk('public')->allFiles());
    }

    public function test_un_document_est_stocke_sur_le_disque_prive(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $auteur = User::factory()->create();
        $categorie = Categorie::factory()->create();

        $this->actingAs($auteur)
            ->post('/contenus', [
                'titre' => 'Publication avec document privé',
                'contenu' => str_repeat('contenu ', 5),
                'categorie_id' => $categorie->id,
                'document' => UploadedFile::fake()->create('guide.pdf', 10, 'application/pdf'),
            ])
            ->assertRedirect('/contenus');

        $publication = Publication::where('titre', 'Publication avec document privé')->firstOrFail();
        Storage::disk('local')->assertExists($publication->document);
        Storage::disk('public')->assertMissing($publication->document);
    }

    public function test_un_document_est_remplace_et_supprime_du_disque_prive(): void
    {
        Storage::fake('local');

        $auteur = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $ancienDocument = 'documents/ancien.pdf';
        Storage::disk('local')->put($ancienDocument, 'ancien contenu');
        $publication = Publication::factory()->create([
            'categorie_id' => $categorie->id,
            'user_id' => $auteur->id,
            'document' => $ancienDocument,
        ]);

        $this->actingAs($auteur)
            ->put("/contenus/{$publication->id}", [
                'titre' => 'Publication avec document remplace',
                'contenu' => str_repeat('contenu ', 5),
                'categorie_id' => $categorie->id,
                'document' => UploadedFile::fake()->create('nouveau.pdf', 10, 'application/pdf'),
            ])
            ->assertRedirect("/contenus/{$publication->id}");

        $publication->refresh();
        $nouveauDocument = $publication->document;
        $this->assertNotSame($ancienDocument, $nouveauDocument);
        Storage::disk('local')->assertMissing($ancienDocument);
        Storage::disk('local')->assertExists($nouveauDocument);

        $this->put("/contenus/{$publication->id}", [
            'titre' => 'Publication sans document prive',
            'contenu' => str_repeat('contenu ', 5),
            'categorie_id' => $categorie->id,
            'retirer_document' => '1',
        ])->assertRedirect("/contenus/{$publication->id}");

        $publication->refresh();
        $this->assertNull($publication->document);
        Storage::disk('local')->assertMissing($nouveauDocument);
    }

    public function test_le_document_prive_est_reserve_auteur_et_administrateur(): void
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

        $this->get("/contenus/{$publication->id}")
            ->assertOk()
            ->assertDontSee('Télécharger le document privé');

        $this->get("/contenus/{$publication->id}/document")
            ->assertRedirect('/connexion');

        $this->actingAs($autreUtilisateur)
            ->get("/contenus/{$publication->id}/document")
            ->assertForbidden();

        $this->actingAs($auteur)
            ->get("/contenus/{$publication->id}/document")
            ->assertOk()
            ->assertDownload('guide.pdf');

        $administrateur = User::factory()->create(['role' => 'admin']);
        $this->actingAs($administrateur)
            ->get("/contenus/{$publication->id}/document")
            ->assertOk()
            ->assertDownload('guide.pdf');
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

    public function test_la_suppression_logique_supprime_les_medias_publics_et_prives(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $chemin = UploadedFile::fake()
            ->create('couverture.jpg', 10, 'image/jpeg')
            ->store('publications', 'public');
        $document = 'documents/guide.pdf';
        Storage::disk('local')->put($document, 'contenu prive');
        $publication = Publication::factory()->create([
            'image' => $chemin,
            'document' => $document,
        ]);

        $publication->delete();
        $publication->refresh();
        $this->assertNull($publication->image);
        $this->assertNull($publication->document);
        Storage::disk('public')->assertMissing($chemin);
        Storage::disk('local')->assertMissing($document);

        $publication->forceDelete();
        Storage::disk('public')->assertMissing($chemin);
        Storage::disk('local')->assertMissing($document);
    }

    public function test_une_publication_invalide_le_cache_des_compteurs_de_categories(): void
    {
        Cache::put('categories.liste', collect(), 3600);

        Publication::factory()->create();

        $this->assertFalse(Cache::has('categories.liste'));
    }
}

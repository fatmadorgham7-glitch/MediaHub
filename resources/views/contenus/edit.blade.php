@extends('layouts.app')

@section('titre', 'Modifier une publication')

@section('contenu')
    <section class="page-heading">
        <div>
            <p class="eyebrow">Espace créateur</p>
            <h1>Modifier la publication</h1>
            <p class="page-intro">Ajustez les informations ou remplacez les médias associés.</p>
        </div>
    </section>

    <div class="form-layout">
        <form class="form-panel" method="POST" action="{{ route('contenus.update', $publication) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section-heading">
                <span class="form-step">01</span>
                <div>
                    <h2>Les informations</h2>
                    <p>Gardez le titre et le contenu à jour.</p>
                </div>
            </div>

            <div class="form-field">
                <label for="titre">Titre</label>
                <input class="form-control" id="titre" type="text" name="titre" value="{{ old('titre', $publication->titre) }}" minlength="10" maxlength="255" required>
                @error('titre') <span class="form-error" role="alert">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="categorie_id">Catégorie</label>
                <select class="form-control" id="categorie_id" name="categorie_id" required>
                    @foreach ($categories as $categorie)
                        <option value="{{ $categorie->id }}" @selected(old('categorie_id', $publication->categorie_id) == $categorie->id)>
                            {{ $categorie->nom }}
                        </option>
                    @endforeach
                </select>
                @error('categorie_id') <span class="form-error" role="alert">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="contenu">Contenu</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="6" minlength="20" required>{{ old('contenu', $publication->contenu) }}</textarea>
                @error('contenu') <span class="form-error" role="alert">{{ $message }}</span> @enderror
            </div>

            <div class="form-section-heading">
                <span class="form-step">02</span>
                <div>
                    <h2>Les médias</h2>
                    <p>Laissez un champ vide pour conserver le fichier actuel.</p>
                </div>
            </div>

            <div class="form-field">
                <label for="image">Nouvelle image</label>
                <input class="file-control" id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,.bmp,image/jpeg,image/png,image/webp,image/gif,image/bmp">
                <p class="form-help">JPG, PNG, WEBP, GIF ou BMP — 2 Mo maximum.</p>
                @error('image') <span class="form-error" role="alert">{{ $message }}</span> @enderror
                @if ($publication->image)
                    <label class="form-check"><input type="checkbox" name="retirer_image" value="1" @checked(old('retirer_image'))> Supprimer l'image actuelle</label>
                @endif
            </div>

            <div class="form-field">
                <label for="fichier_media">Nouveau média audio ou vidéo</label>
                <input class="file-control" id="fichier_media" type="file" name="fichier_media" accept=".mp3,.mp4,.wav,audio/mpeg,audio/wav,video/mp4">
                <p class="form-help">MP3, MP4 ou WAV — 20 Mo maximum.</p>
                @error('fichier_media') <span class="form-error" role="alert">{{ $message }}</span> @enderror
                @if ($publication->fichier_media)
                    <label class="form-check"><input type="checkbox" name="retirer_fichier_media" value="1" @checked(old('retirer_fichier_media'))> Supprimer le média actuel</label>
                @endif
            </div>

            <div class="notice-inline"><strong>Rappel</strong><span>La publication ne peut conserver qu'une image ou un fichier audio/vidéo.</span></div>

            <div class="form-field">
                <label for="document">Nouveau document privé</label>
                <input class="file-control" id="document" type="file" name="document" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                <p class="form-help">PDF ou DOCX — 5 Mo maximum.</p>
                @error('document') <span class="form-error" role="alert">{{ $message }}</span> @enderror
                @if ($publication->document)
                    <a class="text-link" href="{{ route('contenus.document', $publication) }}">Télécharger le document actuel</a>
                    <label class="form-check"><input type="checkbox" name="retirer_document" value="1" @checked(old('retirer_document'))> Supprimer le document actuel</label>
                @endif
            </div>

            <div class="form-actions">
                <a class="button button-secondary" href="{{ route('contenus.show', $publication) }}">Annuler</a>
                <button class="button button-primary" type="submit">Enregistrer les modifications</button>
            </div>
        </form>

        <aside class="form-aside">
            <div class="side-panel">
                <span class="side-panel-icon" aria-hidden="true">M</span>
                <h2>Votre contenu reste le vôtre.</h2>
                <p>Les remplacements sont enregistrés proprement et les anciens fichiers sont retirés après la mise à jour.</p>
            </div>
            <div class="privacy-note">Les documents privés ne sont accessibles qu'à leur auteur et aux administrateurs.</div>
        </aside>
    </div>
@endsection

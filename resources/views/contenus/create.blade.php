@extends('layouts.app')

@section('titre', 'Créer une publication')

@section('contenu')
    <section class="page-heading">
        <div>
            <p class="eyebrow">Espace créateur</p>
            <h1>Créer une publication</h1>
            <p class="page-intro">Donnez un titre à votre idée, puis choisissez le média qui l'accompagne.</p>
        </div>
    </section>

    <div class="form-layout">
        <form class="form-panel" method="POST" action="{{ route('contenus.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-section-heading">
                <span class="form-step">01</span>
                <div>
                    <h2>Les informations</h2>
                    <p>Un titre clair aide les autres à trouver votre publication.</p>
                </div>
            </div>

            <div class="form-field">
                <label for="titre">Titre</label>
                <input class="form-control" id="titre" type="text" name="titre" value="{{ old('titre') }}" minlength="10" maxlength="255" placeholder="Ex. Les coulisses du studio" required>
                <p class="form-help">10 caractères minimum, 255 maximum.</p>
                @error('titre') <span class="form-error" role="alert">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="categorie_id">Catégorie</label>
                <select class="form-control" id="categorie_id" name="categorie_id" required>
                    <option value="">Choisir une catégorie</option>
                    @foreach ($categories as $categorie)
                        <option value="{{ $categorie->id }}" @selected(old('categorie_id') == $categorie->id)>
                            {{ $categorie->nom }}
                        </option>
                    @endforeach
                </select>
                @error('categorie_id') <span class="form-error" role="alert">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="contenu">Votre texte</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="6" minlength="20" placeholder="Racontez ce qu'il faut savoir..." required>{{ old('contenu') }}</textarea>
                <p class="form-help">20 caractères minimum.</p>
                @error('contenu') <span class="form-error" role="alert">{{ $message }}</span> @enderror
            </div>

            <div class="form-section-heading">
                <span class="form-step">02</span>
                <div>
                    <h2>Ajouter un média</h2>
                    <p>Choisissez une image ou un fichier audio/vidéo.</p>
                </div>
            </div>

            <div class="form-field">
                <label for="image">Image de couverture <span class="form-help">(facultative)</span></label>
                <input class="file-control" id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,.bmp,image/jpeg,image/png,image/webp,image/gif,image/bmp">
                <p class="form-help">JPG, PNG, WEBP, GIF ou BMP — 2 Mo maximum.</p>
                @error('image') <span class="form-error" role="alert">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="fichier_media">Audio ou vidéo <span class="form-help">(facultatif)</span></label>
                <input class="file-control" id="fichier_media" type="file" name="fichier_media" accept=".mp3,.mp4,.wav,audio/mpeg,audio/wav,video/mp4">
                <p class="form-help">MP3, MP4 ou WAV — 20 Mo maximum.</p>
                @error('fichier_media') <span class="form-error" role="alert">{{ $message }}</span> @enderror
            </div>

            <div class="notice-inline"><strong>À retenir</strong><span>Une publication peut avoir une image ou un média audio/vidéo, mais pas les deux à la fois.</span></div>

            <div class="form-field">
                <label for="document">Document privé <span class="form-help">(facultatif)</span></label>
                <input class="file-control" id="document" type="file" name="document" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                <p class="form-help">PDF ou DOCX — 5 Mo maximum. Seuls vous et l'administration pourrez le télécharger.</p>
                @error('document') <span class="form-error" role="alert">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <a class="button button-secondary" href="{{ route('contenus.index') }}">Annuler</a>
                <button class="button button-primary" type="submit">Publier le contenu</button>
            </div>
        </form>

        <aside class="form-aside">
            <div class="side-panel">
                <span class="side-panel-icon" aria-hidden="true">M</span>
                <h2>Une publication, un point de vue.</h2>
                <p>Un titre précis et quelques lignes de contexte suffisent souvent à donner envie de découvrir la suite.</p>
                <ul>
                    <li>Choisissez une catégorie pertinente</li>
                    <li>Ajoutez un seul média public</li>
                    <li>Gardez vos documents sensibles privés</li>
                </ul>
            </div>
            <div class="privacy-note">Les fichiers sont contrôlés avant l'enregistrement. Les documents privés ne sont jamais exposés dans les liens publics.</div>
        </aside>
    </div>
@endsection
@extends('layouts.app')

@section('titre', 'Modifier une publication')

@section('contenu')
    <h1>Modifier la publication</h1>

    <form method="POST" action="{{ route('contenus.update', $publication) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 10px;">
            <label>Titre :</label><br>
            <input type="text" name="titre" value="{{ old('titre', $publication->titre) }}" style="width: 100%;">
            @error('titre') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 10px;">
            <label>Catégorie :</label><br>
            <select name="categorie_id" style="width: 100%;">
                @foreach($categories as $categorie)
                    <option value="{{ $categorie->id }}" @selected(old('categorie_id', $publication->categorie_id) == $categorie->id)>
                        {{ $categorie->nom }}
                    </option>
                @endforeach
            </select>
            @error('categorie_id') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 10px;">
            <label>Contenu :</label><br>
            <textarea name="contenu" rows="5" style="width: 100%;">{{ old('contenu', $publication->contenu) }}</textarea>
            @error('contenu') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 10px;">
            <label>Nouvelle image :</label><br>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,.bmp,image/jpeg,image/png,image/webp,image/gif,image/bmp">
            @error('image') <span style="color:red;">{{ $message }}</span> @enderror
            @if ($publication->image)
                <label><input type="checkbox" name="retirer_image" value="1" @checked(old('retirer_image'))> Supprimer l’image actuelle</label>
            @endif
        </div>

        <div style="margin-bottom: 10px;">
            <label>Nouveau fichier audio ou vidéo :</label><br>
            <input type="file" name="fichier_media" accept="audio/*,video/*">
            @error('fichier_media') <span style="color:red;">{{ $message }}</span> @enderror
            @if ($publication->fichier_media)
                <label><input type="checkbox" name="retirer_fichier_media" value="1" @checked(old('retirer_fichier_media'))> Supprimer le média actuel</label>
            @endif
        </div>

        <p>La publication ne peut conserver qu’une image ou un fichier audio/vidéo.</p>

        <div style="margin-bottom: 10px;">
            <label>Nouveau document privé PDF ou DOCX :</label><br>
            <input type="file" name="document" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
            @error('document') <span style="color:red;">{{ $message }}</span> @enderror
            @if ($publication->document)
                <p><a href="{{ route('contenus.document', $publication) }}">Télécharger le document actuel</a></p>
                <label><input type="checkbox" name="retirer_document" value="1" @checked(old('retirer_document'))> Supprimer le document actuel</label>
            @endif
        </div>

        <button type="submit" style="padding: 10px; background-color: #3490dc; color: white; border: none; border-radius: 5px;">
            Mettre à jour
        </button>
        <a href="{{ route('contenus.show', $publication) }}">Annuler</a>
    </form>
@endsection

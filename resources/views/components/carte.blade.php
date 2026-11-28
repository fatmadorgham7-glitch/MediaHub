<article class="carte {{ $miseEnAvant ? 'carte-vedette' : '' }}">
    <div @class([
        'carte-media',
        'carte-media-audio' => ! $image && $typeMedia === 'audio',
        'carte-media-video' => ! $image && $typeMedia === 'video',
        'carte-media-placeholder' => ! $image && ! $typeMedia,
    ])>
        @if ($image)
            <img src="{{ Storage::url($image) }}" alt="{{ $titre }}">
        @endif

        <span class="media-type">
            @if ($image)
                Image
            @elseif ($typeMedia === 'audio')
                Audio
            @elseif ($typeMedia === 'video')
                Vidéo
            @else
                Publication
            @endif
        </span>
    </div>

    <div class="carte-corps">
        <h2>{{ $titre }}</h2>
        @if ($typeMedia === 'audio' && $fichierMedia)
            <audio controls class="audio-player" src="{{ Storage::url($fichierMedia) }}"></audio>
        @elseif ($typeMedia === 'video' && $fichierMedia)
            <video class="video-player" controls src="{{ Storage::url($fichierMedia) }}"></video>
        @endif
        <div class="carte-slot">{{ $slot }}</div>
    </div>
</article>
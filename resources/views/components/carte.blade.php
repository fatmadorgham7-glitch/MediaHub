<div class="carte {{ $miseEnAvant ? 'carte-vedette' : '' }}">
    <h2>{{ $titre }}</h2>
    @if ($image)
        <img src="{{ Storage::url($image) }}" alt="{{ $titre }}" style="max-width:100%;">
    @endif
    @if ($typeMedia === 'audio' && $fichierMedia)
        <audio controls src="{{ Storage::url($fichierMedia) }}"></audio>
    @elseif ($typeMedia === 'video' && $fichierMedia)
        <video controls src="{{ Storage::url($fichierMedia) }}" style="max-width:100%;"></video>
    @endif
    <div class="carte-corps">{{ $slot }}</div>
</div>
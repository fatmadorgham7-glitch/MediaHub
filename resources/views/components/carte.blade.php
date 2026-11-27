<div class="carte {{ $miseEnAvant ? 'carte-vedette' : '' }}">
    <h2>{{ $titre }}</h2>
    <div class="carte-corps">{{ $slot }}</div>
</div>
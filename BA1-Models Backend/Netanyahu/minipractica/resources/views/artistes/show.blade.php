@extends('layouts.festival')

@section('titol', $artista->nom)

@section('contingut')
<h1>{{ $artista->nom }}</h1>

<a href="{{ route('artistes.index') }}">Tornar</a> |
<a href="{{ route('artistes.edit', $artista->id) }}">Editar</a>
<br><br>

<table border="1">
    <tr><th>ID</th><td>{{ $artista->id }}</td></tr>
    <tr><th>Nom</th><td>{{ $artista->nom }}</td></tr>
    <tr><th>País</th><td>{{ $artista->pais }}</td></tr>
    <tr><th>Bio</th><td>{{ $artista->bio }}</td></tr>
    <tr><th>Imatge</th><td>{{ $artista->imatge_url }}</td></tr>
    <tr><th>Actiu</th><td>{{ $artista->actiu ? 'Sí' : 'No' }}</td></tr>
    <tr><th>Creat</th><td>{{ $artista->created_at }}</td></tr>
    <tr><th>Actualitzat</th><td>{{ $artista->updated_at }}</td></tr>
</table>

<!-- LA RELACION AL REVES: desde el artista veo sus conciertos -->
<!-- $artista->concerts llama a la funcion concerts() del modelo (hasMany) -->
@if ($artista->concerts->count() > 0)
    <h3>Concerts programats</h3>
    <ul>
    @foreach ($artista->concerts as $concert)
        <!-- format() funciona porque en el modelo data_hora tiene el cast a datetime -->
        <li>{{ $concert->nom }} -- {{ $concert->data_hora->format('d/m/Y H:i') }}</li>
    @endforeach
    </ul>
@else
    <p>Encara no té concerts programats.</p>
@endif
@endsection

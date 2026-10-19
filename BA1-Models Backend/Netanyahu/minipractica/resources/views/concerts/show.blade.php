@extends('layouts.festival')

@section('titol', $concert->nom)

@section('contingut')
<h1>{{ $concert->nom }}</h1>

<a href="{{ route('concerts.index') }}">Tornar</a>
<br><br>

<table border="1">
    <tr><th>ID</th><td>{{ $concert->id }}</td></tr>
    <tr><th>Nom</th><td>{{ $concert->nom }}</td></tr>
    <tr><th>Data i hora</th><td>{{ $concert->data_hora->format('d/m/Y H:i') }}</td></tr>
    <tr><th>Durada</th><td>{{ $concert->durada_minuts }} min</td></tr>
    <tr><th>Tipus</th><td>{{ $concert->tipus }}</td></tr>
</table>

<!-- LA INFO DEL ARTISTA, gracias a la relacion -->
<h3>Artista</h3>
<table border="1">
    <tr><th>Nom</th><td><a href="{{ route('artistes.show', $concert->artista->id) }}">{{ $concert->artista->nom }}</a></td></tr>
    <tr><th>País</th><td>{{ $concert->artista->pais }}</td></tr>
    <tr><th>Bio</th><td>{{ $concert->artista->bio }}</td></tr>
</table>
@endsection

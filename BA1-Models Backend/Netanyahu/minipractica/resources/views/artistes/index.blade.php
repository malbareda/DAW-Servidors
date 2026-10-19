@extends('layouts.festival')

@section('titol', 'Artistes')

@section('contingut')
<h1>Artistes</h1>

<a href="{{ route('artistes.create') }}">Nou artista</a>
<br><br>

<table border="1">
    <tr>
        <th>Nom</th>
        <th>País</th>
        <th>Actiu</th>
        <th>Accions</th>
    </tr>
    @foreach($artistes as $artista)
        <tr>
            <td>{{ $artista->nom }}</td>
            <td>{{ $artista->pais }}</td>
            <!-- si es actiu le pongo la clase 'actiu' (verde), si no 'inactiu' (gris) -->
            <td class="{{ $artista->actiu ? 'actiu' : 'inactiu' }}">
                {{ $artista->actiu ? 'Sí' : 'No' }}
            </td>
            <td>
                <!-- le paso el artista a la ruta para que sepa a cual ir -->
                <a href="{{ route('artistes.show', $artista->id) }}">Veure</a>
                <a href="{{ route('artistes.edit', $artista->id) }}">Editar</a>
            </td>
        </tr>
    @endforeach
</table>
@endsection

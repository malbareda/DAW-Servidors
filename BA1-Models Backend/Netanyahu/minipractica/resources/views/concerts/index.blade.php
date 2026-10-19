@extends('layouts.festival')

@section('titol', 'Concerts')

@section('contingut')
<h1>Concerts</h1>

<a href="{{ route('concerts.create') }}">Nou concert</a>
<br><br>

<table border="1">
    <tr>
        <th>Nom</th>
        <th>Artista</th>
        <th>Data i hora</th>
        <th>Tipus</th>
        <th>Durada</th>
        <th>Accions</th>
    </tr>
    @foreach($concerts as $concert)
        <tr>
            <td>{{ $concert->nom }}</td>
            <!-- $concert->artista llama a la funcion artista() del modelo (belongsTo) -->
            <!-- no hace consultas extra porque en el controlador hemos hecho with('artista') -->
            <td><a href="{{ route('artistes.show', $concert->artista->id) }}">{{ $concert->artista->nom }}</a></td>
            <td>{{ $concert->data_hora->format('d/m/Y H:i') }}</td>
            <td>{{ $concert->tipus }}</td>
            <td>{{ $concert->durada_minuts }} min</td>
            <td><a href="{{ route('concerts.show', $concert->id) }}">Veure</a></td>
        </tr>
    @endforeach
</table>
@endsection

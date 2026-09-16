<!DOCTYPE html>
<html lang="ca">

<body>
<h1>Editar Pel·lícula: {{ $film->titol }}</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<form action="/films/edit/{{ $film->id }}" method="POST">
    @csrf

    <label for="titol">Títol:</label><br>
    {{-- igual que l'anterior però aquí el value es el valor que ja teniem. Es un copypaste en un 80% --}}
    <input type="text" name="titol" id="titol" value="{{$film->titol}}"><br>

    <label for="genere">Gènere:</label><br>
    <select name="genere" id="genere">
        <option value="ACCIO"   {{$film->genere == 'ACCIO'   ? 'selected' : '' }}>Acció</option>
        <option value="COMEDIA" {{$film->genere == 'COMEDIA' ? 'selected' : '' }}>Comèdia</option>
        <option value="DRAMA"   {{$film->genere == 'DRAMA'   ? 'selected' : '' }}>Drama</option>
    </select><br>


    <label for="durada">Durada (minuts):</label><br>
    <input type="number" name="durada" id="durada" value="{{$film->durada }}" min="1"><br>

    <br>
    <button type="submit">Send</button>
</form>
</body>
</html>

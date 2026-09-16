<!DOCTYPE html>
<html lang="ca">
<body>

<h1>Crear nova Pel·lícula</h1>

{{-- Totalment innecessari, pero si ho copieu a l'examen us sera mes facil debugar --}}
@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<form action="/films/create" method="POST">
    @csrf
    <label for="titol">Títol:</label><br>
    <input type="text" name="titol" id="titol"}}"><br>
    <label for="genere">Gènere:</label><br>
    <select name="genere" id="genere">

        <option value="ACCIO" >Acció</option>
        <option value="COMEDIA">Comèdia</option>
        <option value="DRAMA">Drama</option>
    </select><br>
    <label for="durada">Durada (minuts):</label><br>
    <input type="number" name="durada" id="durada" min="1"><br>

    <br>
    <button type="submit">Send</button>
</form>
</body>
</html>

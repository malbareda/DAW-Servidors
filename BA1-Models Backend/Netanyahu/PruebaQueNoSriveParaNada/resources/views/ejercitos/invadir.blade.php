<!DOCTYPE html>
<html>
<head>
    <title>Invadir - {{ $ejercito->nombre }}</title>
</head>
<body>
<h1>{{ $ejercito->nombre }} vol envair una ciutat</h1>

<a href="{{ route('ejercitos.index') }}">Tornar</a>

<form action="{{ route('ejercitos.invadido', $ejercito) }}" method="POST">
    @csrf
    <p>
        <label>Selecciona la ciutat:</label><br>
        <select name="ciudad_id">
            @foreach($ciudades as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
            @endforeach
        </select>
    </p>
    <p>
        <button type="submit">Envair!</button>
    </p>
</form>


</body>
</html>

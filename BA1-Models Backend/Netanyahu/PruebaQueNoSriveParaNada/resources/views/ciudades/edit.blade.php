<!DOCTYPE html>
<html>
<head>
    <title>Editar {{ $ciudad->nombre }}</title>
</head>
<body>
<h1>Editar {{ $ciudad->nombre }}</h1>

<a href="{{ route('ciudades.index') }}">Tornar</a>

<form action="{{ route('ciudades.update', $ciudad->id) }}" method="POST">
    @csrf
    @method('PUT')

    <p>
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="{{ $ciudad->nombre }}">
    </p>
    <p>
        <label>Habitantes:</label><br>
        <input type="number" name="habitantes" value="{{ $ciudad->habitantes }}">
    </p>
    <p>
        <label>País:</label><br>
        <input type="text" name="pais" value="{{ $ciudad->pais }}">
    </p>
    <p>
        <label>Superficie:</label><br>
        <input type="number" step="0.01" name="superficie" value="{{ $ciudad->superficie }}">
    </p>
    <p>
        <label>Alcalde:</label><br>
        <input type="text" name="alcalde" value="{{ $ciudad->alcalde }}">
    </p>
    <p>
        <label>Estado:</label><br>
        <input type="text" name="estado" value="{{ $ciudad->estado }}">
    </p>
    <p>
        <label>
            <input type="checkbox" name="amigo" value="1" {{ $ciudad->amigo ? 'checked' : '' }}>
            Amigo
        </label>
    </p>
    <p>
        <button type="submit">Guardar</button>
    </p>
</form>
</body>
</html>

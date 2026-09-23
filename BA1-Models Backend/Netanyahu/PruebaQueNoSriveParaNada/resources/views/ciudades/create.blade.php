<!DOCTYPE html>
<html>
<head>
    <title>Nova ciutat</title>
</head>
<body>
<h1>Nova ciutat</h1>

<a href="{{ route('ciudades.index') }}">Tornar</a>

@if($errors->any())
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form action="{{ route('ciudades.store') }}" method="POST">
    @csrf

    <p>
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="{{ old('nombre') }}">
    </p>
    <p>
        <label>Habitantes:</label><br>
        <input type="number" name="habitantes" value="{{ old('habitantes') }}">
    </p>
    <p>
        <label>País:</label><br>
        <input type="text" name="pais" value="{{ old('pais') }}">
    </p>
    <p>
        <label>Superficie:</label><br>
        <input type="number" step="0.01" name="superficie" value="{{ old('superficie') }}">
    </p>
    <p>
        <label>Alcalde:</label><br>
        <input type="text" name="alcalde" value="{{ old('alcalde') }}">
    </p>
    <p>
        <label>Estat:</label><br>
        <select name="estat">
            <option value="perfecte" {{ old('estat') == 'perfecte' ? 'selected' : '' }}>Perfecte</option>
            <option value="destruit" {{ old('estat') == 'destruit' ? 'selected' : '' }}>Destruit</option>
            <option value="en_guerra" {{ old('estat') == 'en_guerra' ? 'selected' : '' }}>En guerra</option>
            <option value="abandonat" {{ old('estat') == 'abandonat' ? 'selected' : '' }}>Abandonat</option>
        </select>
    </p>
    <p>
        <label>
            <input type="checkbox" name="amic" value="1" {{ old('amic') ? 'checked' : '' }}>
            Amic
        </label>
    </p>
    <p>
        <button type="submit">Crear</button>
    </p>
</form>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Nova ciutat</title>
</head>
<body>
<h1>Nova ciutat</h1>

<a href="{{ route('ciudades.index') }}">Tornar</a>

<!-- Action te lleva a una ruta con POST casi siempre -->
<!-- route es el nombre que le has puesto a la ruta tu mismo en web.php -->
<form action="{{ route('ciudades.store') }}" method="POST">
    <!-- esto  hay que ponerlo siempre-->
    <!-- habeis oido hablar de cors -->
    @csrf

    <p>
        <label>Nombre:</label><br>
        <input type="text" name="nombre">
    </p>
    <p>
        <label>Habitantes:</label><br>
        <input type="number" name="habitantes">
    </p>
    <p>
        <label>País:</label><br>
        <input type="text" name="pais">
    </p>
    <p>
        <label>Superficie:</label><br>
        <input type="number" step="0.01" name="superficie">
    </p>
    <p>
        <label>Alcalde:</label><br>
        <input type="text" name="alcalde">
    </p>
    <p>
        <label>Estat:</label><br>
        <select name="estado">
            <option value="perfecto">Perfecte</option>
            <option value="destruido">Destruit</option>
            <option value="en_guerra">En guerra</option>
            <option value="abandonado">Abandonat</option>
            <option value="bombardeable">Bombardeable</option>
            <option value="colonizable">Colonizable</option>

        </select>
    </p>
    <p>
        <label>
            <input type="checkbox" name="amigo" value="1">
            Amic
        </label>
    </p>
    <p>
        <button type="submit">Crear</button>
    </p>
</form>
</body>
</html>

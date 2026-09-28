<!DOCTYPE html>
<html>
<head>
    <title>{{ $ciudad->nombre }}</title>
</head>
<body>
<h1>{{ $ciudad->nombre }}</h1>

<a href="{{ route('ciudades.index') }}">Tornar</a>

<table border="1">
    <tr><th>ID</th><td>{{ $ciudad->id }}</td></tr>
    <tr><th>Nombre</th><td>{{ $ciudad->nombre }}</td></tr>
    <tr><th>Habitantes</th><td>{{ $ciudad->habitantes }}</td></tr>
    <tr><th>País</th><td>{{ $ciudad->pais }}</td></tr>
    <tr><th>Superficie</th><td>{{ $ciudad->superficie }}</td></tr>
    <tr><th>Alcalde</th><td>{{ $ciudad->alcalde }}</td></tr>
    <tr><th>Amigo</th><td>{{ $ciudad->amigo ? 'Sí' : 'No' }}</td></tr>
    <tr><th>Estado</th><td>{{ $ciudad->estado }}</td></tr>
</table>
</body>
</html>

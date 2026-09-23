<!DOCTYPE html>
<html>
<head>
    <title>Ciudades</title>
</head>
<body>
<h1>Ciudades</h1>



<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Habitantes</th>
        <th>País</th>
        <th>Superficie</th>
        <th>Alcalde</th>
        <th>Amic</th>
        <th>Estat</th>
    </tr>
    @foreach($ciudades as $lechota)
        <tr>
            <td>{{ $lechota->id }}</td>
            <td>{{ $lechota->nombre }}</td>
            <td>{{ $lechota->habitantes }}</td>
            <td>{{ $lechota->pais }}</td>
            <td>{{ $lechota->superficie }}</td>
            <td>{{ $lechota->alcalde }}</td>
            <td>{{ $lechota->amigo ? 'Sí' : 'No' }}</td>
            <td>{{ $lechota->estado }}</td>
        </tr>
    @endforeach
</table>
</body>
</html>

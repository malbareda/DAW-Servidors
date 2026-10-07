<!DOCTYPE html>
<html>
<head>
    <title>Exèrcits</title>
</head>
<body>
<h1>Exèrcits</h1>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Soldats</th>
        <th>Ciutat</th>
        <th>Accions</th>
    </tr>
    @foreach($ejercitos as $ejercito)
        <tr>
            <td>{{ $ejercito->id }}</td>
            <td>{{ $ejercito->nombre }}</td>
            <td>{{ $ejercito->soldados }}</td>
            <td>
                {{ $ejercito->ciudad->nombre }}
            </td>
        </tr>
    @endforeach
</table>
</body>
</html>

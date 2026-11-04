<table border="1">
    <tr>
        <th>ID</th>
        <th>Titol</th>
        <th>Categoria</th>
    </tr>
    @foreach($llibres as $llibre)
        <tr>
            <td>{{ $llibre->id }}</td>
            <td>{{ $llibre->titol }}</td>
            <td>{{ $llibre->categoria }}</td>
        </tr>
    @endforeach
</table>

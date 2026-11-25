<table border="1">
    <tr>
        <th>Nom</th>
        <th>Edat</th>
        <th>Mida</th>
        <th>Propietari</th>
        <th>Concursos</th>
    </tr>
    @foreach($ponis as $poni)
        <tr>
            <td>{{ $poni->nom }}</td>
            <td>{{ $poni->edat }}</td>
            <td>{{ $poni->mida }}</td>
            <td>{{ $poni->user ? $poni->user->name : "De l'hípica" }}</td>
            <!-- concursos_count el crea el withCount del controlador -->
            <td>{{ $poni->concursos_count }}</td>
        </tr>
    @endforeach
</table>

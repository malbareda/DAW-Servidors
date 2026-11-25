<table border="1">
    <tr>
        <th>Nom</th>
        <th>Edat</th>
        <th>Mida</th>
        <th>Propietari</th>
    </tr>
    @foreach($ponis as $poni)
        <tr>
            <td>{{ $poni->nom }}</td>
            <td>{{ $poni->edat }}</td>
            <td>{{ $poni->mida }}</td>
            <!-- si no te propietari: el de l'hipica -->
            <td>{{ $poni->user ? $poni->user->name : "De l'hípica" }}</td>
        </tr>
    @endforeach
</table>

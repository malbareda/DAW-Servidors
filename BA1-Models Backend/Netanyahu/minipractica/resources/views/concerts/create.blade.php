@extends('layouts.festival')

@section('titol', 'Nou concert')

@section('contingut')
<h1>Nou concert</h1>

<a href="{{ route('concerts.index') }}">Tornar</a>

<!-- Action te lleva a una ruta con POST casi siempre -->
<!-- route es el nombre que le has puesto a la ruta tu mismo en web.php -->
<form action="{{ route('concerts.store') }}" method="POST">
    @csrf

    <p>
        <label>Artista:</label><br>
        <!-- DESPLEGABLE DE ARTISTAS: el value es el id (lo que se guarda), y se ve el nombre -->
        <select name="artista_id">
            @foreach ($artistes as $artista)
                <!-- si la validacion falla, dejo seleccionado el que habia elegido -->
                <option value="{{ $artista->id }}" {{ old('artista_id') == $artista->id ? 'selected' : '' }}>{{ $artista->nom }}</option>
            @endforeach
        </select>
        @error('artista_id')
            <br><span class="error">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <label>Nom del concert:</label><br>
        <input type="text" name="nom" value="{{ old('nom') }}">
        @error('nom')
            <br><span class="error">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <label>Data i hora:</label><br>
        <!-- datetime-local saca un calendario con hora -->
        <input type="datetime-local" name="data_hora" value="{{ old('data_hora') }}">
        <!-- AQUI SALEN 2 TIPOS DE ERROR: los de validate() (required, date)
             y el del back()->withErrors(['data_hora' => ...]) del controller (artista ocupado) -->
        @error('data_hora')
            <br><span class="error">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <label>Durada (minuts):</label><br>
        <input type="number" name="durada_minuts" value="{{ old('durada_minuts') }}">
        @error('durada_minuts')
            <br><span class="error">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <label>Tipus:</label><br>
        <!-- LOS MISMOS VALORES QUE EL ENUM DE LA MIGRACION -->
        <select name="tipus">
            @foreach (['CONCERT', 'DJ_SET', 'SHOWCASE', 'ACUSTIC'] as $t)
                <option value="{{ $t }}" {{ old('tipus') == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        @error('tipus')
            <br><span class="error">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <button type="submit">Crear</button>
    </p>
</form>
@endsection

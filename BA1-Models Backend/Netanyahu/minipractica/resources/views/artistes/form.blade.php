@extends('layouts.festival')

@section('titol', isset($artista) ? 'Editar artista' : 'Nou artista')

@section('contingut')
<!-- UN SOLO FORMULARIO PARA CREAR Y EDITAR -->
<!-- isset($artista) es true si vengo del edit (le paso artista) y false si vengo del create -->
<h1>{{ isset($artista) ? 'Editar ' . $artista->nom : 'Nou artista' }}</h1>

<a href="{{ route('artistes.index') }}">Tornar</a>

<!-- si edito voy al update (con el artista), si creo voy al store -->
<form action="{{ isset($artista) ? route('artistes.update', $artista->id) : route('artistes.store') }}" method="POST">
    <!-- esto hay que ponerlo siempre -->
    @csrf

    <!-- los formularios html solo saben hacer GET y POST -->
    <!-- para el update (PUT) laravel hace este truco -->
    @if (isset($artista))
        @method('PUT')
    @endif

    <!-- old('nom', ...) pone lo que el usuario habia escrito si la validacion falla -->
    <!-- si no hay nada, pone lo segundo: el valor del artista (o nada si es nuevo) -->
    <p>
        <label>Nom:</label><br>
        <input type="text" name="nom" value="{{ old('nom', $artista->nom ?? '') }}">
        <!-- error() solo se pinta si ese campo ha fallado en la validacion, $message es el texto del error -->
        @error('nom')
            <br><span class="error">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <label>País:</label><br>
        <input type="text" name="pais" value="{{ old('pais', $artista->pais ?? '') }}">
        @error('pais')
            <br><span class="error">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <label>Bio:</label><br>
        <!-- el textarea no tiene value, el texto va entre las etiquetas -->
        <textarea name="bio" rows="4" cols="40">{{ old('bio', $artista->bio ?? '') }}</textarea>
        @error('bio')
            <br><span class="error">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <label>URL de la imatge:</label><br>
        <input type="text" name="imatge_url" value="{{ old('imatge_url', $artista->imatge_url ?? '') }}">
        @error('imatge_url')
            <br><span class="error">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <!-- EL TRUCO DEL CHECKBOX -->
        <!-- si el checkbox no esta marcado, el navegador NO ENVIA NADA -->
        <!-- por eso pongo antes un hidden con el mismo nombre y valor 0 -->
        <!-- si se marca, el checkbox (1) machaca al hidden (0) porque va despues -->
        <!-- (otra forma es hacer $request->has('actiu') en el controlador, como en ciudades) -->
        <input type="hidden" name="actiu" value="0">
        <label>
            <!-- si es nuevo, por defecto marcado (true) -->
            <input type="checkbox" name="actiu" value="1" {{ old('actiu', $artista->actiu ?? true) ? 'checked' : '' }}>
            Actiu (confirmat al festival)
        </label>
        @error('actiu')
            <br><span class="error">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <button type="submit">{{ isset($artista) ? 'Guardar' : 'Crear' }}</button>
    </p>
</form>
@endsection

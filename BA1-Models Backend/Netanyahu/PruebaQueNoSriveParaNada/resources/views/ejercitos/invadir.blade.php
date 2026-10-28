<!DOCTYPE html>
<html>
<head>
    <title>Invadir - {{ $ejercito->nombre }}</title>
</head>
<body>
<h1>{{ $ejercito->nombre }} vol envair una ciutat</h1>

<a href="{{ route('ejercitos.index') }}">Tornar</a>

<p>Soldats: {{ $ejercito->soldados }}</p>

<form action="{{ route('ejercitos.invadido', $ejercito) }}" method="POST">
    @csrf

    <!-- este error no es de ningun input, es el 'soldados' que me invento en el controller -->
    @error('soldados')
        <p style="color: red;">{{ $message }}</p>
    @enderror

    <p>
        <label>Selecciona la ciutat:</label><br>
        <select name="ciudad_id">
            @foreach($ciudades as $c)
                <!-- si vuelve con error, dejo seleccionada la que habia elegido (gracias al withInput) -->
                <option value="{{ $c->id }}" {{ old('ciudad_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->nombre }} ({{ $c->estado }}{{ $c->amigo ? ', amiga' : '' }})
                </option>
            @endforeach
        </select>
        <!-- $message es el texto que he puesto en el withErrors(['ciudad_id' => '...']) -->
        @error('ciudad_id')
            <br><span style="color: orange;">{{ $message }}</span>
        @enderror
    </p>
    <p>
        <button type="submit">Envair!</button>
    </p>
</form>


</body>
</html>


<form action="{{ route('llibres.update',$llibre->id) }}" method="POST">
    <!-- esto hay que ponerlo siempre -->
@csrf

<p>
    <label>Titol:</label><br>
    <!-- value: el valor actual del llibre -->
    <input type="text" name="titol" value="{{ $llibre->titol }}">
</p>
<p>
    <label>Categoria:</label><br>
    <!-- LOS MISMOS VALORES QUE EL ENUM DE LA MIGRACION -->
    <!-- dejo seleccionada la categoria que tiene ahora el llibre -->
    <select name="categoria">
        <option value="FICCIO" {{ $llibre->categoria == 'FICCIO' ? 'selected' : '' }}>FICCIO</option>
        <option value="NOFICCIO" {{ $llibre->categoria == 'NOFICCIO' ? 'selected' : '' }}>NOFICCIO</option>
    </select>
</p>
<p>
    <button type="submit">Send</button>
</p>
</form>



<form action="{{ route('llibres.store') }}" method="POST">
    <!-- esto hay que ponerlo siempre -->
@csrf

<p>
    <label>Titol:</label><br>
    <input type="text" name="titol">
</p>
<p>
    <label>Categoria:</label><br>
    <!-- LOS MISMOS VALORES QUE EL ENUM DE LA MIGRACION -->
    <select name="categoria">
        <option value="FICCIO">FICCIO</option>
        <option value="NOFICCIO">NOFICCIO</option>
    </select>
</p>
<p>
    <button type="submit">Send</button>
</p>
</form>


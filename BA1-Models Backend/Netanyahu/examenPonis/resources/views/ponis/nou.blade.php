
<form action="{{ route('ponis.store') }}" method="POST">
    <!-- aixo s'ha de posar sempre -->
@csrf

<p>
    <label>Nom:</label><br>
    <input type="text" name="nom">
</p>
<p>
    <label>Edat:</label><br>
    <input type="number" name="edat">
</p>
<p>
    <label>Mida:</label><br>
    <!-- ELS MATEIXOS VALORS QUE L'ENUM DE LA MIGRACIO -->
    <select name="mida">
        <option value="PETIT">PETIT</option>
        <option value="MITJA">MITJA</option>
        <option value="GRAN">GRAN</option>
    </select>
</p>
<p>
    <button type="submit">Send</button>
</p>
</form>

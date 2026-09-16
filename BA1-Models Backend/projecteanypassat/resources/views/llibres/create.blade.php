<div>
    <!-- If you do not have a consistent goal in life, you can not live it in a consistent way. - Marcus Aurelius -->
    <h1>Create Llibre</h1>
    <form action="{{route("llibres.store")}}" method="POST">
        @csrf
        <input type="text" id="titol" name="titol">
        <select id="categoria" name="categoria">
            <option value="FICCIO">Ficcio</option>
            <option value="NOFICCIO">NoFiccio</option>
        </select>
        <button type="submit">Enviar</button>



    </form>
</div>

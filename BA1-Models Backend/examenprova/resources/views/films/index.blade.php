{{-- resources/views/films/list.blade.php --}}

@foreach($films as $film)
    <p>
        {{-- en l'examen no us molesteu a fer html maco, no cal posar ni paragrafs ni strong ni res --}}
        <strong>{{ $film->titol }}</strong>
        - {{ $film->genere }} - {{ $film->durada }} min
    </p>
@endforeach

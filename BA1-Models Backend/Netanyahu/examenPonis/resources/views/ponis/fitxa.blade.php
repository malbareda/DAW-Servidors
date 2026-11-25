<h1>{{ $poni->nom }}</h1>
<p>Edat: {{ $poni->edat }}</p>
<p>Mida: {{ $poni->mida }}</p>
<p>Propietari: {{ $poni->user ? $poni->user->name : "De l'hípica" }}</p>

<!DOCTYPE html>
<html>
<head>
    <!-- yield pinta lo que cada vista ponga en su section('titol') -->
    <title>SonarFest - @yield('titol')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; }
        th, td { padding: 5px 10px; }
        .actiu { color: green; font-weight: bold; }
        .inactiu { color: gray; }
        .error { color: red; }
    </style>
</head>
<body>

<!-- ESTO ES EL LAYOUT: lo que se repite en todas las paginas -->
<!-- las vistas hacen extends('layouts.festival') y solo ponen su contenido -->
<p>
    <a href="{{ route('artistes.index') }}">Artistes</a> |
    <a href="{{ route('concerts.index') }}">Concerts</a> |
    <!-- auth()->user() es el usuario que esta logueado -->
    {{ auth()->user()->name }}
</p>

<!-- el logout tiene que ser POST, por eso es un formulario y no un enlace -->
<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">Tancar sessió</button>
</form>

<hr>

<!-- AQUI VA EL CONTENIDO DE CADA VISTA -->
@yield('contingut')

</body>
</html>

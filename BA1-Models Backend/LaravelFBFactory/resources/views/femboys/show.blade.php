@extends('layouts.app')

@section('title', 'Detalls del Femboy')

@section('content')
    <div class="container">
        <h1>Detalles del Femboy</h1>

        <div class="card">
            <div class="card-body">
                <table class="table" style="width: auto;">
                    <tr>
                        <th>ID:</th>
                        <td>{{ $femboy->id }}</td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ $femboy->nombre }}</td>
                    </tr>
                    <tr>
                        <th>Edad:</th>
                        <td>{{ $femboy->edad }} años</td>
                    </tr>
                    <tr>
                        <th>Procedencia:</th>
                        <td>{{ $femboy->procedencia }}</td>
                    </tr>
                    <tr>
                        <th>Descripción:</th>
                        <td>{{ $femboy->descripcion ?? 'Sin descripción' }}</td>
                    </tr>
                    <tr>
                        <th>Fecha Cumpleaños:</th>
                        <td>{{ $femboy->fecha_cumpleanos->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            {{-- Amb enum pots accedir directament als mètodes --}}
                            <span class="badge bg-{{ $femboy->estado->color() }}">
                                {{ $femboy->estado->emoji() }} {{ $femboy->estado->value }}
                            </span>

                            {{-- També pots comprovar l'estat amb els mètodes del model --}}
                            @if($femboy->isDisponible())
                                <small class="text-success">✓ Disponible per reserva</small>
                            @elseif($femboy->isReservado())
                                <small class="text-warning">⏳ Actualment reservat</small>
                            @else
                                <small class="text-danger">✗ No disponible</small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Factory:</th>
                        <td>{{ $femboy->factory->nombre }}</td>
                    </tr>
                    <tr>
                        <th>Creado:</th>
                        <td>{{ $femboy->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Actualizado:</th>
                        <td>{{ $femboy->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>

                <div class="mt-3">
                    <a href="{{ route('femboys.edit', $femboy) }}" class="btn btn-warning">Editar</a>
                    <a href="{{ route('femboys.all') }}" class="btn btn-secondary">Volver al listado</a>
                    <a href="{{ route('femboys.destroy', $femboy) }}" class="btn btn-danger"
                       onclick="return confirm('¿Estás seguro de eliminar este femboy?')">
                        Eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

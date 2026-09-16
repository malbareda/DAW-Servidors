@extends('layouts.app')

@section('title', 'Crear Femboy')

@section('content')
    <div class="container">
        <h1>Crear Nou Femboy</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('femboys.store') }}" method="POST">
            @csrf

            <div class="form-group mb-3">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="edad">Edad:</label>
                <input type="number" name="edad" id="edad" class="form-control" min="18" value="{{ old('edad') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="procedencia">Procedencia:</label>
                <input type="text" name="procedencia" id="procedencia" class="form-control" value="{{ old('procedencia') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" class="form-control">{{ old('descripcion') }}</textarea>
            </div>

            <div class="form-group mb-3">
                <label for="fecha_cumpleanos">Fecha Cumpleaños:</label>
                <input type="date" name="fecha_cumpleanos" id="fecha_cumpleanos" class="form-control" value="{{ old('fecha_cumpleanos') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="estado">Estado:</label>
                <select name="estado" id="estado" class="form-select" required>
                    <option value="">Selecciona un estado</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->value }}" {{ old('estado') == $estado->value ? 'selected' : '' }}>
                            {{ $estado->emoji() }} {{ $estado->value }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    Estados disponibles:
                    @foreach($estados as $estado)
                        <span class="badge bg-{{ $estado->color() }}">{{ $estado->value }}</span>
                    @endforeach
                </small>
            </div>

            <div class="form-group mb-3">
                <label for="factory_id">Factory:</label>
                <select name="factory_id" id="factory_id" class="form-select" required>
                    <option value="">Selecciona una factory</option>
                    @foreach($factories as $factory)
                        <option value="{{ $factory->id }}" {{ old('factory_id') == $factory->id ? 'selected' : '' }}>
                            {{ $factory->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-success">Crear Femboy</button>
                <a href="{{ route('femboys.all') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

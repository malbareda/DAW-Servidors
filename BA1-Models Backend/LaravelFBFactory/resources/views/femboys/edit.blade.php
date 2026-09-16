<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Femboy: {{ $femboy->nombre }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Editar Femboy: {{ $femboy->nombre }}</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('femboys.update', $femboy) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $femboy->nombre) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="edad">Edad:</label>
            <input type="number" name="edad" id="edad" class="form-control" min="18" max="100" value="{{ old('edad', $femboy->edad) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="procedencia">Procedencia:</label>
            <input type="text" name="procedencia" id="procedencia" class="form-control" value="{{ old('procedencia', $femboy->procedencia) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="form-control" required>{{ old('descripcion', $femboy->descripcion) }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label for="fecha_cumpleanos">Fecha Cumpleaños:</label>
            <input type="date" name="fecha_cumpleanos" id="fecha_cumpleanos" class="form-control" value="{{ old('fecha_cumpleanos', $femboy->fecha_cumpleanos->format('Y-m-d')) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado" class="form-select" required>
                <option value="">Selecciona un estado</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado->value }}"
                        {{ old('estado', $femboy->estado->value) == $estado->value ? 'selected' : '' }}>
                        {{ $estado->emoji() }} {{ $estado->value }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">
                Estado actual: <span class="badge bg-{{ $femboy->estado->color() }}">{{ $femboy->estado->value }}</span>
            </small>
        </div>

        <div class="form-group mb-3">
            <label for="factory_id">Factory:</label>
            <select name="factory_id" id="factory_id" class="form-select" required>
                <option value="">Selecciona una factory</option>
                @foreach($factories as $factory)
                    <option value="{{ $factory->id }}" {{ old('factory_id', $femboy->factory_id) == $factory->id ? 'selected' : '' }}>
                        {{ $factory->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-success">Actualizar Femboy</button>
            <a href="{{ route('femboys.all') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>

{{-- resources/views/femboys/index.blade.php --}}

    <!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Llistat de Femboys</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .user-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            padding: 5px 10px;
            margin: 2px;
            text-decoration: none;
            border-radius: 3px;
            display: inline-block;
            font-size: 14px;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-secondary {
            background-color: #008CBA;
            color: white;
        }
        .btn-danger {
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn-warning {
            background-color: #ff9800;
            color: white;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #4CAF50;
            color: white;
        }
        .badge-warning {
            background-color: #ff9800;
            color: white;
        }
        .badge-danger {
            background-color: #f44336;
            color: white;
        }
        .info-box {
            margin: 20px 0;
            padding: 10px;
            background: #e3f2fd;
            border-radius: 5px;
            border-left: 4px solid #2196F3;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Llistat de Femboys</h1>

    <div class="user-info">
        <strong>Usuari:</strong> {{ auth()->user()->name }}<br>
        @can('esAdmin', App\Models\Femboy::class)
            <strong>Rol:</strong> Administrador
        @elsecan('esPropietari', App\Models\Femboy::class)
            <strong>Rol:</strong> Propietari de Factory<br>
            <strong>Factory:</strong> {{ auth()->user()->customer->factory->nombre }}
        @elsecan('esCustomer', App\Models\Femboy::class)
            <strong>Rol:</strong> Customer
        @endcan
    </div>
</div>

@can('create', App\Models\Femboy::class)
    <a href="{{ route('femboys.create') }}" class="btn btn-primary">Crear nou Femboy</a>
@endcan

<div class="info-box">
    @can('esAdmin', App\Models\Femboy::class)
        <strong>Vista Admin:</strong> Pots veure tots els femboys del sistema
    @elsecan('esPropietari', App\Models\Femboy::class)
        <strong>Vista Propietari:</strong> Pots veure els femboys de la teva factory ({{ auth()->user()->customer->factory->nombre }})
    @elsecan('esCustomer', App\Models\Femboy::class)
        <strong>Vista Customer:</strong> Pots veure només els teus femboys assignats
    @endcan
</div>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Edad</th>
        <th>Procedencia</th>
        <th>Estado</th>
        <th>Factory</th>
        <th>Descripción</th>
        <th>Accions</th>
    </tr>
    </thead>
    <tbody>
    @forelse($femboys as $femboy)
        {{-- Admin: veu tots els femboys --}}
        @can('viewAll', App\Models\Femboy::class)
            <tr>
                <td>{{ $femboy->id }}</td>
                <td>{{ $femboy->nombre }}</td>
                <td>{{ $femboy->edad }}</td>
                <td>{{ $femboy->procedencia }}</td>
                <td>
                    @if($femboy->estado === 'Disponible')
                        <span class="badge badge-success">{{ $femboy->estado }}</span>
                    @elseif($femboy->estado === 'Reservado')
                        <span class="badge badge-warning">{{ $femboy->estado }}</span>
                    @else
                        <span class="badge badge-danger">{{ $femboy->estado }}</span>
                    @endif
                </td>
                <td>{{ $femboy->factory->nombre }}</td>
                <td>{{ Str::limit($femboy->descripcion, 50) }}</td>
                <td>
                    @can('view', $femboy)
                        <a href="{{ route('femboys.show', $femboy) }}" class="btn btn-secondary">Veure</a>
                    @endcan

                    @can('update', $femboy)
                        <a href="{{ route('femboys.edit', $femboy) }}" class="btn btn-warning">Editar</a>
                    @endcan

                    @can('delete', $femboy)
                        <form action="{{ route('femboys.destroy', $femboy) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Estàs segur?')">
                                Eliminar
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
        @elsecan('viewFactory', $femboy)
            {{-- Propietari: només veu femboys de la seva factory --}}
            <tr>
                <td>{{ $femboy->id }}</td>
                <td>{{ $femboy->nombre }}</td>
                <td>{{ $femboy->edad }}</td>
                <td>{{ $femboy->procedencia }}</td>
                <td>
                    @if($femboy->estado === 'Disponible')
                        <span class="badge badge-success">{{ $femboy->estado }}</span>
                    @elseif($femboy->estado === 'Reservado')
                        <span class="badge badge-warning">{{ $femboy->estado }}</span>
                    @else
                        <span class="badge badge-danger">{{ $femboy->estado }}</span>
                    @endif
                </td>
                <td>{{ $femboy->factory->nombre }}</td>
                <td>{{ Str::limit($femboy->descripcion, 50) }}</td>
                <td>
                    @can('view', $femboy)
                        <a href="{{ route('femboys.show', $femboy) }}" class="btn btn-secondary">Veure</a>
                    @endcan

                    @can('update', $femboy)
                        <a href="{{ route('femboys.edit', $femboy) }}" class="btn btn-warning">Editar</a>
                    @endcan

                    @can('delete', $femboy)
                        <form action="{{ route('femboys.destroy', $femboy) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Estàs segur?')">
                                Eliminar
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
        @elsecan('viewOwn', $femboy)
            {{-- Customer: només veu els seus femboys assignats --}}
            <tr>
                <td>{{ $femboy->id }}</td>
                <td>{{ $femboy->nombre }}</td>
                <td>{{ $femboy->edad }}</td>
                <td>{{ $femboy->procedencia }}</td>
                <td>
                    @if($femboy->estado === 'Disponible')
                        <span class="badge badge-success">{{ $femboy->estado }}</span>
                    @elseif($femboy->estado === 'Reservado')
                        <span class="badge badge-warning">{{ $femboy->estado }}</span>
                    @else
                        <span class="badge badge-danger">{{ $femboy->estado }}</span>
                    @endif
                </td>
                <td>{{ $femboy->factory->nombre }}</td>
                <td>{{ Str::limit($femboy->descripcion, 50) }}</td>
                <td>
                    @can('view', $femboy)
                        <a href="{{ route('femboys.show', $femboy) }}" class="btn btn-secondary">Veure</a>
                    @endcan

                    @cannot('update', $femboy)
                        <span style="color: #999; font-size: 12px;">No editable</span>
                    @endcannot

                    @cannot('delete', $femboy)
                        <span style="color: #999; font-size: 12px;">No eliminable</span>
                    @endcannot
                </td>
            </tr>
        @endcan
    @empty
        <tr>
            <td colspan="8" class="empty-state">
                No hi ha femboys disponibles per mostrar.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

</body>
</html>

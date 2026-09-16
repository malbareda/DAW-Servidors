@extends('layouts.app')

@section('title', 'Els meus Femboys')

@section('content')
    <div class="container">
        <h1>Els meus Femboys</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($femboys && $femboys->count() > 0)
            <p class="lead">Tens {{ $femboys->count() }} femboy(s) associat(s)</p>

            <div class="row">
                @foreach($femboys as $femboy)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ $femboy->nombre }}</h5>
                                @if($femboy->estado == 'Disponible')
                                    <span class="badge bg-success">{{ $femboy->estado }}</span>
                                @elseif($femboy->estado == 'Reservado')
                                    <span class="badge bg-warning">{{ $femboy->estado }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $femboy->estado }}</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <p><strong>Edat:</strong> {{ $femboy->edad }} anys</p>
                                <p><strong>Procedència:</strong> {{ $femboy->procedencia }}</p>
                                <p><strong>Descripció:</strong> {{ Str::limit($femboy->descripcion, 100) }}</p>
                                <p><strong>Aniversari:</strong> {{ \Carbon\Carbon::parse($femboy->fecha_cumpleanos)->format('d/m/Y') }}</p>
                                <p><strong>Factory:</strong> {{ $femboy->factory->nombre }}</p>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('femboys.show', $femboy) }}" class="btn btn-primary btn-sm">Veure detalls</a>
                                <a href="{{ route('femboys.edit', $femboy) }}" class="btn btn-warning btn-sm">Editar</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <div class="alert alert-info">
                <h4>No tens cap femboy associat</h4>
                <p>Encara no tens cap femboy vinculat al teu compte de customer.</p>
                <a href="{{ route('femboys.all') }}" class="btn btn-primary">Veure tots els femboys</a>
            </div>
        @endif

        <div class="mt-4">

            <a href="{{ route('femboys.all') }}" class="btn btn-outline-primary">Veure tots els femboys</a>
        </div>
    </div>
@endsection

@can('create', App\Models\Llibre::class)
    <li><a href="{{ route('llibres.create') }}">Crear Llibre</a></li>
@endcan
@can('create', App\Models\Prestec::class)
    <li>Crear Prestec</li>
@endcan

@cannot('create', App\Models\Prestec::class)
    <li>SKILL ISSUE</li>
@endcannot
@cannot('create', App\Models\Llibre::class)
    <li>PAPAPAPAPAPAPAP</li>
@endcannot

yyyyyyyy

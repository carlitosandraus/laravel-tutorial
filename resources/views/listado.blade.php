@extends('web')
@section('content')
        
        <table class="table">
            <thead class="thead-dark">
                <th>Id</th>
                <th>Nombre</th>
                <th>Edad</th>
                <th>Opciones</th>
            </thead>
            <tbody class="table-striped">
                @foreach ($datos as $dato)
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->edad }}</td>
                    <td>
                        <button class="btn btn-info" onclick="window.location='/web/{{$dato->id}}';">Ver</button>
                        <button class="btn btn-danger" data-target="#modal-eliminar" data-toggle="modal" data-registro="{{$dato->id}}">Borrar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $datos->links() }}
        <button class="btn btn-success" onclick="window.location='/web/crear/dato';">Crear</button>
@endsection        
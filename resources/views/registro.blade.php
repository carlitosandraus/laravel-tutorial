@extends('web')
@section('content')
    <?php if($dato->id != null): ?>
    <form class="form" id="forma" method="POST" action="/web/{{$dato->id}}">
        <input name="_method" type="hidden" value="PUT">
    <?php endif; ?>
    <?php if(!$dato->id): ?>
    <form class="form" id="forma" method="POST" action="/web">
    <?php endif; ?>
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{$dato->nombre}}" />
        </div>
        <div class="form-group">
            <label for="edad">Edad</label>
            <input type="text" class="form-control" id="edad" name="edad" value="{{$dato->edad}}" />
        </div>
        <?php if($dato->id != null): ?>
        <div class="form-group">
            <label for="creado">Creado: {{$dato->created_at}} </label><br />
            <label for="actualizado">Actualizado: {{$dato->updated_at}} </label>
        </div>        
        <?php endif; ?>
        <input type="hidden" name="id" value="{{ $dato->id }}" />
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    </form>
    <br />
    <div class="btn-group">
        <button class="btn btn-info" onclick="window.location='/web';">Regresar</button>
        <button class="btn btn-success" onclick="document.forms.forma.submit()">Guardar</button>
        <button class="btn btn-danger" data-target="#modal-eliminar" data-toggle="modal" data-registro="{{$dato->id}}">Borrar</button>
    </div>
@endsection        
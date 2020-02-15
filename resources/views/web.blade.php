<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body>
        @yield('content')



        <div class="modal" tabindex="-1" role="dialog" id="modal-eliminar">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Realmente desea borrar el registro con id <span id="iddato"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="borrar()">Borrar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"  crossorigin="anonymous"></script>

        <script type="text/javascript">

            $('#modal-eliminar').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var idRegistro = button.data('registro');
                $("#iddato").html(idRegistro);           
            })

            function borrar() {
                let idDato = $("#iddato").html();  
                $.ajax({
                    url: '/web/'+idDato,
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    type: 'DELETE',
                    success: function(result) {
                        window.location="/web";
                    },
                    error: function(result) {
                        alert("Fallo!!!")
                    }
                });
            }
        </script>
    </body>
</html>
 /*
        Carga de archivos
    */
    function subirArchivos() {
          $("#archivo").upload('../managers/subir_archivo.php',
          {
             nombre_archivo: $("#nombre_archivo").val(),
             selector: $("#selector").val()
          },
          function(respuesta) {
             //Subida finalizada.
            $("#barra_de_progreso").val(0);
            if (respuesta == 1) {
                mostrarRespuesta('El archivo ha sido subido correctamente.', true);
                $("#nombre_archivo, #archivo").val('');
            }
            else {
                mostrarRespuesta(respuesta,false);
             }
             mostrarArchivos();
             }, function(progreso, valor) {
                //Barra de progreso.
                $("#barra_de_progreso").val(valor);
             });
       }
       
       /*
        Eliminar archivos
       */
       
       function eliminarArchivos(archivo) {
          $.ajax({
             url: '../managers/eliminar_archivo.php',
             type: 'POST',
             timeout: 10000,
             data: {archivo: archivo},
             error: function() {
                mostrarRespuesta('Error al intentar eliminar el archivo.', false);
             },
             success: function(respuesta) {
                if (respuesta == 1) {
                   mostrarRespuesta('El archivo ha sido eliminado.', true);
                } else {
                   mostrarRespuesta('Error al intentar eliminar el archivo.', false); 
                }
                mostrarArchivos();
             }
          });
       }
    
    /*
    Mostrar archivos
    */
    function mostrarArchivos() {
      $.ajax({
         url: '../managers/mostrar_archivos.php',
         dataType: 'JSON',
         success: function(respuesta) {
            if (respuesta) {
               var html = '';
               for (var i = 0; i < respuesta.length; i++) {
                  if (respuesta[i] != undefined) {
                     html += '<div class="row"> <span class="col-md-3"> ' + respuesta[i] + ' </span> <div class="col-md-2"> <a class="eliminar_archivo btn btn-danger " id="btn_eliminar" name="elim" href="javascript:void(0);"> Eliminar </a> </div> </div> <hr />';
                  }
               }
               $("#archivos_subidos").html(html);
            }
         }
      });
    }
    
    $(document).ready(function() {
    mostrarArchivos();
    $("#boton_subir").on('click', function() {
        subirArchivos();
    });
    $("#archivos_subidos").on('click', '.eliminar_archivo', function() {
    var archivo = $(this).parents('.row').eq(0).find('span').text();
        archivo = $.trim(archivo);
        eliminarArchivos(archivo);
    });
    $("#respuesta").on('click', '.continue', function() {
       setTimeout('document.location.reload()', 500);
    });
    });
    
    function mostrarRespuesta(mensaje, ok){
      $("#respuesta").removeClass('alert-success').removeClass('alert-danger').html(mensaje);
      if(ok){
         $("#respuesta").addClass('alert-success');
         setTimeout('document.location.reload()', 500);
      }else{
         $("#respuesta").addClass('alert-danger');
         var btn_continue = $('<br> <div class="row"><div class="col-md-2 col-md-offset-5"><a class="continue btn btn-danger" href="javascript:void(0);"> Continuar </a></div></div>');
         btn_continue.appendTo($("#respuesta"));
         
      }
    }
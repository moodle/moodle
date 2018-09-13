// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/dphpforms_form_editor
  */

  define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui','block_ases/select2'], function($, bootstrap, sweetalert, jqueryui, select2) {
    
    return {
        init: function() {

                function get_url_parameters(url){
                    var start_param_position = url.indexOf("?");
                    var params = "";
                    for(var i = start_param_position; i < url.length; i++){
                        params += url[i];
                    }
                    return params.replace(/#[a-zA-z]+_[a-zA-z]+/i, '');
                };

                $('#dphpforms-redirect-new-form').click(function(){
                    window.location.href = "dphpforms_form_builder.php" + get_url_parameters(window.location.href);
                });

                $('#dphpforms-redirect-adm-alias').click(function(){
                    window.location.href = "dphpforms_alias_editor.php" + get_url_parameters(window.location.href);
                });

                $('#dphpforms-redirect-adm-forms').click(function(){
                    window.location.href = "dphpforms_form_editor.php" + get_url_parameters(window.location.href);
                });

                $('#dphpforms-redirect-new-pregu').click(function(){
                    window.location.href = "dphpforms_form_creator_pregunta.php" + get_url_parameters(window.location.href) + '&form_id=' + $(this).attr('data-form-id');
                });

                $('#dphpforms-redirect-adm-disparadores').click(function(){
                    window.location.href = "dphpforms_form_editor_comportamientos.php" + get_url_parameters(window.location.href) + '&form_id=' + $(this).attr('data-form-id');
                });

                $('.btn-editor-form').click(function(){
                    window.location.href = "dphpforms_form_editor_preguntas.php" + get_url_parameters(window.location.href) + '&form_id=' + $(this).attr('data-form-id');
                });

                $('.btn-editor-permiso').click(function(){
                    window.location.href = "dphpforms_form_editor_permiso.php" + get_url_parameters(window.location.href) + '&permiso_id=' + $(this).attr('data-permiso-id');
                });

                $('.btn-editor-pregunta').click(function(){
                    window.location.href = "dphpforms_form_editor_pregunta.php" + get_url_parameters(window.location.href) + '&pregunta_id=' + $(this).attr('data-pregunta-id');
                });
                

                $('#actualizar-permiso').click(function(){
                    
                    var permiso_id = $(this).attr('data-permiso-id');
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está actualizando este permiso, ¿desea continuar?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Actualizar!'
                      }, function(isConfirm) {
                        if (isConfirm) {
                            

                            $.ajax({
                                method: "POST",
                                url: "../managers/dphpforms/dphpforms_form_updater.php",
                                contentType: "application/json",
                                dataType: "text",
                                data: JSON.stringify({"function":"update_permiso", "permiso_id":permiso_id, "permisos":$('#permisos').val()}) ,
                                success: function( msg ){
                                    msg = JSON.parse( msg );
                                    
                                    if( msg['status'] == 0 ){
                                        alert('Actualizado');

                                        
                                    }else if( msg['status'] == -1 ){
                                        /*swal(
                                            'Error',
                                            'Permiso vacio o inexistente',
                                            'error'
                                          );*/
                                          alert('Permiso vacio o inexistente');
                                    }
                                    
                                },
                                error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                    /*swal(
                                        'Error',
                                        'Informe de este error.',
                                        'error'
                                      );*/

                                      alert('Informe de este error');
                                    console.log( "some error " + textStatus + " " + errorThrown );
                                    console.log( XMLHttpRequest );
                                }
                            });

                        }
                    });
                    
                    
                    //window.location.href = "dphpforms_form_editor_permiso.php" + get_url_parameters(window.location.href) + '&permiso_id=' + $(this).attr('data-permiso-id');
                });

                $('#actualizar-enunciado').click(function(){
                    
                    var pregunta_id = $(this).attr('data-pregunta-id');
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está actualizando el enunciado, ¿desea continuar?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Actualizar!'
                      }, function(isConfirm) {
                        if (isConfirm) {
                            

                            $.ajax({
                                method: "POST",
                                url: "../managers/dphpforms/dphpforms_form_updater.php",
                                contentType: "application/json",
                                dataType: "text",
                                data: JSON.stringify({"function":"update_pregunta_enunciado", "pregunta_id":pregunta_id, "enunciado":$('#enunciado').val()}) ,
                                success: function( msg ){
                                    msg = JSON.parse( msg );
                                    
                                    if( msg['status'] == 0 ){
                                        alert('Actualizado');
                                        
                                    }else if( msg['status'] == -1 ){
                                          alert('Pregunta inexistente');
                                    }
                                    
                                },
                                error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                    alert('Informe de este error');
                                    console.log( "some error " + textStatus + " " + errorThrown );
                                    console.log( XMLHttpRequest );
                                }
                            });

                        }
                    });
                    
                });

                $('#actualizar-atributos').click(function(){
                    
                    var pregunta_id = $(this).attr('data-pregunta-id');
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está actualizando los atributos, ¿desea continuar?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Actualizar!'
                      }, function(isConfirm) {
                        if (isConfirm) {
                            

                            $.ajax({
                                method: "POST",
                                url: "../managers/dphpforms/dphpforms_form_updater.php",
                                contentType: "application/json",
                                dataType: "text",
                                data: JSON.stringify({"function":"update_pregunta_atributos", "pregunta_id":pregunta_id, "atributos":$('#atributos').val()}) ,
                                success: function( msg ){
                                    msg = JSON.parse( msg );
                                    
                                    if( msg['status'] == 0 ){
                                        alert('Actualizado');
                                        
                                    }else if( msg['status'] == -1 ){
                                          alert('Pregunta inexistente');
                                    }
                                    
                                },
                                error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                    alert('Informe de este error');
                                    console.log( "some error " + textStatus + " " + errorThrown );
                                    console.log( XMLHttpRequest );
                                }
                            });

                        }
                    });
                    
                });

                $('#actualizar-opciones').click(function(){
                    
                    var pregunta_id = $(this).attr('data-pregunta-id');
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está las opciones de esta pregunta, ¿desea continuar?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Actualizar!'
                      }, function(isConfirm) {
                        if (isConfirm) {
                            

                            $.ajax({
                                method: "POST",
                                url: "../managers/dphpforms/dphpforms_form_updater.php",
                                contentType: "application/json",
                                dataType: "text",
                                data: JSON.stringify({"function":"update_pregunta_opciones", "pregunta_id":pregunta_id, "opciones":$('#opciones').val()}) ,
                                success: function( msg ){
                                    msg = JSON.parse( msg );
                                    console.log(msg);
                                    if( msg['status'] == 0 ){
                                        alert('Actualizado');
                                        
                                    }else if( msg['status'] == -1 ){
                                          alert('Pregunta inexistente');
                                    }
                                    
                                },
                                error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                    alert('Informe de este error');
                                    console.log( "some error " + textStatus + " " + errorThrown );
                                    console.log( XMLHttpRequest );
                                }
                            });

                        }
                    });
                    
                });

                $('#registrar-pregunta').click(function(){
                    
                    var form_id = $(this).attr('data-form-id');
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está registrando una nueva pregunta en el formulario, ¿desea continuar?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Crear!'
                      }, function(isConfirm) {
                        if (isConfirm) {
                            

                            $.ajax({
                                method: "POST",
                                url: "../managers/dphpforms/dphpforms_form_updater.php",
                                contentType: "application/json",
                                dataType: "text",
                                data: JSON.stringify({"function":"create_pregunta", "form_id":form_id, "json_pregunta":$('#json_pregunta').val()}) ,
                                success: function( msg ){
                                    msg = JSON.parse( msg );
                                    console.log( msg );
                                    if( msg['status'] == 0 ){
                                        alert('Creada');
                                        $('#json_pregunta').val('');
                                        
                                    }else if( msg['status'] == -1 ){
                                          alert('Formulario inexistente o JSON-pregunta vacio');
                                    }
                                    
                                },
                                error: function( XMLHttpRequest, textStatus, errorThrown ) {

                                    alert('Informe de este error');
                                    console.log( "some error " + textStatus + " " + errorThrown );
                                    console.log( XMLHttpRequest );
                                }
                            });

                        }
                    });
                    
                });

                $('#actualizar-orden').click(function(){
                    
                    var form_id = $(this).attr('data-form-id');
                    console.log( 'FormID: ' + form_id );
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está actualizando el orden de las pregunta, ¿desea continuar?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Actualizar!'
                      }, function(isConfirm) {
                        if (isConfirm) {
                            

                            $.ajax({
                                method: "POST",
                                url: "../managers/dphpforms/dphpforms_form_updater.php",
                                contentType: "application/json",
                                dataType: "text",
                                data: JSON.stringify({"function":"update_positions", "form_id":form_id, "ordenamiento":$('#ordenamiento').val()}) ,
                                success: function( msg ){
                                    //msg = JSON.parse( msg );
                                    console.log(msg);
                                    /*if( msg['status'] == 0 ){
                                        alert('Actualizado');
                                        
                                    }else if( msg['status'] == -1 ){
                                          alert('Error');
                                    }*/
                                    
                                },
                                error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                    alert('Informe de este error');
                                    console.log( "some error " + textStatus + " " + errorThrown );
                                    console.log( XMLHttpRequest );
                                }
                            });

                        }
                    });
                    
                });

                $('.btn-remove-form').click(function(){
                    var form_name = $(this).attr('data-form-name');
                    var form_id = $(this).attr('data-form-id');
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está eliminando el formulario <strong><i>" + form_name + "</i></strong>, ¿desea continuar?, tenga en consideración que los alias asociados a las preguntas del formulario no serán eliminados.",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Eliminar!'
                      }, function(isConfirm) {
                        if (isConfirm) {

                            $.get( "../managers/dphpforms/dphpforms_form_updater.php?function=delete_form&id_form=" + form_id, function( data ) {
                                var response = data;
                                if(response['status'] == 0){
                                    swal(
                                        {title:'Información',
                                        text: 'Eliminado',
                                        type: 'success'},
                                        function(){
                                            window.location.href = window.location.href;
                                        }
                                    );
                                }else if(response['status'] == -1){
                                    swal(
                                        'Error!',
                                        response['message'],
                                        'error'
                                    );
                                }
                            });

                        }
                    });
                });

                $('.btn-remove-alias').click(function(){
                    var alias = $(this).attr('data-form-alias');
                    var alias_id = $(this).attr('data-form-id');
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está eliminando el alias <strong><i>" + alias + "</i></strong>, ¿desea continuar?, tenga en consideración que cualquier consulta que haga uso de este alias dejará de funcionar.",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Eliminar!'
                      }, function(isConfirm) {
                        if (isConfirm) {

                            $.get( "../managers/dphpforms/dphpforms_form_updater.php?function=delete_alias&id_alias=" + alias_id, function( data ) {
                                var response = data;
                                if(response['status'] == 0){
                                    swal(
                                        {title:'Información',
                                        text: 'Eliminado',
                                        type: 'success'},
                                        function(){
                                            window.location.href = window.location.href;
                                        }
                                    );
                                }else if(response['status'] == -1){
                                    swal(
                                        'Error!',
                                        response['message'],
                                        'error'
                                    );
                                }
                            });

                        }
                    });
                });

                $('#actualizar-disparadores').click(function(){
                    
                    var disparadores_id = $(this).attr('data-disparadores-id');
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está actualizando este disparador de comportamientos, ¿desea continuar?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Actualizar!'
                      }, function(isConfirm) {
                        if (isConfirm) {
                            

                            $.ajax({
                                method: "POST",
                                url: "../managers/dphpforms/dphpforms_form_updater.php",
                                contentType: "application/json",
                                dataType: "text",
                                data: JSON.stringify({"function":"update_disparadores", "disparadores_id":disparadores_id, "disparadores":$( '#disparadores-' + disparadores_id ).val()}) ,
                                success: function( msg ){
                                    msg = JSON.parse( msg );
                                    
                                    if( msg['status'] == 0 ){
                                        alert('Actualizado');
                                        
                                    }else if( msg['status'] == -1 ){
                                          alert('Bloque de disparadores inexistente');
                                    }
                                    
                                },
                                error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                    alert('Informe de este error');
                                    console.log( "some error " + textStatus + " " + errorThrown );
                                    console.log( XMLHttpRequest );
                                }
                            });

                        }
                    });
                    
                });
            }
    };
      
});
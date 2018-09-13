 /**
 * Management - Upload, delete and display files
 * @module amd/src/uploaddata_main
 * @author Iader E. García Gómez
 * @copyright 2018 Iader E. García <iadergg@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/validator'], function ($, bootstrap, sweetalert, validator) {

    return {
        init: function () {

            $(document).ready(function () {
                mostrarArchivos();
                var form = document.getElementById('upload_data_form');
                $("#load_button").on('click', function () {
                    subir_archivos(form);
                });
                $("#archivos_subidos").on('click', '.eliminar_archivo', function () {
                    var archivo = $(this).parents('.row').eq(0).find('span').text();
                    archivo = $.trim(archivo);
                    eliminarArchivos(archivo);
                });
                $("#respuesta").on('click', '.continue', function () {
                    setTimeout('document.location.reload()', 500);
                });
            });

           
            /**
             * @method subir_archivos
             * @desc Upload files given a form to obtain the data
             * @param {DOM element} form 
             * @return {void}
             */
            function subir_archivos(form) {

                var status_bar = form.children[2].children[0].children[0];
                var span = status_bar.children[0];
                var goback_button = form.children[4].children[1];

                response_div = document.getElementById('response_div');
                response_span = document.getElementById('response_span');

                response_span.innerHTML = "";
                response_div.setAttribute('hidden', 'hidden');

                status_bar.classList.remove('green_bar', 'red_bar');

                //Ajax request
                var request = new XMLHttpRequest();

                // Progress bar

                request.upload.addEventListener("progress", (event) => {
                    let percent = Math.round((event.loaded / event.total) * 100);

                    status_bar.style.width = percent + '%';
                    span.innerHTML = percent + '%';

                });

                request.addEventListener("load", () => {
                    if (request.response == '1') {
                        status_bar.classList.add('green_bar');
                        span.innerHTML = "Éxito";
                        response_div.removeAttribute('hidden');
                        response_span.classList.add('response_success');
                        response_span.innerHTML += 'El contenido del archivo ha sido cargado con éxito en la base de datos.';
                    }
                    else {
                        status_bar.classList.add('red_bar');
                        span.innerHTML = "Error";
                        response_div.removeAttribute('hidden');
                        response_span.classList.add('response_error');
                        response_span.innerHTML = request.response;
                    }

                });

                //Upload files processing file at subir_archivo.php

                request.open('post', '../managers/upload_files_form/subir_archivo.php');

                request.send(new FormData(form));

                goback_button.addEventListener('click', () => {
                    request.abort();
                    status_bar.classList.remove('green_bar');
                    status_bar.classList.add('red_bar');
                    span.innerHTML = "Proceso cancelado";
                });
            }

            /**
             * @method eliminarArchivos
             * @desc Deletes a specific file through an ajax call
             * @param {file} archivo file to delete
             */
            function eliminarArchivos(archivo) {
                //Ajax call using processing at eliminar_archivo.php
                $.ajax({
                    url: '../managers/upload_files_form/eliminar_archivo.php',
                    type: 'POST',
                    timeout: 10000,
                    data: {
                        archivo: archivo
                    },
                    error: function () {
                        //In case something were missing
                        mostrarRespuesta('Error al intentar eliminar el archivo.', false);
                    },
                    success: function (respuesta) {
                        if (respuesta == 1) {
                            //File deleted
                            mostrarRespuesta('El archivo ha sido eliminado.', true);
                        }
                        else {
                            //Error during deleting
                            mostrarRespuesta('Error al intentar eliminar el archivo.', false);
                        }
                        mostrarArchivos();
                    }
                });
            }

            /**
             * @method mostrar_archivos
             * @desc Displays all files through an ajax call
             * @return {void}
             */
            function mostrarArchivos() {
                //Ajax call using processing at mostrar_archivos.php
                $.ajax({
                    url: '../managers/upload_files_form/mostrar_archivos.php',
                    dataType: 'JSON',
                    success: function (respuesta) {
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

            /**
             * @method mostrarRespuesta
             * @desc Displays a given message according an 'ok' flag
             * @param {string} mensaje message to show
             * @param {boolean} ok boolean flasg that indicates if it's  a success or danger alert (message) 
             */
            function mostrarRespuesta(mensaje, ok) {
                $("#respuesta").removeClass('alert-success').removeClass('alert-danger').html(mensaje);
                if (ok) {
                    $("#respuesta").addClass('alert-success');
                    setTimeout('document.location.reload()', 500);
                }
                else {
                    $("#respuesta").addClass('alert-danger');
                    var btn_continue = $('<br> <div class="row"><div class="col-md-2 col-md-offset-5"><a class="continue btn btn-danger" href="javascript:void(0);"> Continuar </a></div></div>');
                    btn_continue.appendTo($("#respuesta"));

                }
            }



        }
    };
});

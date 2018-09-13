/**
* mass management LOAD
* @module amd/src/massmanagement_main
* @author Jhon Lourido 
* @author Isabella Serna Ramírez
* @author Camilo José Cruz rivera
* @copyright 2018 - Jhon Lourido <jhonkrave@gmail.com>, Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>, Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co> 
* @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert'], function ($, bootstrap, sweetalert) {


    return {
        init: function () {

            var val = $('#selector').val();
            addHelpMessage(val);
            $('#selector').on('change', function () {
                var val = $('#selector').val();
                addHelpMessage(val);
            });

            $('#boton_subir').on('click', function () {
                $('#informacion').empty();
                uploadFile();
            });

            /**
             * @method getUrlParams
             * @desc This function is anonymous, is executed immediately and  the return value is assigned to QueryString!
             * @param {DOM element} page 
             * @return {query string}
             */
            function getUrlParams(page) {
                //
                var query_string = [];
                var query = document.location.search.substring(1);
                var vars = query.split("&");
                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split("=");
                    query_string[pair[0]] = pair[1];
                }

                return query_string;
            }

            /**
             * @method uploadFile
             * @desc uploads a specific file depending on a choose on a selector. Change information deployed on a div
             * @return {void}
             */
            function uploadFile() {

                var urlParameters = getUrlParams(document.location.search); //method defined on checkrole

                var formData = new FormData();

                formData.append('idinstancia', urlParameters.instanceid);

                //In case any file were uploaded
                if ($('#archivo')[0].files[0] == undefined) {
                    swal({
                        title: "Archivo no registrado.",
                        text: "Seleccione el archivo a subir",
                        html: true,
                        type: "error",
                        confirmButtonColor: "#d51b23"
                    });
                    var val = $('#selector').val();
                    addHelpMessage(val);
                    return;
                }

                formData.append('file', $('#archivo')[0].files[0]);

                var controler = '';

                //The selector has two options, monitor_estud or roles_usuario, depending on selection is redirected to a php file. 
                switch ($('#selector').val()) {
                    case 'monitor_estud':
                        controler = 'mrm_monitor_estud.php'; //
                        break;
                    case 'roles_usuario':
                        controler = 'mrm_roles.php'; //
                        break;
                    case 'status':
                        controler = 'mrm_status.php'; //
                        break;
                    default:
                        return 0;
                }

                $.ajax({
                    url: '../managers/mass_management/' + controler,
                    data: formData,
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    // required parameters to upload files
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('#response').html("<img src='../icon/facebook.gif' />");
                    },
                    success: function (msj) {

                        $('#response').empty();

                        $('#informacion').empty();

                        if (msj.success) {
                            $('#informacion').append('<div class="alert alert-success"><h4 align="center">Información</h4><strong>Exito!</strong> <br><p>' + msj.success + '</p></div>');
                        } else if (msj.warning) {
                            $('#informacion').append('<div class="alert alert-warning"><h4 align="center">Información</h4><strong>Cargado con inconsitencias!</strong> <br>' + msj.warning + '</div>');
                        } else if (msj.error) {
                            $('#informacion').append('<div class="alert alert-danger"><h4 align="center">Información</h4><strong>Error!</strong> <br>' + msj.error + '</div>');
                        }

                        $('#informacion').append(msj.urlzip);
                    },
                    error: function (msj) {
                        alert("error ajax");
                        $('#response').html("");
                        var val = $('#selector').val();
                        addHelpMessage(val);
                    }
                    // ... Other options like success and etc
                });

            }

            /**
             * @method addHelpMessage
             * @desc Changes the information deployed on a div depending on the choose on the previous selector
             * @param {DOM element} selector 
             */
            function addHelpMessage(selector) {
                $('#informacion').empty();
                switch (selector) {
                    case 'monitor_estud':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información Asignacion</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username_monitor</li><li>username_estudiante</li> </ul> </p></div>');
                        break;
                    case 'roles_usuario':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información Roles</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username</li><li>rol(administrativo, reportes,profesional_ps, monitor_ps,  estudiante_t ó practicante_psp)</li> </ul> </p><p>Columnas extras aceptadas: <ul> <li>jefe</li>  </ul> </p></div>');
                        break;
                    case 'status':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información estados</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username</li> <li>estado_ases</li> <li>estado_icetex</li> <li>estado_programa</li><li>tracking_status</li> <li>motivo_ases(puede ir en blanco)</li> <li>motivo_icetex(puede ir en blanco)</li> </ul> </p></div>');
                        break;
                    default:
                    // code
                }
            }


        }
    };
});
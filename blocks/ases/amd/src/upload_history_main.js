// Standard license block omitted.
/** @autor      Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_ases/upload_history_main
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert'], function ($, bootstrap, sweetalert) {


    return {
        init: function () {


            addHelpMessage();
            $('#selector').on('change', function () {
                addHelpMessage();
            });

            $('#boton_subir').on('click', function () {
                $('#informacion').empty();
                uploadFile();
            });


            function uploadFile() {

                var formData = new FormData();

                if ($('#archivo')[0].files[0] == undefined) {
                    swal({
                        title: "Archivo no registrado.",
                        text: "Seleccione el archivo a subir",
                        html: true,
                        type: "error",
                        confirmButtonColor: "#d51b23"
                    });
                    addHelpMessage();
                    return;
                }

                formData.append('file', $('#archivo')[0].files[0]);

                var controler = $('#selector').val() + '_processing.php';

                $.ajax({
                    url: '../managers/historic_management/' + controler,
                    data: formData,
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    //parametros necesarios para la carga de archivos
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('#response').html("<img src='../icon/facebook.gif' />");
                    },
                    success: function (msj) {
                        swal({
                            title: "Exito",
                            text: "Archivo Cargado. Por favor consulte los detalles",
                            html: true,
                            type: "warning",
                            showCancelButton: false,
                            showConfirmButton: false,
                            timer: 1300
                        });
                        
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
                        console.log(msj);
                        swal({
                            title: "Error en conexion al servidor.",
                            text: "No se ha podido establecer conexion al servidor",
                            html: true,
                            type: "error",
                            confirmButtonColor: "#d51b23"
                        });
                        $('#response').html("");
                        addHelpMessage();
                    }
                    //... Other options like success and etc
                });

            }
            function addHelpMessage() {
                var selector = $('#selector').val();
                $('#informacion').empty();
                switch (selector) {
                    case 'academic':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información de carga histórico académico</h4><br><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>codigo_estudiante</li> <li>semestre</li> <li>programa</li> <li>promedio_semestre</li> <li>promedio_acumulado</li> </ul> </p><p>Columnas extras aceptadas: <ul> <li>numero_bajo</li> <li>puesto_estimulo</li> <li>fecha_cancelacion</li> </ul> </p></div>');
                        break;
                    case 'materias':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información de carga materias</h4><br><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>codigo_estudiante</li> <li>semestre</li> <li>programa</li> <li>nombre_materia</li> <li>codigo_materia</li> <li>creditos</li> <li>nota</li> <li>fecha_cancelacion_materia</li> </ul> </p></div>');
                        break;
                    case 'icetex':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información de carga histórico ICETEX</h4><br> <strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul><li>cedula_estudiante</li><li>programa</li><li>codigo_resolucion</li> <li>monto_estudiante</li></ul> </p></div>');
                        break;
                    case 'resolution':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información de carga resolución ICETEX</h4><br> <strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul><li>codigo_resolucion</li><li>nombre_semestre</li><li>fecha</li><li>total_girado</li></ul></p></div>');
                        break;
                    default:
                    // code
                }
            }


        }
    };
});
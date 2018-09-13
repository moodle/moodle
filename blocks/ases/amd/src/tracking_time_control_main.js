// Standard license block omitted.
/*
 * @package    block_ases/tracking_time_control_main
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/tracking_time_control_main
 */
define(['jquery', 'block_ases/bootstrap', 'block_ases/datatables', 'block_ases/sweetalert', 'block_ases/select2', 'block_ases/jqueryui', 'block_ases/moment'], function($, bootstrap, datatablesnet, sweetalert, select2, jqueryui, moment) {


    return {

        init: function() {

            $(document).ready(function() {
                $('#table_hours').DataTable();
                $(".period_date").datepicker({
                    dateFormat: "yy-mm-dd"
                });

                load_hours_report();

                $("#consult").click(function() {

                    var beginningDate = $("#beginning_date").val();
                    var endingDate = $("#ending_date").val();

                    var result_validation = validateFields(beginningDate, endingDate);
                    if (result_validation != "success") {
                        swal({
                            title: "Advertencia",
                            text: result_validation,
                            type: "warning",
                            html: true
                        });
                    } else {
                        load_hours_report(beginningDate, endingDate);
                    }
                });
            });


            //*Perform date format validation.
            function validateFields(beginningDate, endingDate) {

                var regexp = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;

                var validate_begin_date = regexp.exec(beginningDate);
                var validate_end_date = regexp.exec(endingDate);

                if (beginningDate == "" && endingDate == "") {
                    return "Debe llenar todos los campos";
                } else if (beginningDate == "" || endingDate == "") {
                    return "Debe introducir la fecha de inicio y fin del período";
                } else if (validate_begin_date === null) {
                    return "La fecha de inicio no sigue el patrón yyyy-mm-dd. Ejemplo: 2017-10-20";
                } else if (validate_end_date === null) {
                    return "La fecha de fin no sigue el patrón yyyy-mm-dd. Ejemplo: 2017-10-20";
                } else if (beginningDate == endingDate) {
                    return "La fecha de inicio y de fin deben ser diferentes";
                } else {
                    return "success";
                }

            }


            function SumarColumna(grilla, columna) {

                var resultVal = 0.0;
                $("#" + grilla + " tbody tr").not(':first').each(
                    function() {

                        var celdaValor = $(this).find('td:eq(' + columna + ')');

                        if (celdaValor.val() != null)
                            resultVal += parseFloat(celdaValor.html().replace(',', '.'));

                    } //function

                ) //each
                $("#" + grilla + " tfoot tr").find('td:eq(' + columna + ')').html(resultVal.toFixed(2).toString().replace('.', ','));

            }

            //*Create the hour report table
            function load_hours_report(init, fin){

                var url      = window.location.href;     // Returns full URL
                var monitorid = url.split('monitorid=');


                if(init === undefined){
                    init = 0;
                }

                if(fin === undefined){
                    fin = 0;
                }


                $.ajax({
                    type: "POST",
                    url: "../managers/tracking_time_control/load_hours_report.php",
                    data: {
                        initial_hour: init,
                        final_hour: fin,
                        monitorid: monitorid[1] 
                    },

                    success: function(msg) {
                        if (msg == '') {
                            $("#div_hours").empty();
                            $("#div_hours").append('<h2>No existen registros de seguimientos en el dia de hoy</h2>');
                        } else {
                            $("#div_hours").empty();
                            $("#div_hours").append('<table id="tableHours"  class="display" cellspacing="0" width="100%" ><thead><thead><tfoot id="hours_foot"></tfoot></table>');
                            var table = $("#tableHours").DataTable(msg);
                            $("#hours_foot").append(' <tr><td><strong>Total : </strong></td><td id="total_hour"></td><td></td></tr>');
                            SumarColumna('tableHours', 1);
                            SumarColumna('tableHours', 2);
                            $('#div_hours #show_details').css('cursor', 'pointer');
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cargar horas trabajadas")
                    },
                })
            }
        }
    };
});
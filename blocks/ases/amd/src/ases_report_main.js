// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/ases_report_main
 */
define(['jquery',
    'block_ases/jszip',
    'block_ases/pdfmake',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.autoFill',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print',
    'block_ases/bootstrap',
    'block_ases/sweetalert2'
],
    function ($, jszip, pdfmake, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert) {
        return {
            init: function () {

                window.JSZip = jszip;
                //Control para el botón 'Generar Reporte
                $("#send_form_btn").on('click', function () {
                    
                    createTable();

                    var cohorts = $('#conditions').val();

                    if(cohorts == 'TODOS'){
                        $('#div-summary-spp').prop('hidden', false);
                        $('#div-summary-spe').prop('hidden', false);
                        $('#div-summary-3740').prop('hidden', false);
                        $('#div-summary-oa').prop('hidden', false);
                    }else if(cohorts == 'TODOS-SPP' || cohorts.substring(0, 3) == 'SPP'){
                        $('#div-summary-spp').prop('hidden', false);
                        $('#div-summary-spe').prop('hidden', true);
                        $('#div-summary-3740').prop('hidden', true);
                        $('#div-summary-oa').prop('hidden', true);
                    }else if(cohorts == 'TODOS-SPE' || cohorts.substring(0, 3) == 'SPE'){
                        $('#div-summary-spp').prop('hidden', true);
                        $('#div-summary-spe').prop('hidden', false);
                        $('#div-summary-3740').prop('hidden', true);
                        $('#div-summary-oa').prop('hidden', true);
                    }else if(cohorts == 'TODOS-3740' || cohorts.substring(0, 4) == '3740'){
                        $('#div-summary-spp').prop('hidden', true);
                        $('#div-summary-spe').prop('hidden', true);
                        $('#div-summary-3740').prop('hidden', false);
                        $('#div-summary-oa').prop('hidden', true);
                    }else if(cohorts == 'TODOS-OTROS'){
                        $('#div-summary-spp').prop('hidden', true);
                        $('#div-summary-spe').prop('hidden', true);
                        $('#div-summary-3740').prop('hidden', true);
                        $('#div-summary-oa').prop('hidden', false);
                    }
                });

                //Controles para la tabla generada
                $(document).on('click', '#tableResult tbody tr td', function () {
                    var pagina = "student_profile.php";
                    var table = $("#tableResult").DataTable();
                    var colIndex = table.cell(this).index().column;

                    if (colIndex <= 2) {
                        $("#formulario").each(function () {
                            this.reset;
                        });
                        location.href = pagina + location.search + "&student_code=" + table.cell(table.row(this).index(), 0).data();
                    }
                });

                //Controles check all 
                $('#contact_fields_check').on('change', function () {
                    if ($('#contact_fields_check').prop('checked')) {
                        $("#contact_fields input[type='checkbox']").prop('checked', true);
                    } else {
                        $("#contact_fields input[type='checkbox']").prop('checked', false);
                    }
                });

                $('#status_fields_check').on('change', function () {
                    if ($('#status_fields_check').prop('checked')) {
                        $("input[name='status_fields[]']").prop('checked', true);
                    } else {
                        $("input[name='status_fields[]']").prop('checked', false);
                    }
                });

                $('#academic_fields_check').on('change', function () {
                    if ($('#academic_fields_check').prop('checked')) {
                        $("input[name='academic_fields[]']").prop('checked', true);
                    } else {
                        $("input[name='academic_fields[]']").prop('checked', false);
                    }
                });

                $('#risk_fields_check').on('change', function () {
                    if ($('#risk_fields_check').prop('checked')) {
                        $("input[name='risk_fields[]']").prop('checked', true);
                    } else {
                        $("input[name='risk_fields[]']").prop('checked', false);
                    }
                });

                $('#assignment_fields_check').on('change', function () {
                    if ($('#assignment_fields_check').prop('checked')) {
                        $("input[name='assignment_fields[]']").prop('checked', true);
                    } else {
                        $("input[name='assignment_fields[]']").prop('checked', false);
                    }
                });
                     
                
                //Filtros de riesgos.
                $(document).on('change', '.select_risk', function () {
                    var table = $("#tableResult").DataTable();
                    var colIndex = $(this).parent().index() + 1;
                    var selectedText = $(this).parent().find(":selected").text();
                    table.columns(colIndex - 1).search(this.value).draw();
                });

                //Filtros sobre asignaciones socioeducativas
                $(document).on('change', '.filter_assignments', function () {
                    var table = $("#tableResult").DataTable();
                    var colIndex = $(this).parent().index() + 1;
                    var selectedText = $(this).parent().find(":selected").text();
                    table.columns(colIndex - 1).search(this.value).draw();
                });
                
                //Filtros sobre estados 
                $(document).on('change', '.select_filter_statuses', function () {     
                    var table = $("#tableResult").DataTable();
                    var colIndex = $(this).parent().index() + 1;
                    var selectedText = $(this).parent().find(":selected").text();                               
                    table.columns(colIndex - 1).search(selectedText).draw();
                    $.fn.dataTable.ext.search.pop()
                });

                
                //Controles sobre el resumen de estudiantes
                $(document).on('click', '.summary-title', function(){

                    // Icono de la lista
                    var icon = $($(this).data('icon'));

                    if(icon.hasClass('glyphicon-chevron-right')){
                        icon.removeClass("glyphicon-chevron-right");
                        icon.addClass("glyphicon-chevron-down");
                    }else{
                        icon.addClass("glyphicon-chevron-right");
                        icon.removeClass("glyphicon-chevron-down");
                    }                   

                    var target = $($(this).data('target')); 
                    if(target.css('display') != "none"){
                        target.hide(300);
                    }else{
                        target.show(300);
                    }
                });

                $('.panel-heading-summary').on('click', function(){

                    var icon = $(this).find('.icon-group-cohort');

                    if(icon.hasClass('glyphicon-chevron-right')){
                        icon.removeClass("glyphicon-chevron-right");
                        icon.addClass("glyphicon-chevron-down");
                    }else{
                        icon.addClass("glyphicon-chevron-right");
                        icon.removeClass("glyphicon-chevron-down");
                    }

                    var target = $($(this).data('target')); 
                    if(target.css('display') != "none"){
                        target.hide(300);
                    }else{
                        target.show(300);
                    }

                });

                

            },
            load_defaults_students: function (data) {

                $("#div_table").html('');
                $("#div_table").fadeIn(1000).append('<table id="tableResult" class="stripe row-border order-column" cellspacing="0" width="100%"><thead> </thead></table>');

                $("#tableResult").DataTable(data);

            },
            create_table: function () {

            },
            get_id_instance: function () {
                var urlParameters = location.search.split('&');

                for (x in urlParameters) {
                    if (urlParameters[x].indexOf('instanceid') >= 0) {
                        var intanceparameter = urlParameters[x].split('=');
                        return intanceparameter[1];
                    }
                }
                return 0;
            }
        }



        // Creación de tabla general
        function createTable() {

            var dataString = $('#form_general_report').serializeArray();

            dataString.push({
                name: 'instance_id',
                value: getIdinstancia()
            });

            $("#div_table").html('<img class="icon-loading" src="../icon/loading.gif"/>');
            $.ajax({
                type: "POST",
                data: dataString,
                url: "../managers/ases_report/asesreport_server_processing.php",
                success: function (msg) {
                    $("#div_table").html('');
                    $("#div_table").fadeIn(1000).append('<table id="tableResult" class="display" cellspacing="0" width="100%"><thead> </thead></table>');

                    $("#tableResult").DataTable(msg);

                    $('#tableResult tr').each(function () {
                        $.each(this.cells, function () {
                            if ($(this).html() == 'Bajo') {
                                $(this).addClass('riesgo_bajo');
                            } else if ($(this).html() == 'Medio') {
                                $(this).addClass('riesgo_medio');
                            } else if ($(this).html() == 'Alto') {
                                $(this).addClass('riesgo_alto');
                            }
                        });
                    });

                    $('#tableResult').bind("DOMSubtreeModified", function () {
                        $('#tableResult tr').each(function () {
                            $.each(this.cells, function () {
                                if ($(this).html() == 'Bajo') {
                                    $(this).addClass('riesgo_bajo');
                                } else if ($(this).html() == 'Medio') {
                                    $(this).addClass('riesgo_medio');
                                } else if ($(this).html() == 'Alto') {
                                    $(this).addClass('riesgo_alto');
                                }
                            });
                        });
                    });
                },
                dataType: "json",
                cache: "false",
                error: function (msg) {
                    alert("Error al conectar con el servidor")
                },
            });
        }

        function getIdinstancia() {
            var urlParameters = location.search.split('&');

            for (x in urlParameters) {
                if (urlParameters[x].indexOf('instanceid') >= 0) {
                    var intanceparameter = urlParameters[x].split('=');
                    return intanceparameter[1];
                }
            }
            return 0;
        }
    })

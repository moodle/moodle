// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/dphpforms_reports
  */

define([
    'jquery', 
    'block_ases/jszip',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.autoFill',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print',
    'block_ases/bootstrap', 
    'block_ases/sweetalert', 
    'block_ases/jqueryui',
    'block_ases/select2'
], function($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2) {
    
    return {
        init: function() {

            window.JSZip = jszip;
            $("#dphpform_datatable").DataTable(
                { 
                    "bsort" : false,
                    "data": [
                        {
                            "op":55,
                            "casa": 0
                        },
                        {
                            "casa":5,
                            "op": 14
                        }
                    ],
                    "columns" : [
                        {
                            "title" : "t", 
                            "name" : "casita", 
                            "data" : "casa",
                        },
                        {
                            "title" : "op", 
                            "name" : "op", 
                            "data" : "op",
                        },
                    ],
                    "dom":"lifrtpB",
                    "buttons" : [
                        {
                            "extend" : "print",
                            "text" : 'Imprimir'
                        },{
                            "extend" : "csv",
                            "text" : 'CSV'
                        },{
                            "extend" : "excel",
                            "text" : 'Excel',
                            "className" : 'buttons-excel',
                            "filename" : 'Export excel',
                            "extension" : '.xls'
                        }   
                    ]
                }
            );
            var id_semester = null;

            function render_datatable( records ){
                console.log( records );
            };

            function is_in_array( array_, new_element ){
                for( var i = 0; i < array_.length; i++ ){
                    if( array_[i] == new_element ){
                        return true;
                    };
                };
                return false;
            };

            function custom_actions( report, form_type ){
                
                if( form_type == "seguimiento_pares" ){
                    
                    var report_size = report.length;
                    for( var x = 0; x < report_size; x++){

                        var pos_firstname = -1;
                        var pos_lastname = -1;

                        var id_estudiante = null;
                        var id_creado_por = null;

                        var deleted = false;

                        for( var y = 0; y < Object.keys(report[x]).length; y++ ){
                            if( report[x][y].respuesta == "-#$%-" ){
                                report[x][y].respuesta = "";
                            }
                            
                            if( 
                                report[x][y].local_alias == "puntuacion_riesgo_individual" ||
                                report[x][y].local_alias == "puntuacion_riesgo_familiar" ||
                                report[x][y].local_alias == "puntuacion_riesgo_academico" ||
                                report[x][y].local_alias == "puntuacion_riesgo_economico" ||
                                report[x][y].local_alias == "puntuacion_vida_uni"
                             ){
                                if( report[x][y].respuesta == "1" ){
                                    report[x][y].respuesta = "bajo"
                                }else if( report[x][y].respuesta == "2" ){
                                    report[x][y].respuesta = "medio"
                                }else if( report[x][y].respuesta == "3" ){
                                    report[x][y].respuesta = "alto"
                                }
                            }

                            if( report[x][y].local_alias == "revisado_profesional" ){
                                
                                if( report[x][y].respuesta == "0" ){
                                    report[x][y].respuesta = "marcado"
                                }else if( report[x][y].respuesta == "-1" ){
                                    report[x][y].respuesta = "no_marcado"
                                }

                            }
                            if( report[x][y].local_alias == "revisado_practicante" ){
                                
                                if( report[x][y].respuesta == "0" ){
                                    report[x][y].respuesta = "marcado"
                                }else if( report[x][y].respuesta == "-1" ){
                                    report[x][y].respuesta = "no_marcado"
                                }

                            }
                            if( !id_estudiante || !id_creado_por ){
                                if( report[x][y].local_alias == "id_estudiante" ){
                                    id_estudiante = report[x][y].respuesta;
                                }
                                if( report[x][y].local_alias == "id_creado_por" ){
                                    id_creado_por = report[x][y].respuesta;
                                }
                            }
                            
                        }

                        var instance_id = parseInt($("#dphpforms-instance-id").data("instance-id"));

                        $.ajax({
                            type: "POST",
                            url: "../managers/user_management/user_management_api.php",
                            data: JSON.stringify({ "function": "get_crea_stud_mon_prac_prof", "params": [ id_estudiante, id_creado_por, instance_id, id_semester ] }),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            async: false,  
                            success: function(data){

                                report[x][Object.keys(report[x]).length] = { 
                                    enunciado:"monitor_name", 
                                    id:"00", 
                                    local_alias:"monitor_name",
                                    respuesta: String(data.data_response.monitor.firstname) + " " + String(data.data_response.monitor.lastname)
                                };

                                report[x][Object.keys(report[x]).length] = { 
                                    enunciado:"practicing_name", 
                                    id:"00", 
                                    local_alias:"practicing_name",
                                    respuesta: String(data.data_response.practicing.firstname) + " " + String(data.data_response.practicing.lastname)
                                };

                                report[x][Object.keys(report[x]).length] = { 
                                    enunciado:"professional_name", 
                                    id:"00", 
                                    local_alias:"professional_name",
                                    respuesta: String(data.data_response.professional.firstname) + " " + String(data.data_response.professional.lastname)
                                };

                                report[x][Object.keys(report[x]).length] = { 
                                    enunciado:"created_by", 
                                    id:"00", 
                                    local_alias:"created_by",
                                    respuesta: String(data.data_response.created_by.firstname) + " " + String(data.data_response.created_by.lastname)
                                };

                                var username = data.data_response.student_username;
                                if( username ){
                                    username = data.data_response.student_username.split("-")[0];
                                }else{
                                    username = "null";
                                }

                                report[x][Object.keys(report[x]).length] = { 
                                    enunciado:"student_code", 
                                    id:"00", 
                                    local_alias:"student_code",
                                    respuesta: username
                                };

                                report[x][Object.keys(report[x]).length] = { 
                                    enunciado:"student_firstname", 
                                    id:"00", 
                                    local_alias:"student_firstname",
                                    respuesta: String(data.data_response.student.firstname)
                                };

                                report[x][Object.keys(report[x]).length] = { 
                                    enunciado:"student_lastname", 
                                    id:"00", 
                                    local_alias:"student_lastname",
                                    respuesta: String(data.data_response.student.lastname)
                                };        
                                
                            },
                            failure: function(errMsg) {
                                console.log(errMsg);
                                
                            }
                        });

                        var fields_to_move = 3;

                        for( var k = Object.keys(report[x]).length - 1; k >= 0 ; k-- ){
                            report[x][k+fields_to_move] = $.extend( true, {}, report[x][k] );
                        }

                        report[x][0] = $.extend( true, {}, report[x][Object.keys(report[x]).length - 3] );
                        report[x][1] = $.extend( true, {}, report[x][Object.keys(report[x]).length - 2] );
                        report[x][2] = $.extend( true, {}, report[x][Object.keys(report[x]).length - 1] );

                        delete report[x][Object.keys(report[x]).length - 1];
                        delete report[x][Object.keys(report[x]).length - 1];
                        delete report[x][Object.keys(report[x]).length - 1];

                        $("#progress-custom").find("div").width( (( 100 / report_size ) * (x+1)).toFixed( 0 ) + "%" );
                        $("#progress-custom").find("div").html( (( 100 / report_size ) * (x+1)).toFixed( 0 ) + "%" );
                        $("#progress-custom").find("div").attr( "aria-valuenow", (( 100 / report_size ) * (x+1)).toFixed( 0 ) );

                    }

                    $("#progress-custom").find("div").addClass("progress-bar-success");
                    $("#message").removeClass("alert alert-info");
                    $("#message").addClass("alert alert-success");
                    $("#message").html( "<strong>Info!</strong>  Reporte generado." );
                    
                    return report;
                }else{

                    $("#progress-custom").find("div").addClass("progress-bar-success");
                    $("#message").removeClass("alert alert-info");
                    $("#message").addClass("alert alert-success");
                    $("#message").html( "<strong>Info!</strong>  Reporte generado." );

                    return report;
                }
            }

            function convertArrayOfObjectsToCSV(args) {  
                var result, ctr, keys, columnDelimiter, lineDelimiter, data;
        
                data = args.data || null;
                if (data == null || !data.length) {
                    return null;
                }
        
                columnDelimiter = args.columnDelimiter || ',';
                lineDelimiter = args.lineDelimiter || '\n';
                
                keys = Object.keys(data[0]);
              
                var enunciados = [];
                for( var x = 0; x <  Object.keys(data[0]).length; x++ ){
                    try {
                        enunciados.push( data[0][x].local_alias );
                    } catch (error) {
                        console.log( "ERROR" );
                        console.log( data[0] );
                    }
                    
                }
        
                result = '';
                result += enunciados.join(columnDelimiter);
                result += lineDelimiter;
        
                data.forEach(function(item) {
                    ctr = 0;
                    keys.forEach(function(key) {
                        if (ctr > 0) result += columnDelimiter;
                        try {
                            result += "\"" + item[key]['respuesta'].replace(/"/g, '\'') + "\"";
                        } catch (error) {}
                        ctr++;
                    });
                    result += lineDelimiter;
                });
        
                return result;
            };

            function downloadCSV( data_ ) {  

                var filename, link;
                var csv = convertArrayOfObjectsToCSV({
                    data: data_
                });
                

                if( csv == null ){
                    return
                };
                
                filename = 'reporte.csv';
                csvData = new Blob([csv], { type: 'text/csv' }); 

                var link = document.createElement("a");
                if (link.download !== undefined) {
                    var url = URL.createObjectURL(csvData);
                    link.setAttribute("href", url);
                    link.setAttribute("download", filename);
                    link.style = "visibility:hidden";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                };

            };

            $('#btn-generar-reporte').click(function(){

                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();

                if( start_date >= end_date ){
                    swal(
                        {title:'Información',
                        text: 'Intervalo de fechas inválido',
                        type: 'warning'},
                        function(){}
                    );
                }else{

                    $.ajax({
                        type: "POST",
                        url: "../managers/periods_management/periods_api.php",
                        data: JSON.stringify({ "function": "get_current_semester_by_apprx_interval", "params": [ start_date, end_date ] }),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        async: false,  
                        success: function(data){
                            id_semester = data.data_response;
                        },
                        failure: function(errMsg) {}
                    });

                    if( id_semester ){

                        var preguntas = JSON.parse( $("#dphpforms-reports-preguntas").html());
                    
                        $(".progress-bar").removeClass("progress-bar-success");
                        $(".progress-bar").removeClass("progress-bar-success");
                        $(".progress-bar").width( "0%" );
                        $(".progress-bar").html( "0%" );
                        $(".progress-bar").attr( "aria-valuenow", "0" );
                        $('#progress_group').css('display','block');
                        $("#message").removeClass("alert alert-success");
                        $("#message").addClass("alert alert-info");
                        $('#message').html( "<strong>Info!</strong> Se está generando el reporte, esto puede tardar un par de minutos dependiendo de su conexión a internet, capacidad del ordenador y rapidez del campus virtual." );
                        
                        $.get( '../managers/dphpforms/dphpforms_reverse_filter.php?id_pregunta=seguimiento_pares_fecha&cast=date&criterio={"criteria":[{"operator":">=","value":"'+start_date+'"},{"operator":"<=","value":"'+end_date+'"}]}', function( data ) {
                        
                            var count_records = Object.keys( data['results'] ).length;
                            var completed_records = [];
                            var completed_records_datatable = [];
                            var progress = 0;

                            for( var t = 0; t < count_records; t++ ){

                                $.get( '../managers/dphpforms/dphpforms_reverse_finder.php?respuesta_id=' + data['results'][t]['id'], function( answer ) {
                                    $.get( '../managers/dphpforms/dphpforms_get_record.php?record_id=' + answer['result']['id_registro'], function( record ) {
                                        
                                    if(  Object.keys( record['record'] ).length > 0  ){

                                            var seguimiento_base = $.extend( true, {}, preguntas );

                                            for( var x = 0; x <  Object.keys( record['record']['campos'] ).length; x++ ){

                                                for( var k = 0; k < Object.keys( seguimiento_base ).length; k++ ){
                                                    if( seguimiento_base[k].id == parseInt( record['record']['campos'][ x ]['id_pregunta'] ) ){
                                                        seguimiento_base[k].respuesta = record['record']['campos'][ x ]['respuesta'];
                                                                                                                
                                                    }
                                                }
                                            };

                                            completed_records.push( seguimiento_base );
                                        };

                                        progress ++;
                                        $("#progress-download").find("div").width( (( 100 / count_records ) * progress).toFixed( 0 ) + "%" );
                                        $("#progress-download").find("div").html( (( 100 / count_records ) * progress).toFixed( 0 ) + "%" );
                                        $("#progress-download").find("div").attr( "aria-valuenow", (( 100 / count_records ) * progress).toFixed( 0 ) );
                                        if( progress == count_records ){
                                            $("#progress-download").find("div").addClass("progress-bar-success");
                                            var tight_records = custom_actions( completed_records, "seguimiento_pares" );
                                            render_datatable( tight_records );
                                            downloadCSV( tight_records );
                                        };
                                        
                                    }).fail(function(err) {
                                        console.log(err);
                                    });
                                });
                                $('#progress').text( Math.round( progress ) );
                            }
                            if( count_records == 0 ){ 
                                $('#progress').text( 100 );
                                $("#message").removeClass("alert alert-info");
                                $("#message").addClass("alert alert-success");
                                $("#message").html( "<strong>Info!</strong>  Reporte generado." );
                            };
                        });

                    }else{
                        swal(
                            {
                                title:'Información',
                                text: "Las fechas deben estar en el lapso de tiempo que comprende un periodo académico. ",
                                type: 'error'
                            },
                            function(){}
                        );
                    }
                };
            });
        }
    };
      
});
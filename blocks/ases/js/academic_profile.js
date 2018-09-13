$(document).ready(function(){
    var userid =  $('#ficha_estudiante #iduser').val();
    create_semesters_panel(userid);
    
    $('#pes_academica').on('click',function(){
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        create_semesters_panel(userid);
    });
    
  
    
     // mostrar descripción    
    $('#academic_report').on('click', 'td.details-control', function () {
        
        var tr = $(this).closest('tr');
        var boton = tr.find('span');
        //console.log(boton.attr());
        var tableid =  $(this).closest('table').attr('id');
        //  console.log(tableid);
        //var tables =  $.fn.dataTable.tables();
        var table = $('#'+tableid).DataTable();
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            boton.attr('hidden', true);
        }
        else {
            // Open this row format(row.data()) 
            close_tabs();
            boton.removeAttr('hidden');
            row.child(format(row.data()) ).show();
            tr.addClass('shown');
            
            
                    
        }
        
        // console.log(boton.css('display'));
    });
    
    
});

//Close tabs

function close_tabs(){
      
    $('#academic_report td.details-control').each(function(){
        
        var tr = $(this).closest('tr');
        var boton = tr.find('span');
        //console.log(boton.attr());
        var tableid =  $(this).closest('table').attr('id');
       // console.log("tablaid: ");
        //var tables =  $.fn.dataTable.tables();
        var table = $('#'+tableid).DataTable();
        var row = table.row( tr );
 
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            boton.attr('hidden', true);
            boton.parent().html("<span class = 'editar' hidden>Editar Notas </span>");
        }
    });
}


/* Formatting function for row details - modify as you need */
function format (dataRow) {
    
    if(dataRow.descriptions  == null){
        return 'El curso no registra calificaciones';
    }
    else{
        return dataRow.descriptions ;
    }
}

function create_semesters_panel(userid){
    var tables = new Array();  //objeto que  va a contener cada una de las tablas creadas
    
    $.ajax({
    type: "POST",
    data: {dat: 'semesters', user:userid},
    url: "../managers/academic_processing_profile.php",
    success: function(msg)
    {   
        //console.log(msg);
        $('#academic_report').html('');
        if(msg){
            //console.log(msg);
            var isfirst = true;
            
            for(x in msg){
                
                var panel = '<div class="accordion-container"><a id="title'+msg[x].id_semester+'" class="accordion-toggle">Semestre '+msg[x].name_semester+'<span class="toggle-icon"><i class="glyphicon glyphicon-chevron-left"></i></span></a>';
                panel += '<div id="panel-body'+msg[x].id_semester+'" class="accordion-content ScrollStyle"></div></div>';
                
                $('#academic_report').append(panel);
                
                var courses = msg[x].courses;

                if(courses.length != 0){
                    
                    $("#panel-body"+msg[x].id_semester).empty();
                    $("#panel-body"+msg[x].id_semester).append('<table id="academicTableResult'+msg[x].id_semester+'" class="display" cellspacing="0" width="100%"><thead><thead></table> ');
                    var table = $("#academicTableResult"+msg[x].id_semester).DataTable({
                        "bsort": false,
                        "bPaginate": false,
                        "searching": false,
                        "columns": [
                            {
                                "className":      'details-control',
                                "orderable":      false,
                                "data":           null,
                                "defaultContent": ''
                            },
                            { "title": "Curso", "data": "name_course"},
                            { "title": "Calificación Final", "data": "grade" },
                            {
                                "className":      '',
                                "orderable":      false,
                                "data":           null,
                                "defaultContent": "<span class = 'editar' hidden >Editar Notas </span>"
                            },
                        ],
                        "data": msg[x].courses,
                        "order": [[1, 'asc']],
                        
                    });
                    
                    tables[msg[x].id_semester] = table;
                }
                if(isfirst){
                	openAccordionToggle('#academic_report #title'+msg[x].id_semester);
                	isfirst = false;
                }
            }
            
            console.log();
            
            var riesgo = 'bajo';
            
            for(x in msg[0].courses){
                if(courses[x].grade < 3.0){
                    riesgo = 'alto';
                    break;
                }
                else if(courses[x].grade > 3.0 && courses[x].grade < 3.5){
                    riesgo = 'medio';
                }
            }
            $('#pes_academica').html('');
            $('#pes_academica').append('ACADÉMICA <i>Riesgo '+riesgo+'</i>');
            
        }else{
           $("#panel-body"+msg[x].id_semester).append("No se encontraron registros");
        }
        
        
    },
    dataType: "json",
    cache: "false",
    error: function(msg){console.log(msg)},
    });
}

function prueba(userid){
    
            $.ajax({
        type: "POST",
        data: {dat: 'semesters', user:userid},
        url: "../managers/academic_processing_profile.php",
        success: function(msg)
        {   
            //console.log(msg);
            $('#academic_report').html('');
            console.log(msg);

        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg)},
        });
}
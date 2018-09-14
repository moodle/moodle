//se añaden los eventos a cada boton
$(document).ready(function(){
    $('#div_teacher_reports').click(function(){ teacher_reports(); return false; });
    $('#div_item_reports').click(function(){ items_reports(); return false; });
});

function teacher_reports(){
    var curso="si";
 $.ajax({
        type: "POST",
        data: {course:curso},
        url: "../../../blocks/ases/managers/gr_grade_google_info.php",
        async:false,
        success: function(msg)
        {
            
            $('#limpiarTabla').html('<table class="reemplazar_report_grade_google " id="reemplazar_general_reports"></table>')
            var arrayInfoEstudiantes=infoEstudiantes(msg);
            var arregloEstudiantesOrdenado=organizarInformacionPorRegistro(arrayInfoEstudiantes,"Estudiantes");
            var arrayInfoItems=infoItems(msg);
            var arregloItemsOrdenado=organizarInformacionPorRegistro(arrayInfoItems,"Items");
            var arrayInfoNotas=infoNotas(msg);
            var arregloNotasOrdenado=organizarInformacionPorRegistro(arrayInfoNotas,"Notas");
            
            arregloDeReporte=reporteDeNotas(arregloNotasOrdenado,arregloItemsOrdenado,arregloEstudiantesOrdenado);
            crearDatatable(arregloDeReporte,"notasPerdidas");

        },
        dataType: "json",
        cache: "false",
        error:  function(msg){alert("no")},
        });
}

function items_reports(){
        var curso="si";
 $.ajax({
        type: "POST",
        data: {course:curso},
        url: "../../../blocks/ases/managers/gr_grade_google_info.php",
        async:false,
        success: function(msg)
        {
            
            $('#limpiarTabla').html('<table class="reemplazar_report_grade_google " id="reemplazar_general_reports"></table>')
            var arrayInfoAsignaturas=infoAsignaturasSinItem(msg);
            var arregloAsignaturasOrdenado=organizarInformacionPorRegistro(arrayInfoAsignaturas,"AsignaturasSinItem");
            crearDatatable(arregloAsignaturasOrdenado,"asignaturasSinItems");

        },
        dataType: "json",
        cache: "false",
        error:  function(msg){alert("no")},
        });
}

function infoAsignaturasSinItem(msg) {
    
    var $page=$(msg);

    var $rows= $page.find("div#816670514 table.waffle tbody tr");      
    var data = [];

    $rows.each(function(row, v) {
        $(this).find("td").each(function(cell, v) {
            if (typeof data[cell] === 'undefined') {
                data[cell] = [];
            }
            data[cell][row] = $(this).text();
        });
    });
    return data;
}

function infoEstudiantes(msg) {
    
    var $page=$(msg);

    var $rows= $page.find("div#176443518 table.waffle tbody tr");      
    var data = [];

    $rows.each(function(row, v) {
        $(this).find("td").each(function(cell, v) {
            if (typeof data[cell] === 'undefined') {
                data[cell] = [];
            }
            data[cell][row] = $(this).text();
        });
    });
    return data;
}

function infoItems(msg) {
    
    var $page=$(msg);

    var $rows= $page.find("div#0 table.waffle tbody tr");      
    var data = [];

    $rows.each(function(row, v) {
        $(this).find("td").each(function(cell, v) {
            if (typeof data[cell] === 'undefined') {
                data[cell] = [];
            }
            data[cell][row] = $(this).text();
        });
    });
    return data;
}

function infoNotas(msg) {
    
    var $page=$(msg);

    var $rows= $page.find("div#1901720047 table.waffle tbody tr");      
    var data = [];

    $rows.each(function(row, v) {
        $(this).find("td").each(function(cell, v) {
            if (typeof data[cell] === 'undefined') {
                data[cell] = [];
            }
            data[cell][row] = $(this).text();
        });
    });
    return data;
}

function organizarInformacionPorRegistro(arregloOrdenarRegistro,arregloTipo){


    var arregloOrganizado=[];
    var columnas=arregloOrdenarRegistro.length;
    var filas = arregloOrdenarRegistro[0].length;
    for(var filasArreglo=0;filasArreglo<filas;filasArreglo++)
    {
         if(arregloTipo=="Estudiantes"&&arregloOrdenarRegistro[8][filasArreglo].substr(0,3)!=999)
         {
            var arregloAuxiliar=[];
            for(var columnasArreglo=0;columnasArreglo<columnas;columnasArreglo++)
            {   
                arregloAuxiliar.push(arregloOrdenarRegistro[columnasArreglo][filasArreglo]);
            }
            arregloOrganizado.push(arregloAuxiliar);
        }else if(arregloTipo=="Notas"&&arregloOrdenarRegistro[2][filasArreglo].substr(0,3)!=999&&arregloOrdenarRegistro[3][filasArreglo]!=""&&arregloOrdenarRegistro[3][filasArreglo]<3)
        {
            var arregloAuxiliar=[];
            for(var columnasArreglo=0;columnasArreglo<columnas;columnasArreglo++)
            {   
                arregloAuxiliar.push(arregloOrdenarRegistro[columnasArreglo][filasArreglo]);
            }
            arregloOrganizado.push(arregloAuxiliar);
        }else if(arregloTipo=="AsignaturasSinItem"&&arregloOrdenarRegistro[7][filasArreglo]=="no")
        {
            var arregloAuxiliar=[];
            for(var columnasArreglo=0;columnasArreglo<columnas-2;columnasArreglo++)
            {   
                arregloAuxiliar.push(arregloOrdenarRegistro[columnasArreglo][filasArreglo]);
            }
            arregloOrganizado.push(arregloAuxiliar);
        }else if(arregloTipo=="Items")
        {
            var arregloAuxiliar=[];
            for(var columnasArreglo=0;columnasArreglo<columnas;columnasArreglo++)
            {   
                arregloAuxiliar.push(arregloOrdenarRegistro[columnasArreglo][filasArreglo]);
            }
            arregloOrganizado.push(arregloAuxiliar);
        }
    }
    
        arregloOrganizado.splice(0,3);   
    return arregloOrganizado;
}

function reporteDeNotas(infoNotas,infoItems,informacionEstudiantes)
{
    var arregloNotas=[];
    
    for(var nota in infoNotas)
    { 
        var arregloAuxiliar=[];
        //nota
        arregloAuxiliar.push(infoNotas[nota][3]);
        //estudiante
        arregloAuxiliar.push(infoNotas[nota][2]);
        //item
        arregloAuxiliar.push(infoNotas[nota][1]);
        arregloNotas.push(arregloAuxiliar);
  }
  
  //cada subarreglo lleva la forma nota,estudiante,item
  for(var grupo in arregloNotas)
  {
    var arregloAuxiliar=arregloNotas[grupo];
    
    for(var item in infoItems)
    {
      //si el codigo del item coincide con el codigo de la hoja items
      if(arregloAuxiliar[2]==infoItems[item][0])
      {
        
        //nombre item
        arregloAuxiliar.push(infoItems[item][1])
        //peso item
        arregloAuxiliar.push(infoItems[item][2])
        //codigo unico grupo
        arregloAuxiliar.push(infoItems[item][3])
        break;
      }
    }
    arregloNotas[grupo]=arregloAuxiliar;
  }
  
  //cada subarreglo lleva la forma nota,estudiante,item,nombre item,peso item, codigo unico grupo
  for(var informacion in arregloNotas)
  {
    var arregloAuxiliar=arregloNotas[informacion];
    for(var informacionCompletaEstudiante in informacionEstudiantes)
    {
      //se compara el codigo unico de la materia para saber a quien pertenece la informacion
      //y ademas que el codigo del estudiante coincida
      if(arregloAuxiliar[5]==informacionEstudiantes[informacionCompletaEstudiante][5]&&arregloAuxiliar[1]==informacionEstudiantes[informacionCompletaEstudiante][8])
      {
        //nombre estudiante
        arregloAuxiliar.push(informacionEstudiantes[informacionCompletaEstudiante][7])
        //apellido estudiante
        arregloAuxiliar.push(informacionEstudiantes[informacionCompletaEstudiante][6])
        //codigo materia
        arregloAuxiliar.push(informacionEstudiantes[informacionCompletaEstudiante][2])
        //grupo maetria
        arregloAuxiliar.push(informacionEstudiantes[informacionCompletaEstudiante][4])
        //nombre materia
        arregloAuxiliar.push(informacionEstudiantes[informacionCompletaEstudiante][3])
        //profesor
        arregloAuxiliar.push(informacionEstudiantes[informacionCompletaEstudiante][0])
        //correo
        arregloAuxiliar.push(informacionEstudiantes[informacionCompletaEstudiante][1])
        break;
      }
    }
    arregloNotas[informacion]=arregloAuxiliar;
  }
  
  var arregloNotasImprimir=[];
  for(var imprimir in arregloNotas)
  {   
      var arregloAuxiliar=[];
      arregloAuxiliar.push(arregloNotas[imprimir][1]);
      arregloAuxiliar.push(arregloNotas[imprimir][6]);
      arregloAuxiliar.push(arregloNotas[imprimir][7]);
      arregloAuxiliar.push(arregloNotas[imprimir][8]);
      arregloAuxiliar.push(arregloNotas[imprimir][9]);
      arregloAuxiliar.push(arregloNotas[imprimir][10]);
      arregloAuxiliar.push(arregloNotas[imprimir][2]);
      arregloAuxiliar.push(arregloNotas[imprimir][3]);
      arregloAuxiliar.push(arregloNotas[imprimir][4]);
      arregloAuxiliar.push(arregloNotas[imprimir][0]);
      arregloAuxiliar.push(arregloNotas[imprimir][11]);
      arregloAuxiliar.push(arregloNotas[imprimir][12]);
      
      arregloNotasImprimir.push(arregloAuxiliar);
      
  }
  return arregloNotasImprimir
}

function crearDatatable(arrayDatatable,reporte) {
    var language = new Array();
        var language = {
                  "sProcessing":     "Procesando...",
                  "sLengthMenu":     "Mostrar _MENU_ registros",
                  "sZeroRecords":    "No se encontraron resultados",
                  "sEmptyTable":     "Ningún dato disponible en esta tabla",
                  "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                  "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                  "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                  "sInfoPostFix":    "",
                  "sSearch":         "Buscar:",
                  "sUrl":            "",
                  "sInfoThousands":  ",",
                  "sLoadingRecords": "Cargando...",
                  "oPaginate": {
                      "sFirst":    "Primero",
                      "sLast":     "Último",
                      "sNext":     "Siguiente",
                      "sPrevious": "Anterior"
                  },
                  "oAria": {
                      "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                      "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                  }
              }
    var columnsInfo = [];
    
    if(reporte=="notasPerdidas")
    {
        columnsInfo = [
                { "title": "Codigo Estudiante"},
                { "title": "Nombre Estudiante"},
                { "title": "Apellido Estudiante"},
                { "title": "Codigo Materia"},
                { "title": "Codigo Grupo"},
                { "title": "Nombre Materia"},
                { "title": "Codigo Item"},
                { "title": "Nombre Item"},
                { "title": "Peso"},
                { "title": "Nota"},
                { "title": "Nombre Profesor"},
                { "title": "Correo"}];
                
                $('#reemplazar_general_reports').DataTable( {
                data: arrayDatatable,
                columns: columnsInfo,
                order : [[0,"asc"],[9,"asc"]],
                language : language
        
    });
    }else if(reporte=="asignaturasSinItems")
    {
        columnsInfo = [
                { "title": "Nombre Profesor"},
                { "title": "Correo Profesor"},
                { "title": "Codigo Asignatura"},
                { "title": "Nombre Asignatura"},
                { "title": "Grupo Asignatura"},
                { "title": "Id Asignatura"}];
                
                $('#reemplazar_general_reports').DataTable( {
                data: arrayDatatable,
                columns: columnsInfo,
                order : [[0,"asc"]],
                language : language
        
    });
    }
    
}



$(document).ready(function(){
    var semester = $('#semester').val();
    createTable(semester);
    
    $('#semester').on('change',function(){
        createTable($(this).val());});

});

$(document).on('click','#tableResult tbody tr td',function(){
    var pagina = "talentos_profile.php";
    var table = $("#tableResult").DataTable();
    var colIndex = table.cell(this).index().column;
    
    if(colIndex <= 2 ){
        location.href=pagina+location.search+"&talento_id=" + table.cell(table.row(this).index(),0).data() + "&ficha=asistencia";
    }
});  


function createTable(semester)
{
    $.ajax({
        type: "POST",
        data: {dat: semester},
        url: "../managers/attendance_processing.php",
        success: function(msg)
        {   
            console.log(msg);
            $("#attendance_div").empty();
            $("#attendance_div").append('<table id="tableResult" class="display" cellspacing="0" width="100%"></table>');
            var table = $("#tableResult").DataTable(msg);
            
        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log("Asistencias error")}
        });
}
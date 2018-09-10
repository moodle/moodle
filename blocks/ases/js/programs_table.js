//Nueva vista

$(document).ready(function() {
    $("#btn-send-indexform").on('click', function() {
        createTable();
    });
});

$(document).on('click','#tableResult tbody tr td',function(){
             var pagina = "talentos_profile.php";
             var table = $("#tableResult").DataTable();
             var colIndex = table.cell(this).index().column;

              if(colIndex <= 2){
                $("#formulario").each(function(){this.reset});
                // document.getElementById("formulario").reset();
                location.href=pagina+location.search+"&talento_id=" + table.cell(table.row(this).index(),0).data();
              }
});

function createTable()
{
    var dataString = $('#formulario').serializeArray();
    $.ajax({
        type: "POST",
        data: dataString,
        url: "../managers/server_processing.php",
        success: function(msg)
        {  
            $("#div_table").empty();
            $("#div_table").append('<table id="tableResult" class="display" cellspacing="0" width="100%"><thead> </thead></table>');
            $("#tableResult").DataTable(msg.data);
            
        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg)},
        })
}

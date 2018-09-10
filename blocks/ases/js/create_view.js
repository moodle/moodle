$(document).ready(function() {
    //Cargar los datos de los roles creados en el select de rol-cmb
    
    $("#add_vista").on('click',function(){
        crearVista();
    });
});


/**
 * Crea un ajax para llamar la accion que guarda la nueva accion en la base de datos
 * @param Nombre 
 * @param Descripcion
 * @author Edgar Mauricio Ceron
 */ 

function crearVista(){
    var instance = getUrlParams().instanceid;
    var nombre = $("#nombre").val().trim();
    var msj = "";

    if(nombre.length == 0){
        msj = "Nombre no puede ser nulo";
    }

    if(nombre.length == 0){
        alert(msj);
    }
    else{
        $.ajax({
            type: "POST",
            data: {nombre: nombre, instance: instance},
            url: "../managers/ActionCreateView.php",
            async: false,
            success: function(msg){
                alert(msg);
            }
        });
    }
}

/**
 * Obtiene los parametros de la url
 */
function getUrlParams (page) {
    // This function is anonymous, is executed immediately and
    // the return value is assigned to QueryString!
    var query_string = [];
    var query = document.location.search.substring(1);
    var vars = query.split("&");

    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        query_string[pair[0]] = pair[1];
    }

    return query_string;
}

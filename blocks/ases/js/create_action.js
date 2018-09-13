$(document).ready(function() {
    //Cargar los datos de los roles creados en el select de rol-cmb
    
    $("#add_accion").on('click',function(){
        crearAccion();
    });

    $("#add_profile").on('click',function(){
        crearPerfil();
    });
});

/**
 * Crea un ajax para llamar la accion que guarda la nueva accion en la base de datos
 * @param Nombre 
 * @param Descripcion
 * @author Edgar Mauricio Ceron
 */ 

function crearAccion(){
    var nombre = $("#nombre").val().trim();
    var descripcion = $("#descripcion").val().trim();
    var msj = "";
    
    if(nombre.length == 0){
        msj = "Nombre no puede ser nulo"; 
    }
    if(descripcion.length == 0){
        msj = msj + "\nDescripcion no puede ser nulo";
    }
    
    if(nombre.length == 0 || descripcion.length == 0){
        alert(msj);
    }
    else{
        $.ajax({
            type: "POST",
            data: {nombre: nombre, descripcion: descripcion},
            url: "../managers/ActionCreateAction.php",
            async: false,
            success: function(msg){
                $("#formAction")[0].reset();
                alert(msg);
            }
        });
    }
}
/**
 * Crea un ajax para llamar la accion que guarda el nuevo perfil en la base de datos
 * @param Nombre 
 * @param Descripcion
 */ 

function crearPerfil(){
    var nombre= $("#nombre_perfil").val().trim();
    var descripcion = $("#descripcion_perfil").val().trim();
    var msj = "";
    
    if(nombre.length == 0){
        msj = "Nombre del perfil no puede ser nulo"; 
    }
    if(descripcion.length == 0){ 
        msj = msj + "\nDescripcion del perfil no puede ser nulo";
    }
    
    if(nombre.length == 0 || descripcion.length == 0){
        alert(msj);
    }
    else{
        $.ajax({
            type: "POST",
            data: {nombre_perfil: nombre, descripcion_perfil: descripcion},
            url: "../managers/ActionCreateAction.php",
            async: false,
            success: function(msg){
                $("#formPerfil")[0].reset();
                alert(msg);
            }
        });
    }
}

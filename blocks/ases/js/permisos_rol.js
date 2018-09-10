$(document).ready(function() {
    //Cargar los datos de los roles creados en el select de rol-cmb
    cargarFuncionalidades();
    cargarRoles();
    
    
    // $("#rol-cmb").on('change',function(){
    //     var rol = $("#rol-cmb").val();
    //     cargarPermisosRol(rol);
    // });
});






/**
 * Carga la informacion de los roles en el select rol-cmb, esta se obtiene
 * del manager load_role_select.php 
 * @author Edgar Mauricio Ceron Florea
 * @uses rol-cmb html Select en donde se cargara la información
 */

function cargarRoles(){
    $.ajax({
        type: "POST",
        url: "../managers/load_role_select.php",
        async: false,
        success: function(msg){
            $('#rol-cmb').empty();
            for (role in msg){
                var html = "<option value=\""+msg[role].id+"\">"+msg[role].nombre_rol+"</option>";
                $('#rol-cmb').append(html);
            }
        },
        dataType: "json",
        error: function(msg){console.log(msg);}
    })
}
/**
 * Crea una lista de elementos html que representa la lista de funcionalidades
 * que existen en la base de datos y pinta permisos existentes para cada una
 * Ejemplo:
 *  <div class="col-lg-3 col-md-3">
 *    <fieldset id="**NOMBRE FUNCIONALIDAD">
 *      <legend><input type="checkbox">Datos de Contacto</legend>
 *        <input type="checkbox" name="chk[]" value="**ID FUNCIONALIDAD - **ID PERMISO">**NOMBRE PERMISO<br>
 *        <input type="checkbox" name="chk[]" value="**ID FUNCIONALIDAD - **ID PERMISO">**NOMBRE PERMISO<br>
 *        <input type="checkbox" name="chk[]" value="**ID FUNCIONALIDAD - **ID PERMISO">**NOMBRE PERMISO<br>
 *    </fieldset>
 *  </div>
 * 
 * 
 */ 
function cargarFuncionalidades(){
    $.ajax({
        type: "POST",
        url: "../managers/load_permisos_rol.php",
        async: false,
        success: function(msg){
            //var permisos = msg['permisos'];
            var html = '';
            var x = 0;
            var funcionalidades;
            var permisos;
            for(arreglo in msg){
                if(x == 0){
                    permisos = msg[arreglo];
                }
                if(x == 1){
                    funcionalidades = msg[arreglo];
                }
                x++;
            }
            
            for(funcionalidad in funcionalidades){
                html = html + '<div class="col-lg-3 col-md-3">';
                html = html + '<fieldset id="' + funcionalidades[funcionalidad].nombre_func + '">';
                html = html + '<legend>' + funcionalidades[funcionalidad].nombre_func + '</legend>';
                for(permiso in permisos){
                    html = html + '<input type="checkbox" name="permiso[]" ';
                    html = html + '" value="' + funcionalidades[funcionalidad].id + '-' 
                    + permisos[permiso].id + '">' + permisos[permiso].permiso + '<br>';
                }
                html = html + '</fieldset>';
                html = html + '</div>'; 
            }
            
            document.getElementById("lista_funcionalidades").innerHTML = html;
        },
        dataType: "json",
        error: function(msg){console.log(msg);}
    })
}

/**
 * Carga en la interfaz los permisos que tiene el rol seleccionado
 * De acuerdo a los registrado en la base de datos se activaran los 
 * chekbox correspondientes en la GUI
 * @author Edgar Mauricio Ceron Florez
 * @param rol Id del rol a consultar
 */ 
function cargarPermisosRol(rol){
        //$(rol).val();
        $.ajax({
        type: "POST",
        data: {rol: $(rol).val()},
        url: "../managers/mostrar_permisos_rol.php",
        async: false,
        success: function(msg){
            var permiso;
            var funcionalidad;
            var chekbox_permiso;
            //limpiarCheckPermisos();
            for(permiso in msg){
                funcionalidad = msg[permiso].id_funcionalidad;
                permiso = msg[permiso].id_permiso;
                chekbox_permiso =  funcionalidad + "-" + permiso;
                $('#formulario input[type=checkbox]').each(function(){
                    if ($(this).val() == chekbox_permiso) {
                        this.checked = true;
                    }
                });
                //document.getElementsByName(chekbox_permiso).checked = true;
                //$('#' + chekbox_permiso).attr('checked', true);
            }
        },
        dataType: "json",
        error: function(msg){console.log(msg);}
    })
}

/**
 * Desactiva todos los checkbox de permisos
 * @author Edgar Muaricio Ceorn Florez
 */

function limpiarCheckPermisos(){
        $.ajax({
        type: "POST",
        url: "../managers/load_permisos_rol.php",
        async: false,
        success: function(msg){
            var x = 0;
            var funcionalidades;
            var permisos;
            for(arreglo in msg){
                if(x == 0){
                    permisos = msg[arreglo];
                }
                if(x == 1){
                    funcionalidades = msg[arreglo];
                }
                x++;
            }
            var chekbox_permiso;
            
            for(funcionalidad in funcionalidades){
                for(permiso in permisos){
                    chekbox_permiso = 'p' + funcionalidades[funcionalidad].id + '-' + permisos[permiso].id;
                    $('#' + chekbox_permiso).checked = false;
                }
            }
        },
        dataType: "json",
        error: function(msg){console.log(msg);}
    })
}

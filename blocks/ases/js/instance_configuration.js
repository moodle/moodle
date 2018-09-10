$(document).ready(function(){
    loadPrograms();
    $('#search_button').on('click', function(){
        $(".assignment_li").removeClass('hidden');
        //$("#username_input").attr('readonly', true);
       var usr = $('#username_input').val();
       if(usr == null || usr == "") return 0;
       searchUser();
    }) ;
   
   $('#ok_button').on('click', function(){
       updateSystemUser();
   });
   
   $("#cancel_button").on('click', function(){
        $(".assignment_li").addClass('hidden');
        $("#form_mon_student").fadeOut();
        $("#username_input").val("");
        $('#username_input').prop('disabled', false);
        $('#name_lastname').val(" ");
        
        $("#form_prof_type").fadeOut();
        
    });
    
    $('#next').on('click',function() {
        var pagina = "upload_files_form.php";

        location.href=pagina+location.search;

    });
   
   
   $('#listadministradores').on('click', function() {
       loadSystemAdministrators();
   });
   
   loadSystemAdministrators();
   
   $('#div_users').on('click','#delete_user',function(){
        
        var table = $("#div_users #tableUsers").DataTable();
        var td =$(this).parent();
        var childrenid = $(this).children('span').attr('id');
        var colIndex = table.cell(td).index().column;
        
        var username =  table.cell(table.row(td).index(),0).data();
        var firstname = table.cell(table.row(td).index(),1).data();
        var lastname = table.cell(table.row(td).index(),2).data();
        
        swal(
            {  
                title: "Estas seguro/a?",   
                text: "Al usuario <strong>"+firstname+" "+lastname+"</strong> con código <strong>"+username+"</strong> se le inhabilitará los permisos de administrador del sistema</strong>.<br><strong>¿Estás de acuerdo con los cambios que se efectuarán?</strong>",   
                type: "warning",
                html: true,
                showCancelButton: true,   
                confirmButtonColor: "#d51b23",   
                confirmButtonText: "Si!",
                cancelButtonText: "No", 
                closeOnConfirm : true, 
            }, 
            function(isConfirm){
                if(isConfirm){
                
                    deleteUser(username);
                    
                
                }
            }
        );

    
    });
   
   
});

function searchUser(){
    var username = $('#username_input').val();
    var data = new Array();
    
    data.push({name:'function', value:'search'});
    data.push({name:'username', value:username});
    
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/instaceconfiguration.php",
        success: function(msg)
        {
            var error = msg.error;
            if(!error){
                $('#username_input').prop('disabled', true);
                $('#lista_programas').prop('disabled', false);
                $('#name_lastname').val(msg.firstname+" "+msg.lastname);
                $('#lista_programas').append('<option value="'+msg.cod_programa+'" selected> '+msg.cod_programa+' - '+msg.nombre_programa+'</option>');
               
                if(msg.seg_academico == 1){
                    $('label input[name="segAca"][ value="1"]').prop('checked', true);
                }else{
                    $('label input[name="segAca" ][ value="0"]').prop('checked', true);
                }
                
                if(msg.seg_asistencias == 1){
                    $('label input[name="segAsis" ][ value="1"]').prop('checked', true);
                }else{
                    $('label input[name="segAsis"][ value="0"]').prop('checked', true);
                }
                
                if(msg.seg_socioeducativo == 1){
                    $('label input[name="segSoc" ][ value="1"]').prop('checked', true);
                }else{
                    $('label input[name="segSoc"][ value="0"]').prop('checked', true);
                }
                
            }else{
                swal({title: 'Error', html:true, type: "error",  text: msg.error, confirmButtonColor: "#D3D3D3"});
            }
            
            
        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg)},
    });

}

function loadPrograms(){
    $('#lista_programas').html('');
    
    $.ajax({
        type: "POST",
        data:[{name:'function', value:'load_programs'}],
        url: "../managers/instaceconfiguration.php",
        success: function(msg)
        {   
            $('#lista_programas').append('<option value="0" selected> 0000 - Ninguno</option>');
            if(msg){
                for (x in msg){
                    $('#lista_programas').append('<option value="'+msg[x].cod_univalle+'">'+msg[x].cod_univalle+' - '+msg[x].nombre+'</option>');//cod_univalle
                }
            }
            
            
        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg)}
    });
}


function updateSystemUser(){
    $('#username_input').prop('disabled', false);
    $('#name_lastname').prop('disabled', false);
    var username =  $('#username_input').val();
    var programa = $("#lista_programas option[value='"+$('#lista_programas').val()+"']").text()
    var data =  $('input, select').serializeArray();
    $('#username_input').prop('disabled', true);
    $('#name_lastname').prop('disabled', true);
    var blockid =  "<?php echo $blockid; ?>";
    
    //se obtiene el id de la instancia por la url
    var instanceid = 0;
    var urlParameters = location.search.split('&');
    
    for (x in urlParameters){
        if(urlParameters[x].indexOf('instanceid') >= 0){
            var intanceparameter = urlParameters[x].split('=');
            instanceid = intanceparameter[1];
        }
    }
    
    data.push({name:"idinstancia" , value: instanceid});
    data.push({name:"function" , value: "updateUser"});
    console.log(data);
    
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/instaceconfiguration.php",
        success: function(msg)
        {   
            //console.log(msg);
            if(msg === true){
                swal({
                        title: "Actualizado con exito!!", 
                        html:true, 
                        type: "success",
                        text: "El usuario con código "+username+" es ahora administrador del sistema de "+programa , 
                        confirmButtonColor: "#d51b23"}, 
                        function(isConfirm){   if (isConfirm) {  
                            
                        }
                });
                $('#next_div').fadeIn();
                loadSystemAdministrators();
                loadPrograms();
            }else{
                swal({
                        title: "Error :(", 
                        html:true, 
                        type: "error",
                        text: msg,
                        confirmButtonColor: "#d51b23"}, 
                        function(isConfirm){   if (isConfirm) {  
                            
                        }
                });
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg)}
    });
}


function loadSystemAdministrators(){
    var data =  new Array();
    data.push({name:"function" , value: "loadSystemAdministrators"});
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/instaceconfiguration.php",
        success: function(msg){
            $("#div_users").empty();
            $("#div_users").append('<table id="tableUsers" class="display" cellspacing="0" width="100%"><thead><thead></table>');
            var table = $("#tableUsers").DataTable(msg);
            $('#div_users #delete_user').css('cursor','pointer');
        },
        dataType: "json",
        cache: "false",
        error: function(msg){
            console.log(msg);
        }
    });
}

function deleteUser(username){
    var data =  new Array();
    data.push({name:"function" , value: "deleteUser"});
    data.push({name:"username" , value: username});
    //console.log(data);
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/instaceconfiguration.php",
        success: function(msg){
            if(msg === true){
                swal({
                        title: "Actualizado con exito!!", 
                        html:true, 
                        type: "success",
                        text: "El usuario con código "+username+" se ha eliminado satisfactoriamente" , 
                        confirmButtonColor: "#d51b23"}, 
                        function(isConfirm){   if (isConfirm) {  
                            
                        }
                });
                loadSystemAdministrators();
                loadPrograms();
            }else{
                swal({
                        title: "Error :(", 
                        html:true, 
                        type: "error",
                        text: msg,
                        confirmButtonColor: "#d51b23"}, 
                        function(isConfirm){   if (isConfirm) {  
                            
                        }
                });
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg){
            console.log(msg);
        }
    });
}
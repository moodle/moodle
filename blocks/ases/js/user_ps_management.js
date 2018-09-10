$(document).ready(function(){
    assignment();
    $('#a_assignment').click(function(){ assignment(); return false; });
    $('#a_reports').click(function(){ reports(); return false; });
    $('#search_button').click(function(){loadInfoUser(); return false;});
    $('#button_edit_role').click(function(){disabledButtonsEdition(); return false;})
    var currentRole = $("#select_role").val();
    $('#button_cancel').click(function(){cancel(currentRole); return false;})
})

function assignment(){
    $("#div_assignment").show();
    $("#div_reports").hide();
}

function reports(){
    $("#div_assignment").hide();
    $("#div_reports").show();
}

function loadInfoUser(){
    $("#button_add_student").remove();
    $("#button_add_monitor").remove();
    var username = $("#input_user_code").val();
    if($("#input_user_code").val() == ""){
        swal("Error","Por favor digite un nombre de usuario", "error");
    }else{
        $.ajax({
        type: "POST",
        data: {dat: username, idinstancia: getIdinstancia()},
        url: "../managers/search_user.php",
        success: function(msg){
            if(msg.error){
                swal("Error", msg.error, "error");
                $("#input_user_code").val('');
            }else{
                if(msg.rol == "monitor_ps"){
                    $("#div_info_user").show();
                    $("#input_id_user").val(msg.id);
                    var fullname = msg.firstname + " " + msg.lastname;
                    $("#photo").attr("src","../../../user/pix.php/"+msg.id+"/f1.jpg");
                    $("#input_full_name").val(fullname);
                    $("#option_monitor_ps").attr('selected','selected');
                    $("#input_email").val(msg.email);
                    $("#input_boss").val(msg.boss_name);
                    $("#div_list_assignments").show();
                    $("#button_add_monitor").remove();  
                    $("#div_buttons").append('<button class="button_green" type="button" id="button_add_student" style="margin-left: 0px" onclick="add_student()">Añadir estudiante</button>');
                    var students = loadStudents();
                }else if(msg.rol == "practicante_ps"){
                    $("#div_info_user").show();
                    $("#input_id_user").val(msg.id);
                    var fullname = msg.firstname + " " + msg.lastname;
                    $("#input_full_name").val(fullname);
                    $("#input_email").val(msg.email);
                    $("#option_practicante_ps").attr('selected','selected');
                    $("#input_boss").val(msg.boss_name);
                    $("#div_list_assignments").show();
                    $("#button_add_student").remove();
                    $("#div_buttons").append('<button class="button_green" type="button" id="button_add_monitor" style="margin-left: 0px">Añadir monitor</button>');
                    $("button_add_monitor").click(function(){alert("button monitor")});
                }else if(msg.rol == "ninguno"){
                    $("#div_info_user").show();
                    $("#input_id_user").val(msg.id);
                    $("#option_ninguno").attr('selected','selected');
                    $("#input_boss").val("");
                    $("#div_list_assignments").empty();
                    var fullname = msg.firstname + " " + msg.lastname;
                    $("#input_full_name").val(fullname);
                    $("#input_email").val(msg.email);
                }else{
                    swal("Error", "El usuario ya tiene asignado un rol que no corresponde al área socioeducativa. Revise el nombre de usuario o dirijase al área de sistemas para revisar el caso.", "error");
                }
            }
        },
        dataType: "json",
        error: function(msg){
            swal("Error", msg.error, "error");
        }});
    }
}

function getIdinstancia(){
    var urlParameters = location.search.split('&');
    
    for (x in urlParameters){
        if(urlParameters[x].indexOf('instanceid') >= 0){
            var intanceparameter = urlParameters[x].split('=');
            return intanceparameter[1];
        }
    }
    return 0;
}

function loadStudents(){
    
    var data =  new Array();
    var user_id   =  $('#input_id_user').val();
 
    data.push({name:"function",value:"load_grupal"});
    data.push({name:"user_ps_management",value:user_id});
    data.push({name:"idinstancia", value:getIdinstancia()});
   
    $.ajax({
            type: "POST",
            data: data,
            url: "../managers/seguimiento.php",
            success: function(msg){
                
                students_data = new Array();
                
                $("#div_list_assignments").empty();
    
                for(var id in msg.content){
                    temp_student = new Array();
                    temp_student.push(msg.content[id].username.substr(0, 7)); 
                    temp_student.push(msg.content[id].username.substr(8, 12)); 
                    temp_student.push(msg.content[id].firstname);
                    temp_student.push(msg.content[id].lastname);
                    temp_student.push(msg.content[id].email);
                    temp_student.push('<span id="'+msg.content[id].id+'" class="glyphicon glyphicon-remove" onclick="delete_assignment_user()"> </span>');
                    students_data.push(temp_student);
                }
                $("#div_list_assignments").append('<h1>Listado de estudiantes asignados</h1>');
                $("#div_list_assignments").append('<table id="table_students" class="col-sm-12 display" cellspacing="0" width="100%"><thead><thead></table>');
                var table = $("#table_students").DataTable({
                    data: students_data,
                    paging: false,
                    info: false,
                    filter: false,
                    columns: [{title: "Código"}, {title: "Programa"}, {title:"Nombres"}, {title:"Apellidos"}, {title:"Correo electrónico"}, {tittle: " "}],
                    language: {
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
                });
            },
            dataType: "json",
            cache: "false",
            error: function(msg){console.log(msg)},
            });
}

function disabledButtonsEdition(){
    $("#select_role").removeAttr("disabled");
    $("#button_edit_role").hide();
    $("#button_add_student").hide();
    $("#button_add_monitor").hide();
    $("#button_save").show();
    $("#button_cancel").show();
}

function cancel(currentRole){
    swal({
      title: "¿Está seguro(a) que desea cancelar?",
      text: "Los cambios no guardados se perderán.",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Si",
      cancelButtonText: "No",
      closeOnConfirm: true,
      closeOnCancel: true},
    function(isConfirm){
      if (isConfirm) {
        var currentRole = "#option_"+currentRole;
        $("".currentRole).attr('selected','selected');
        $("#select_role").prop("disabled", "disabled");
        $("#button_edit_role").show();
        $("#button_add_student").show();
        $("#button_add_monitor").show();
        $("#button_save").hide();
        $("#button_cancel").hide();
      } 
    });
}

function changeRole(){
    
}

function delete_assignment_user(idUser, role){
    if(role == "practicante_ps"){
        
    }else{
        
    }
}

function add_student(){
    alert("function");
}


    




// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module block_ases/student_profile_main
  */

 define(['jquery', 
 'block_ases/bootstrap', 
 'block_ases/d3', 
 'block_ases/sweetalert',
 'block_ases/jqueryui',
 'block_ases/select2', 
 'block_ases/Chart'], function($, bootstrap, d3, sweetalert, jqueryui, select2, Chart) {

return {
 init: function(data_init) {
     // Carga una determinada pestaña

     $('[data-toggle="tooltip"]').tooltip(); 
     
     var parameters = get_url_parameters(document.location.search);
     var panel_collapse = $('.panel-collapse.collapse.in');

     var self = this;

     // Select search
     $("#asignados").select2({
         width: 'resolve',
         height: 'resolve',
         language: {
             noResults: function() {
                 return "No hay resultado";
             },
             searching: function() {
                 return "Buscando..";
             }
         },
     });

     $("#asignados").on('change', function(){
         var code = $('#asignados').val();
         var student_code = code.split(' ')[0];

         load_student(student_code);
     });

     // Manage statuses
     for (var i = 0, len = data_init.length; i < len; i++){
         $('#select-'+data_init[i].academic_program_id+' option[value='+data_init[i].program_status+']').attr('selected', true);
         if(data_init[i].program_status == "1"){
             $('#tr-'+data_init[i].id_moodle_user).addClass('is-active');
         }
         if(data_init[i].tracking_status == "1"){
             $('#div_flags_'+data_init[i].academic_program_id).prop('checked', true);
         }
     }

     var current_tracking_status = "";

     // ******* Manage edition ******
     var height_div_cohorts = $('#div_cohorts').height();
     $('#div-icon-edit').height(height_div_cohorts);
     this.edit_profile(self);     

     $('div.slider.round').click(function(event){current_tracking_status = event.target.parentElement.children[0].checked;});

     $('.input-tracking').on('change', {current_tracking_status: current_tracking_status},
         function(){
             self.update_tracking_status(current_tracking_status, $(this), data_init, self);
         });
     
     var current_status = ""; 

     $('.select_statuses_program').on('focus', function(event){current_status = event.target.value});
     
     $('.select_statuses_program').on('change', {current_status: current_status}, 
         function(){
             self.update_status_program(current_status, $(this));
         });

     $('#icon-tracking').on('click', function(){self.update_status_ases(parameters);});
     
     switch(parameters.tab){
         case "socioed_tab":
             $('#general_li').removeClass('active');
             $('#socioed_li').addClass('active');
             $('#general_tab').removeClass('active');
             $('#socioed_tab').addClass('active');
             panel_collapse.removeClass('in');
             $('#collapseOne').addClass('in');
             break;
         default:
             panel_collapse.removeClass('in');
             break;
     }

     var modal_peer_tracking = $('#modal_peer_tracking');

     modal_manage();
     modal_peer_tracking_manage();
     modal_risk_graphic_manage();

     // Controles para el modal: "Añadir nuevo seguimiento"

     // Despliega el modal de seguimiento
     $('#button_add_track').on('click', function() {
         $('#save_seg').removeClass("hide");
         $('#div_created').addClass('hide');
         $('#upd_seg').addClass('hide');
         $('#myModalLabel').attr('name', 'PARES');

         modal_peer_tracking.show();

         init_form_tracking();
     });
     // Open set profile image form in modal
     $('#view_form_update_profile_image').on('click', function() {
         $('#profile_image_update').show();
     });
     // Save image to backend
     $('#send-profile-image').on('click', function () {
        var data = new FormData();
        data.append('mdl_user_id', $('#id_moodle').val());
        data.append('new_image_file', document.getElementById('profile-image-input').files[0]);
        data.append('func', 'update_user_image');
        $.ajax({
            url: '../managers/student_profile/studentprofile_serverproc.php',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST', // For jQuery < 1.9
            success: function(data){
                alert(data);
            },
            error: function (data) {
                console.log(data)
            }
        });
         console.log(data);
     });
     // Guardar segumiento
     $('#save_tracking_btn').on('click', function() {
         save_tracking_peer();
     });
     // Permitir hacer visibles los formularios de moodle

     $('fieldset').removeClass('hidden');
     // Controles sobre limpiar funcionario
     $('#clean_individual_risk').on('click', function(){
         $('#no_value_individual').prop('checked', true);
     });
     $('#clean_familiar_risk').on('click', function(){
         $('#no_value_familiar').prop('checked', true); 
     });
     $('#clean_academic_risk').on('click', function(){
         $('#no_value_academic').prop('checked', true);
     });
     $('#clean_economic_risk').on('click', function(){
         $('#no_value_economic').prop('checked', true);
     });
     $('#clean_life_risk').on('click', function(){
         $('#no_value_life').prop('checked', true); 
     });

     // Controles para editar formulario de pares
     $('.btn-primary.edit_peer_tracking').on('click', function(){
         var id_button = $(this).attr('id');
         var id_tracking = id_button.substring(14);
         
         load_tracking_peer(id_tracking);           
     });

     $('.btn-danger.delete_peer_tracking').on('click', function(){
         var id_button = $(this).attr('id');
         var id_tracking = id_button.substring(21);

         swal({
             title: "Advertencia",
             text: "¿Está seguro(a) que desea borrar este seguimiento?",
             type: "warning",
             showCancelButton: true,
             confirmButtonText: "Si",
             cancelButtonText: "No",
             closeOnConfirm: false,
         },
         function(isConfirm){

             if (isConfirm) {
                 delete_tracking_peer(id_tracking);
             } 
         });
     });

     // Despliega el modal de seguimiento v2
     //Se mueve a dphpforms_form_renderer.js

     $('#view_graph_radial_button').on('click', function(){
        self.graph_radial();
     });
   
     $('#view_graphic_risk_button').click(function(){
         $('#modal_riesgos').fadeIn(200);
         graficar();
     });

     $('#mymodal-riesgo-close').click(function(){
         $('#modal_riesgos').fadeOut(200);
     });

 },equalize: function(){
     $( document ).ready(function() {
         var heights = $(".equalize").map(function() {
             return $(this).height();
         }).get(),
     
         maxHeight = Math.max.apply(null, heights);
     
         $(".equalize").height(maxHeight);
     });
 },update_status_program: function(current_status, element){

     var program_id = element.attr('id').split("-")[1];
     var status = element.val();
     var student_id = element.parent().parent().attr('id').split("-")[1];

     var data = {
         func: 'update_status_program',
         program_id: program_id,
         status: status,
         student_id: student_id
     };

     swal({
         title: "Advertencia",
         text: "¿Está seguro que desea cambiar el estado del programa?",
         type: "warning",
         showCancelButton: true,
         confirmButtonClass: "btn-danger",
         confirmButtonText: "Si",
         cancelButtonText: "No",
         closeOnConfirm: false
     },
     function(isConfirm){
         if(isConfirm){
             $.ajax({
                 type: "POST",
                 data: data,
                 url: "../managers/student_profile/studentprofile_serverproc.php",
                 success: function(msg) {
                     if($('#select-'+data.program_id).val() == "ACTIVO"){
                         $('#tr-'+data.student_id).addClass('is-active');
                     }else{
                         $('#tr-'+data.student_id).removeClass('is-active');
                     }
                     swal(
                         msg.title,
                         msg.msg,
                         msg.status
                     );
                 },
                 dataType: "json",
                 cache: "false",
                 error: function(msg) {
                     swal(
                         msg.title,
                         msg.msg,
                         msg.status
                     );
                 },
             });
         }else{
             $('#select-'+data.program_id).val(current_status);
         }
        
     });      
 },update_status_ases: function(parameters_url){

     var current_status = $('#input_status_ases').val(); 
     var new_status;

     // Se configura el nuevo estado a partir del estado actual
     switch(current_status){
         case 'seguimiento':
             new_status = 'sinseguimiento'
             break;
         case 'sinseguimiento':
             new_status = 'seguimiento';
             break;
         case 'noasignado':
             new_status = 'seguimiento';
             break;
         default:
     }

     if(current_status == 'sinseguimiento'){
         swal(
             'Advertencia',
             'No es posible realizar esta acción. Al estudiante se le realiza seguimiento desde otra instancia.',
             'warning'
         );

     }else{
         var data = {
             func: 'update_status_ases',
             current_status: current_status,
             new_status: new_status,
             instance_id: parameters_url.instanceid,
             code_student: parameters_url.student_code
         };

         swal({
             title: "Advertencia",
             text: "¿Está seguro que desea cambiar el estado de seguimiento del estudiante en esta instancia? El estado pasará de '"+current_status+"' a '"+new_status+"'",
             type: "warning",
             showCancelButton: true,
             confirmButtonClass: "btn-danger",
             confirmButtonText: "Si",
             cancelButtonText: "No",
         },
         function(isConfirm){
             if(isConfirm){
                 if(new_status == 'sinseguimiento'){

                     modal_dropout = $('#modal_dropout');
                     modal_dropout.show();

                     $('#save_changes_dropout').on('click', function(){
                         data.id_reason_dropout = $('#reasons_select').val();
                         data.observation = $('#description_dropout').val();

                         $.ajax({
                             type: "POST",
                             data: data,
                             url: "../managers/student_profile/studentprofile_serverproc.php",
                             success: function(msg) {

                                 current_status = new_status;
                                 $('#input_status_ases').val(new_status);
     
                                 if(msg.previous_status == 'seguimiento'){
                                     $('#icon-tracking').removeClass('i-tracking-t');
                                     $('#icon-tracking').addClass('i-tracking-n');
                                     $('#tip_ases_status').html('Se realiza seguimiento en otra instancia');
                                 }else if(msg.previous_status == 'sinseguimiento'){
                                     $('#icon-tracking').removeClass('i-tracking-f');
                                     $('#icon-tracking').addClass('i-tracking-t');
                                     $('#tip_ases_status').html('Se realiza seguimiento en esta instancia');
                                 }else if(msg.previous_status == ''){
                                     $('#icon-tracking').removeClass('i-tracking-n');
                                     $('#icon-tracking').addClass('i-tracking-t');
                                     $('#tip_ases_status').html('No se realiza seguimiento');
                                 }

                                 modal_dropout.hide();
     
                                 swal(
                                     msg.title,
                                     msg.msg,
                                     msg.status
                                 );
                             },
                             dataType: "json",
                             cache: "false",
                             error: function(msg) {
                                 modal_dropout.hide();
                                 swal(
                                     msg.title,
                                     msg.msg,
                                     msg.status
                                 );
                             },
                         });
                     });
                 }else if(new_status == 'seguimiento'){
                     $.ajax({
                         type: "POST",
                         data: data,
                         url: "../managers/student_profile/studentprofile_serverproc.php",
                         success: function(msg) {

                             current_status = new_status;
                             $('#input_status_ases').val(new_status);
 
                             if(msg.previous_status == 'seguimiento'){
                                 $('#icon-tracking').removeClass('i-tracking-t');
                                 $('#icon-tracking').addClass('i-tracking-n');
                                 $('#tip_ases_status').html('Se realiza seguimiento en otra instancia');
                             }else if(msg.previous_status == 'sinseguimiento'){
                                 $('#icon-tracking').removeClass('i-tracking-f');
                                 $('#icon-tracking').addClass('i-tracking-t');
                                 $('#tip_ases_status').html('Se realiza seguimiento en esta instancia');
                             }else if(msg.previous_status == ''){
                                 $('#icon-tracking').removeClass('i-tracking-n');
                                 $('#icon-tracking').addClass('i-tracking-t');
                                 $('#tip_ases_status').html('Se realiza seguimiento en esta instancia');
                             }
                         },
                         dataType: "json",
                         cache: "false",
                         error: function(msg) {
                             console.log(msg);
                         },
                     }).then(function(msg){
                         swal(
                             msg.title,
                             msg.msg,
                             msg.status
                         );
                     });
                 }
             }
         });
     }

     
 },update_tracking_status: function(current_status, element, data_init, object_function){

     has_tracking_status = false;

     if(current_status == false){

         has_tracking_status = object_function.validate_tracking_statuses(data_init);

         if(has_tracking_status.tracking_status){
             swal({
                 title: "¿Está seguro/a de cambiar el estado?",
                 text: "Se alternará el perfil de Moodle asociado al estudiante al cual se el realiza seguimiento",
                 type: "warning",
                 showCancelButton: true,
                 confirmButtonColor: "#d51b23",
                 confirmButtonText: "Si",
                 cancelButtonText: "No",
                 closeOnConfirm: true,
                 allowEscapeKey: false
             },
             function(isConfirm) {
                 if (isConfirm) {
                    $('.input-tracking').prop('checked', false);
                    element.prop('checked', true);

                    var id_ases_student = $('#id_ases').val();
                    var id_academic_program = element[0].id;
                    id_academic_program = id_academic_program.split("_")[2];

                    $.ajax({
                        type: "POST",
                        data: {
                            func: 'update_tracking_status',
                            id_ases_student: id_ases_student,
                            id_academic_program: id_academic_program
                        },
                        url: "../managers/student_profile/studentprofile_serverproc.php",
                        success: function(msg) {
                            setTimeout(function(){
                                swal(
                                    msg.title,
                                    msg.msg,
                                    msg.status
                                );
                            }, 100);
                        },
                        dataType: "json",
                        cache: "false",
                        error: function(msg) {
                            setTimeout(function(){
                                swal(
                                    msg.title,
                                    msg.msg,
                                    msg.status
                                );
                            }, 100);
                        },
                    });
                 }
                 else {
                     if(current_status){
                         element.prop('checked', true);
                     }else{
                         element.prop('checked', false);
                     }
                 }
             });
         }

     }else{
         element.prop('checked', true);
     }

 },validate_tracking_statuses: function(data_init){

     has_tracking_status = false;

     for(i = 0; i < data_init.length; i++){
         if(data_init[i].tracking_status == 1){
             has_tracking_status = true;
             break;
         }
     }

     return data_init[i];
 },get_url_parameters: function(page){
     var query_string = [];
     var query = document.location.search.substring(1);
     var vars = query.split("&");
     for (var i = 0; i < vars.length; i++) {
         var pair = vars[i].split("=");
         query_string[pair[0]] = pair[1];
     }

     return query_string;
 },graph_radial: function(){
    
    var ctx = document.getElementById('canvas_radial_graph').getContext('2d');
    
    var individual_level;
    var familiar_level;
    var academic_level;
    var economic_level;
    var life_level; 

    if($('#individual_risk').attr('data-risk-level') == 0){
        individual_level = 4;
    }else{
        individual_level = $('#individual_risk').attr('data-risk-level');
    }

    if($('#familiar_risk').attr('data-risk-level') == 0){
        familiar_level = 4;
    }else{
        familiar_level = $('#familiar_risk').attr('data-risk-level');
    }

    if($('#academic_risk').attr('data-risk-level') == 0){
        academic_level = 4;
    }else{
        academic_level = $('#academic_risk').attr('data-risk-level');
    }

    if($('#economic_risk').attr('data-risk-level') == 0){
        economic_level = 4;
    }else{
        economic_level = $('#economic_risk').attr('data-risk-level');
    }

    if($('#life_risk').attr('data-risk-level') == 0){
        life_level = 4;
    }else{
        life_level = $('#life_risk').attr('data-risk-level');
    }
     
     $('#modal_radial_graph').fadeIn(200);

     var data = {
        labels: ['Individual', 'Familiar', 'Académico', 'Económico', 'Vida universitaria'],
        datasets: [{
            data: [4 - individual_level, 4 - familiar_level, 4 - academic_level, 4 - economic_level, 4 - life_level],
            backgroundColor: 'rgba(255, 99, 132, 0.4)',
            borderColor: ['rgba(255,99,132,1)'],
            borderWidth: 2,
            fontsize: 12
        }]
    }

    var chart_options = {
        scale: {
          ticks: {
            beginAtZero: true,
            min: 0,
            max: 3,
            stepSize: 1,
            display: false
          },
          pointLabels: {
            fontSize: 12
          },
          scaleLabel:{
            fontsize: 10
          },
        },
        legend: {
          display: false
        }
    };

    var radar_chart = new Chart(ctx, {
        type: 'radar',
        data: data,
        options: chart_options
    });   
 },edit_profile: function(object_function){

    var form_wihtout_changes = $('#ficha_estudiante').serializeArray();
    
    $('#span-icon-edit').on('click', function(){
        $(this).hide();
        $('#tip-edit').hide();
        $('#span-icon-save-profile').show();
        $('#tip-save').show();
        $('#span-icon-cancel-edit').show();
        $('#tip-cancel').show();
        $('#tipo_doc').prop('disabled', false);
        $('#num_doc').prop('readonly', false);
        $('#icetex_status').prop('disabled', false);
        $('#observacion').prop('readonly', false);
        $('.select_statuses_program').prop('disabled', false);
        $('.input_fields_general_tab').prop('readonly', false);
        $('.input-tracking').prop('disabled', false);
    });

    $('#span-icon-cancel-edit').on('click', {form: form_wihtout_changes}, function(data){
        swal({
            title: "¿Desea cancelar la edición?",
            text: "Los cambios no guardados se perderán",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d51b23",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm: true,
            allowEscapeKey: false

        },
        function(isConfirm) {
            if (isConfirm) {
                object_function.cancel_edition();
                object_function.revert_changes(data.data.form);
            }
        });        
    });

    $('#span-icon-save-profile').on('click', function(){
        
        var form_with_changes = $('#ficha_estudiante').serializeArray();

        var result_validation = object_function.validate_form(form_with_changes);

        if(result_validation.status == "error"){
            swal(result_validation.title,
                 result_validation.msg,
                 result_validation.status);
        }else{
            object_function.save_form_edit_profile(form_with_changes, object_function);
        }
    });    
 },validate_form: function(form){
    
    for(field in form){
        
        var msg = new Object();

        msg.title = "Éxito";
        msg.msg = "El formulario fue validado con éxito";
        msg.status = "success";

        switch(form[field].name){
            case "num_doc":
            case "tel_res":
            case "celular":
            case "tel_acudiente":
                if(has_letters(form[field].value)){
                    msg.title = "Error";
                    msg.status = "error";
                    msg.msg = "El campo "+form[field].name+" solo debe contener números";
                    return msg;
                };
                break;
            case "emailpilos":
                console.log(is_email(form[field].value));
                if(!is_email(form[field].value)){
                    msg.title = "Error";
                    msg.status = "error";
                    msg.msg = "El campo "+form[field].name+" no tiene el formato de un correo electrónico";
                    return msg;
                };
                break;
        };
    };

    return msg;
 },save_form_edit_profile: function(form, object_function){

    $.ajax({
        type: "POST",
        data: {
            func: 'save_profile',
            form: form
        },
        url: "../managers/student_profile/studentprofile_serverproc.php",
        success: function(msg) {

            swal(
                msg.title,
                msg.msg,
                msg.status
            );
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            swal(
                msg.title,
                msg.msg,
                msg.status
            );
        },
    });

    object_function.cancel_edition();

 },cancel_edition: function(){

    // Deshabilitar campos para el ingreso de datos

    $('#span-icon-cancel-edit').hide();
    $('#tip-cancel').hide();
    $('#span-icon-save-profile').hide();
    $('#tip-save').hide();
    $('#span-icon-edit').show();
    $('#tip-edit').show();
    $('#tipo_doc').prop('disabled', true);
    $('#num_doc').prop('readonly', true);
    $('#icetex_status').prop('disabled', true);
    $('#observacion').prop('readonly', true);
    $('.select_statuses_program').prop('disabled', true);
    $('.input_fields_general_tab').prop('readonly', true);
    $('.input-tracking').prop('disabled', true);

 },revert_changes: function(form){
    // Revertir cualquier cambio después de cancelar la edición
    for(field in form){
        $('#'+form[field].name).val(form[field].value);
    }
 }
};


// Funciones para la validación de formularios
function has_letters(str) {
 var letters = "abcdefghyjklmnñopqrstuvwxyz";
 str = str.toLowerCase();
 for (i = 0; i < str.length; i++) {
     if (letters.indexOf(str.charAt(i), 0) != -1) {
         return 1;
     }
 }
 return 0;
}

function has_numbers(str) {
 var numbers = "0123456789";
 for (i = 0; i < str.length; i++) {
     if (numbers.indexOf(str.charAt(i), 0) != -1) {
         return 1;
     }
 }
 return 0;
}

function is_email(str) {
 var email_regex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
 if (email_regex.test(str)) {
     return 1;
 }
 else {
     return 0;
 }
}

function clean_modal_dropout(){
 $('#description_dropout').val('');
 $('#no_reason_option').attr("selected", "selected");
}

// Funciones para la administración de estados
function manage_icetex_status() {
 //validar cambio en estado
 var previous;
 $('#estado').on('focus', function() {
     // se guarda el valor previo con focus
     previous = $('#estado option:selected').text();
 }).change(function() {
     var newstatus = $('#estado option:selected').text();

     if(newstatus == "RETIRADO"){

         $('#modal_dropout').show();

         $('#save_changes_dropout').click(function(){
             if($('reasons_select').val() == ''){
                 swal({
                     title: "Error",
                     text: "Seleccione un mótivo",
                     type: "error"
                 });
             }else{
                 save_icetex_status();
             }
         });

     }else if(newstatus == "APLAZADO"){

         $('#modal_dropout').show();

         $('#save_changes_dropout').click(function(){
             if($('reasons_select').val() == ''){
                 swal({
                     title: "Error",
                     text: "Seleccione un mótivo",
                     type: "error"
                 });
             }else{
                 save_icetex_status();
             }
         });

     }else if(newstatus == "NO REGISTRA"){
         swal({
             title: "Error",
             text: "Por favor seleccione un Estado Ases.",
             type: "error"
         });
     }else{
         swal({
                 title: "¿Está seguro/a de realizar este cambio?",
                 text: "El estado Icetex del estudiante pasará de " + previous + " a " + newstatus,
                 type: "warning",
                 showCancelButton: true,
                 confirmButtonColor: "#d51b23",
                 confirmButtonText: "Si",
                 cancelButtonText: "No",
                 closeOnConfirm: true,
                 allowEscapeKey: false
             },
             function(isConfirm) {
                 if (isConfirm) {

                     var result_status = save_icetex_status();

                     swal(
                         result_status.title,
                         result_status.msg,
                         resul_status.status);
                 }
                 else {
                     $('#estado').val(previous);
                 }
         });
     }
 });
}

function manage_ases_status() {
 //validar cambio en estado
 var previous;
 $('#estadoAses').on('focus', function() {
     // se guarda el valor previo con focus
     previous = $('#estadoAses option:selected').text();
 }).change(function() {
     var newstatus = $('#estadoAses option:selected').text();

     if (newstatus == "RETIRADO") {
         $('#modal_dropout').show();

         $('#save_changes_dropout').click(function(){
             if($('reasons_select').val() == ''){
                 swal({
                     title: "Error",
                     text: "Seleccione un mótivo",
                     type: "error"
                 });
             }else{
                 save_ases_status();
             }
         });
     }else if (newstatus == "APLAZADO") {
         $('#modal_dropout').show();

         $('#save_changes_dropout').click(function(){
             if($('reasons_select').val() == ''){
                 swal({
                     title: "Error",
                     text: "Seleccione un mótivo",
                     type: "error"
                 });
             }else{
                 save_ases_status();
             }
         });

     }else if(newstatus == "NO REGISTRA"){
         swal({
             title: "Error",
             text: "Por favor seleccione un Estado Ases.",
             type: "error"
         });
     }else{
         swal({
                 title: "¿Está seguro/a de realizar este cambio?",
                 text: "El estado ASES del estudiante pasará de " + previous + " a " + newstatus,
                 type: "warning",
                 showCancelButton: true,
                 confirmButtonColor: "#d51b23",
                 confirmButtonText: "Si",
                 cancelButtonText: "No",
                 closeOnConfirm: true,
                 allowEscapeKey: false
             },
             function(isConfirm) {
                 if (isConfirm) {

                     var result_status = save_ases_status();

                     swal(
                         result_status.title,
                         result_status.msg,
                         resul_status.status);
                 }
                 else {
                     $('#estadoAses').val(previous);
                 }
         });
     }
 });
}

function save_icetex_status() {
 var data = new Array();
 var new_status = $('#estado').val();
 var id_ases = $('#id_ases').val();
 var id_reason = $('#reasons_select').val();
 var reasons_dropout = $('#description_dropout').val();

 data.push({
     name: "func",
     value: "save_icetex_status"
 });
 data.push({
     name: 'new_status',
     value: new_status
 });
 data.push({
     name: 'id_ases',
     value: id_ases
 });

 if(id_reason != ''){
     data.push({
         name: 'id_reason',
         value: id_reason
     });
 };

 if(reasons_dropout != ''){
     data.push({
         name: 'observations',
         value: reasons_dropout
     });
 }

 $.ajax({
     type: "POST",
     data: data,
     url: "../../ases/managers/student_profile/studentprofile_serverproc.php",
     success: function(msg) {
         swal({
             title: msg.title,
             text: msg.msg,
             type: msg.type
         });
         $('#modal_dropout').hide();
         clean_modal_dropout();
     },
     dataType: "json",
     cache: "false",
     error: function(msg) {
         swal(
             'Error',
             'No se puede contactar con el servidor.',
             'error'
         );
     },
 });
}

function save_ases_status() {
 var data = new Array();
 var new_status = $('#estadoAses').val();
 var id_ases = $('#id_ases').val();
 var id_reason = $('#reasons_select').val();
 var reasons_dropout = $('#description_dropout').val();

 data.push({
     name: "func",
     value: "save_ases_status"
 });
 data.push({
     name: 'new_status',
     value: new_status
 });
 data.push({
     name: 'id_ases',
     value: id_ases
 });

 if(id_reason != ''){
     data.push({
         name: 'id_reason',
         value: id_reason
     });
 };

 if(reasons_dropout != ''){
     data.push({
         name: 'observations',
         value: reasons_dropout
     });
 }

 $.ajax({
     type: "POST",
     data: data,
     url: "../../ases/managers/student_profile/studentprofile_serverproc.php",
     success: function(msg) {
         swal({
             title: msg.title,
             text: msg.msg,
             type: msg.type
         });
         $('#modal_dropout').hide();
         clean_modal_dropout();
     },
     dataType: "json",
     cache: "false",
     error: function(msg) {
         swal(
             'Error',
             'El estado no fue actualizado. Error al contactarse con el servidor.',
             'error');
     },
 });
}

//Funciones para el manejo de los modales

function modal_manage() {

 // Get the modal
 var modal = $('#modal_dropout');

 // Get the <span> element that closes the modal
 var span_close = $('.mymodal-close');
 var goback_backdrop = $('#goback_backdrop');

 goback_backdrop.on('click', function() {
     modal.hide();
 });

 // When the user clicks on <span> (x), close the modal
 span_close.on('click', function() {
     modal.hide();
 });
}

function modal_peer_tracking_manage() {
 // Get the modal
 var modal_peer_tracking = $('#modal_peer_tracking');

 // Get the <span> element that closes the modal
 var span_close = $('.mymodal-close');
 var cancel_button = $('#cancel_peer_tracking');

 cancel_button.on('click', function() {
     modal_peer_tracking.hide();
 });

 // When the user clicks on <span> (x), close the modal
 span_close.on('click', function() {
     modal_peer_tracking.hide();
 });

 var panel_heading = $('.panel-heading.heading_semester_tracking');



 panel_heading.on('click', function(){
     if($(this).parent().attr('class') == 'collapsed'){
         $('h4>span', this).removeClass('glyphicon-chevron-left');
         $('h4>span', this).addClass('glyphicon-chevron-down');
     }else{
         $('h4>span', this).removeClass('glyphicon-chevron-down');
         $('h4>span', this).addClass('glyphicon-chevron-left');
     }
     
 });
}

function modal_risk_graphic_manage(){

 var span_close = $('#modal_risk_span');
 var button_close = $('#modal_risk_close');

 span_close.on('click', function(){
     $('#modal_risk_graph').hide();
 });

 button_close.on('click', function(){
     $('#modal_risk_graph').hide();
 });

}

function init_form_tracking() {

 $('#date').datepicker({dateFormat: "yy-mm-dd"});

 var current_date = new Date();
 var current_day = current_date.getDate();
 var current_month = current_date.getMonth() + 1;
 var current_year = current_date.getFullYear();
 var current_min = current_date.getMinutes();
 var current_hour = current_date.getHours();

 //incializar hora
 var hour = "";
 for (var i = 0; i < 24; i++) {
     if (i == current_hour) {
         if (current_hour < 10) {
             current_hour = "0" + current_hour;
         }
         hour += "<option value=\"" + current_hour + "\" selected>" + current_hour + "</option>";
     }
     else if (i < 10) {
         hour += "<option value=\"0" + i + "\">0" + i + "</option>";
     }
     else {
         hour += "<option value=\"" + i + "\">" + i + "</option>";
     }
 }

 var min = "";
 for (var i = 0; i < 60; i++) {
     if (i == current_min) {
         if (current_min < 10) {
             current_min = "0" + current_min;
         }
         min += "<option value=\"" + current_min + "\" selected>" + current_min + "</option>";
     }
     else if (i < 10) {
         min += "<option value=\"0" + i + "\">0" + i + "</option>";
     }
     else {
         min += "<option value=\"" + i + "\">" + i + "</option>";
     }
 }

 $('#h_ini').append(hour);
 $('#m_ini').append(min);
 $('#h_fin').append(hour);
 $('#m_fin').append(min);
 $("#infomonitor").addClass('hide');
 $("#seguimiento").find("input:text, textarea").val('');
 $("#seguimiento").find("input:radio,input:checkbox").prop('checked', false);
 $('#upd_seg').attr('disabled', false);
 $('#upd_seg').attr('title', '');
 $('#seguimiento').find('select, textarea, input').attr('disabled', false);
 $('#no_value_individual').hide();
 $('#no_value_familiar').hide();
 $('#no_value_academic').hide();
 $('#no_value_economic').hide();
 $('#no_value_life').hide();

 $('#id_tracking_peer').val("");
 $('#place').val("");
 $('#topic_textarea').val("");
 $('#objetivos').val("");
 $('#individual').val("");
 $('#familiar').val("");
 $('#academico').val("");
 $('#economico').val("");
 $('#vida_uni').val("");
 $('#observaciones').val("");

 $('#no_value_individual').prop('checked', true);
 $('#no_value_familiar').prop('checked', true); 
 $('#no_value_academic').prop('checked', true);
 $('#no_value_economic').prop('checked', true);
 $('#no_value_life').prop('checked', true); 
}

function save_tracking_peer() {
 var form = $('#tracking_peer_form');
 var modal_peer_tracking = $('#modal_peer_tracking');
 var data = form.serializeArray();

 var url_parameters = get_url_parameters(document.location.search);

 var result_validation = validate_tracking_peer_form(data);

 if (result_validation != "success") {
     swal({
         title: 'Advertencia',
         text: result_validation,
         type: 'warning',
         html: true
     });
 }else{
     data.push({
         name: "func",
         value: "save_tracking_peer"
     });

     var id_ases = $('#id_ases').val();

     data.push({
         name: "id_ases",
         value: id_ases
     });
     data.push({
         name: "id_instance",
         value: url_parameters.instanceid
     });

     $.ajax({
         type: "POST",
         data: data,
         url: "../../ases/managers/student_profile/studentprofile_serverproc.php",
         success: function(msg) {
             swal({
                 title: msg.title,
                 text: msg.msg,
                 type: msg.type
             },function () {
                 modal_peer_tracking.hide();
                 var parameters = get_url_parameters(document.location.search);

                 if(parameters.tab){
                     location.reload();
                 }else{
                     location.href = location.search + "&tab=socioed_tab";
                 }
             });
         },
         dataType: "json",
         cache: "false",
         error: function(msg) {
             console.log(msg);
         },
     });
 }
}

function validate_tracking_peer_form(form) {

 var regexp = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;
 
 var validate_date = regexp.exec(form[1].value);       
 
 // Validación de los datos generales
 if (form[1].value == "") {
     return "Debe introducir la fecha en la cual se realizó el seguimiento";
 }else if(validate_date === null){    
     return "La fecha no sigue el patrón yyyy-mm-dd. Ejemplo 2017-10-13";
 }
 else if (form[2].value == "") {
     return "Debe introducir el lugar donde se realizó el seguimiento";
 }
 else if (form[7].value == "") {
     return "El campo tema se encuentra vacio";
 }
 else if (form[8].value == "") {
     return "El campo objetivos se encuentra vacio";
 }

 //Validación de la hora
 else if (parseInt(form[5].value) < parseInt(form[3].value)) {
     return "La hora de finalización debe ser mayor a la hora inicial";
 }
 else if ((parseInt(form[3].value) == parseInt(form[5].value)) && (parseInt(form[6].value) <= parseInt(form[4].value))) {
     return "La hora de finalización debe ser mayor a la hora inicial";
 }

 // Validación actividades

 // Individual campo
 else if (form[9].value != "" && form[10].value == 0) {
     return "El riesgo asociado al campo Actividad Invidual no está marcado. Si usted escribió información en el campo Actividad Individual debe marcar un nivel de riesgo.";
 }
 // Familiar campo
 else if (form[11].value != "" && form[12].value == 0) {
     return "El riesgo asociado al campo Actividad Familiar no está marcado. Si usted escribió información en el campo Actividad Familiar debe marcar un nivel de riesgo.";
 }
 // Académico campo
 else if(form[13].value != "" && form[14].value == 0){
     return "El riesgo asociado al campo Actividad Académico no está marcado. Si usted escribió información en el campo Actividad Académico debe marcar un nivel de riesgo.";
 }
 // Económico campo
 else if(form[15].value != "" && form[16].value == 0){
     return "El riesgo asociado al campo Actividad Económico no está marcado. Si usted escribió información en el campo Actividad Económico debe marcar un nivel de riesgo.";
 }
 // Vida universitaria y ciudad campo
 else if(form[17].value != "" && form[18].value == 0){
     return "El riesgo asociado al campo Actividad Vida Universitaria no está marcado. Si usted escribió información en el campo Actividad Vida Universitaria debe marcar un nivel de riesgo.";
 }
 // Individual riesgo
 else if(form[9].value == "" && form[10].value != 0){
     return "Usted ha marcado el nivel de riesgo asociado a la actividad Individual, debe digitar información en el campo Actividad Individual. O puede utilizar el icono (<span style='color:gray;' class='glyphicon glyphicon-erase'></span>) de limpiar riesgo.";
 }
 // Familiar riesgo
 else if(form[11].value == "" && form[12].value != 0){
     return "Usted ha marcado el nivel de riesgo asociado a la actividad Familiar, debe digitar información en el campo Actividad Familiar. O puede utilizar el icono (<span style='color:gray;' class='glyphicon glyphicon-erase'></span>) de limpiar riesgo.";
 }
 // Académico riesgo
 else if(form[13].value == "" && form[14].value != 0){
     return "Usted ha marcado el nivel de riesgo asociado a la actividad Académico, debe digitar información en el campo Actividad Académico. O puede utilizar el icono (<span style='color:gray;' class='glyphicon glyphicon-erase'></span>) de limpiar riesgo.";
 }
 // Económico riesgo
 else if(form[15].value == "" && form[16].value != 0){
     return "Usted ha marcado el nivel de riesgo asociado a la actividad Económico, debe digitar información en el campo Actividad Económico. O puede utilizar el icono (<span style='color:gray;' class='glyphicon glyphicon-erase'></span>) de limpiar riesgo.";
 }
 // Vida universitaria y ciudad riesgo
 else if(form[17].value == "" && form[18].value != 0){
     return "Usted ha marcado el nivel de riesgo asociado a la actividad Vida Universitaria, debe digitar información en el campo Actividad Vida Universitaria. O puede utilizar el icono (<span style='color:gray;' class='glyphicon glyphicon-erase'></span>) de limpiar riesgo.";
 }
 // Éxito
 else {
     return "success";
 }
}

function get_url_parameters(page) {
 // This function is anonymous, is executed immediately and
 // the return value is assigned to QueryString!
 var query_string = [];
 var query = document.location.search.substring(1);
 var vars = query.split("&");
 for (var i = 0; i < vars.length; i++) {
     var pair = vars[i].split("=");
     query_string[pair[0]] = pair[1];
 }

 return query_string;
}

//Functions for edit tracking peer

function load_tracking_peer(id_tracking){

 init_form_tracking();

 var date_tracking_peer = new Date($('#'+id_tracking+' .date_tracking_peer').text());
 var place_tracking_peer =  $('#'+id_tracking+' .place_tracking_peer').text();
 var init_time_tracking_peer =  $('#'+id_tracking+' .init_time_tracking_peer').text();
 var ending_time_tracking_peer =  $('#'+id_tracking+' .ending_time_tracking_peer').text();
 var topic_tracking_peer =  $('#'+id_tracking+' .topic_tracking_peer').text();
 var objectives_tracking_peer =  $('#'+id_tracking+' .objectives_tracking_peer').text();
 var individual_tracking_peer =  $('#'+id_tracking+' .individual_tracking_peer').text();
 var ind_risk_tracking_peer =  $('#'+id_tracking+' .ind_risk_tracking_peer').text();
 var familiar_tracking_peer = $('#'+id_tracking+' .familiar_tracking_peer').text();
 var fam_risk_tracking_peer = $('#'+id_tracking+' .fam_risk_tracking_peer').text();
 var academico_tracking_peer = $('#'+id_tracking+' .academico_tracking_peer').text();
 var aca_risk_tracking_peer = $('#'+id_tracking+' .aca_risk_tracking_peer').text();
 var economico_tracking_peer = $('#'+id_tracking+' .economico_tracking_peer').text();
 var econ_risk_tracking_peer = $('#'+id_tracking+' .econ_risk_tracking_peer').text();
 var lifeu_tracking_peer = $('#'+id_tracking+' .lifeu_tracking_peer').text();
 var lifeu_risk_tracking_peer = $('#'+id_tracking+' .lifeu_risk_tracking_peer').text();
 var observations_tracking_peer = $('#'+id_tracking+' .observations_tracking_peer').text();

 var enum_risk = new Object();

 enum_risk.bajo = 1;
 enum_risk.medio = 2;
 enum_risk.alto = 3;

 //Fecha

 var date = date_tracking_peer.getFullYear();
 var month = date_tracking_peer.getMonth() + 1;

 if(date_tracking_peer.getMonth() < 10){
     date += '-0' + month;
 }else{
     date += '-' + month;
 }

 if(date_tracking_peer.getDate() < 10){
     date += '-0' + date_tracking_peer.getDate();
 }else{
     date += '-' + date_tracking_peer.getDate();
 }

 //Hora

 var hour_ini = init_time_tracking_peer.substring(0,2);
 var min_ini = init_time_tracking_peer.substring(3,5);
 var hour_end = ending_time_tracking_peer.substring(0,2);
 var min_end = ending_time_tracking_peer.substring(3,5);

 $('#h_ini option[value="' + hour_ini + '"]').attr("selected", true);
 $('#m_ini option[value="'+ min_ini +'"]').attr("selected", true);
 $('#h_fin option[value="'+ hour_end +'"]').attr("selected", true);
 $('#m_fin option[value="'+ min_end +'"]').attr("selected", true);

 //Riesgos

 var individual_risk = enum_risk[ind_risk_tracking_peer.toLowerCase()];
 var familiar_risk = enum_risk[fam_risk_tracking_peer.toLowerCase()];
 var economic_risk = enum_risk[econ_risk_tracking_peer.toLowerCase()];
 var academic_risk = enum_risk[aca_risk_tracking_peer.toLowerCase()];
 var lifeu_risk = enum_risk[lifeu_risk_tracking_peer.toLowerCase()];

 if(ind_risk_tracking_peer != ""){
     $("input[name='riesgo_ind'][value='"+individual_risk+"']").prop('checked', true);
 }else{
     $("input[name='riesgo_ind'][value='0']").prop('checked', true);
 }

 if(fam_risk_tracking_peer != ""){
     $("input[name='riesgo_familiar'][value='"+familiar_risk+"']").prop('checked', true);
 }else{
     $("input[name='riesgo_familiar'][value='0']").prop('checked', true);
 }

 if(econ_risk_tracking_peer != ""){
     $("input[name='riesgo_econom'][value='"+economic_risk+"']").prop('checked', true);
 }else{
     $("input[name='riesgo_econom'][value='0']").prop('checked', true);
 }

 if(aca_risk_tracking_peer != ""){
     $("input[name='riesgo_aca'][value='"+academic_risk+"']").prop('checked', true);
 }else{
     $("input[name='riesgo_aca'][value='0']").prop('checked', true);
 }

 if(lifeu_risk_tracking_peer != ""){
     $("input[name='riesgo_uni'][value='"+lifeu_risk+"']").prop('checked', true);
 }else{
     $("input[name='riesgo_uni'][value='0']").prop('checked', true);
 }

 $('#date').val(date);
 $('#place').val(place_tracking_peer);
 $('#topic_textarea').val(topic_tracking_peer);
 $('#objetivos').val(objectives_tracking_peer);
 $('#individual').val(individual_tracking_peer);
 $('#familiar').val(familiar_tracking_peer);
 $('#academico').val(academico_tracking_peer);
 $('#economico').val(economico_tracking_peer);
 $('#vida_uni').val(lifeu_tracking_peer);
 $('#observaciones').val(observations_tracking_peer);
 $('#id_tracking_peer').val(id_tracking);

 var modal_peer_tracking = $('#modal_peer_tracking');

 modal_peer_tracking.show();

}

function delete_tracking_peer(id_tracking){

$.ajax({
         type: "POST",
         data: {
             func: 'delete_tracking_peer',
             id_tracking: id_tracking
         },
         url: "../managers/student_profile/studentprofile_serverproc.php",
         success: function(msg) {

             swal(
                 msg.title,
                 msg.msg,
                 msg.status
             );

             var parameters = get_url_parameters(document.location.search);

                 if(parameters.tab){
                     location.reload();
                 }else{
                     location.href = location.search + "&tab=socioed_tab";
                 }
         },
         dataType: "json",
         cache: "false",
         error: function(msg) {
             swal(
                 msg.title,
                 msg.msg,
                 msg.status
             );
         },
     });
}

function load_risk_values() {
 var idUser = $('#idtalentos').val();

 $.ajax({
     type: "POST",
     data: {
         id: idUser
     },
     url: "../managers/get_risk.php",
     success: function(msg) {

         if (msg.individual) {
             var individual_r = parseInt(msg.individual.calificacion_riesgo);
         }
         else {
             var individual_r = 0;
         }

         if (msg.familiar) {
             var familiar_r = parseInt(msg.familiar.calificacion_riesgo);
         }
         else {
             var familiar_r = 0;
         }

         if (msg.economico) {
             var economic_r = parseInt(msg.economico.calificacion_riesgo);
         }
         else {
             var economic_r = 0;
         }

         if (msg.academico) {
             var academic_r = parseInt(msg.academico.calificacion_riesgo);
         }
         else {
             var academic_r = 0;
         }

         if (msg.vida_universitaria) {
             var life_risk = parseInt(msg.vida_universitaria.calificacion_riesgo);
         }
         else {
             var life_risk = 0;
         }

         if (msg.geografico) {
             var geo_risk = parseInt(msg.geografico.calificacion_riesgo);
         }
         else {
             var geo_risk = 0;
         }

         if (individual_r > 0) {
             individual_r = 4 - individual_r;
         }
         if (familiar_r > 0) {
             familiar_r = 4 - familiar_r;
         }
         if (economic_r > 0) {
             economic_r = 4 - economic_r;
         }
         if (life_risk > 0) {
             life_risk = 4 - life_risk;
         }
         if (academic_r > 0) {
             academic_r = 4 - academic_r;
         }

         riskGraphic(individual_r, familiar_r, economic_r, academic_r, life_risk, geo_risk)

     },
     dataType: "json",
     error: function(msg) {
         console.log(msg)
     }
 });
}

function send_email() {
 
 var high_risk_array = new Array();
 var observations_array = new Array();

 var high_individual_risk = $('input:radio[name=riesgo_ind]:checked').val();
 var high_familiar_risk = $('input:radio[name=riesgo_familiar]:checked').val();
 var high_academic_risk = $('input:radio[name=riesgo_aca]:checked').val();
 var high_economic_risk = $('input:radio[name=riesgo_econom]:checked').val();
 var high_life_risk = $('input:radio[name=riesgo_uni]:checked').val();

 if (high_individual_risk == '3') {
     high_risk_array.push('Individual');
     observations_array.push($('#individual').val());
 }
 if (high_familiar_risk == '3') {
     high_risk_array.push('Familiar');
     observations_array.push($('#familiar').val());
 }
 if (high_academic_risk == '3') {
     high_risk_array.push('Académico');
     observations_array.push($('#academico').val());
 }
 if (high_economic_risk == '3') {
     high_risk_array.push('Económico');
     observations_array.push($('#economico').val());
 }
 if (high_life_risk == '3') {
     high_risk_array.push('Vida universitaria');
     observations_array.push($('#vida_uni').val());
 }

 var data_email = new Array();
 data_email.push({
     name: "function",
     value: "send_email"
 });
 data_email.push({
     name: "id_student_moodle",
     value: $('#iduser').val()
 });
 data_email.push({
     name: "id_student_pilos",
     value: $('#idtalentos').val()
 });
 data_email.push({
     name: "risk_array",
     value: high_risk_array
 });
 data_email.push({
     name: "observations_array",
     value: observations_array
 });
 data_email.push({
     name: "date",
     value: $('#date').val()
 });
 data_email.push({
     name: "url",
     value: window.location
 });

 if (high_risk_array.length != 0) {
     $.ajax({
         type: "POST",
         data: data_email,
         url: "../managers/seguimientos.php",
         success: function(msg) {
             console.log(msg);
         },
         dataType: "text",
         cache: "false",
         error: function(msg) {
             console.log(msg)
         }
     });
 }
}

function load_student(code_student){
 
 $.ajax({
     type: "POST",
     data: {
         func: 'is_student',
         code_student: code_student
     },
     url: "../managers/student_profile/studentprofile_serverproc.php",
     success: function(msg) {

         if(msg == "1"){
             var parameters = get_url_parameters(document.location.search);
             var full_url = String(document.location);
             var url = full_url.split("?");

             var new_url = url[0]+"?courseid="+parameters['courseid']+"&instanceid="+parameters['instanceid']+"&student_code="+code_student;

             location.href = new_url;
         }else{
             swal(
                 "Error",
                 "No se encuentra un estudiante asociado al código ingresado",
                 "error"
             );
         }
         
     },
     dataType: "text",
     cache: "false",
     error: function(msg) {
         swal(
             "Error",
             "Error al comunicarse con el servidor, por favor intentelo nuevamente.",
             "error"
         );

     },
 });
}

function procesarJSON(){
 // Asignación de gráficas y manejo del JSON para graficar
 

 

 for(var i = 0; i <  Object.keys(datos['informacion']).length; i++){
   var arregloDimensionTmp = [];
   var nombreDimensionTmp;
   var fechasTmp = [];
   var colorTmp = [];
   var riesgoTmp = [];

   nombreDimensionTmp = datos['informacion'][i]['dimension'];
   var contador = 0;
   while(datos['informacion'][i]['datos'][contador]['end'] == 'false'){
     fechasTmp.push(datos['informacion'][i]['datos'][contador]['fecha']);
     colorTmp.push(datos['informacion'][i]['datos'][contador]['color']);
     riesgoTmp.push(datos['informacion'][i]['datos'][contador]['riesgo']);
     contador = contador + 1;
   }
   arregloDimensionTmp.push(nombreDimensionTmp);
   arregloDimensionTmp.push(fechasTmp);
   arregloDimensionTmp.push(colorTmp);
   arregloDimensionTmp.push(riesgoTmp);
   datosGraficables.push(arregloDimensionTmp);
 }
 // Fin de asignación de gráficas
}

function graficar(){
    procesarJSON();
  var myChart_individual = generar(datosGraficables[0],ctx_individual);
  var myChart_familiar = generar(datosGraficables[1],ctx_familiar);
  var myChart_academico = generar(datosGraficables[2],ctx_academico);
  var myChart_economico = generar(datosGraficables[3],ctx_economico);
  var myChart_vida_universitaria = generar(datosGraficables[4],ctx_vida_universitaria);
}
  
/*Generador de gráficas*/
function generar(datos, destino, canvas){
    return new Chart(destino, {
        type: 'line',
        data: {
            
            labels: datos[1],
            datasets: [{
                label: 'Nivel de riesgo',
                fill: false,
                lineTension: 0,
                pointBackgroundColor: datos[2],
                data: datos[3],
                backgroundColor: 'black',
                borderColor: 'black',
                borderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        stepSize: 1,
                        max : 3,    
                        min : 0
                    }
                }]
            }
        }
    });
};

});
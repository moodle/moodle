 /**
 * Management - Tracks (seguimiento de pilos)
 * @module amd/src/pilos_tracking_main 
 * @author Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery','block_ases/Modernizr-v282' ,'block_ases/bootstrap', 'block_ases/jquery.dataTables',  'block_ases/sweetalert', 'block_ases/select2'], function($,Modernizr,bootstrap, datatables, sweetalert, select2) {

    return {
        init: function() {


            var rol = 0;
            var id = 0;
            var name = "";
            var email = "";
            var namerol = "";


             /**
             *** Rules associated with the handling of new forms
             ***
             **/


            $(document).on( "click", ".btn-dphpforms-close", function() {
                $(this).closest('div[class="mymodal"]').fadeOut(300);


            });

            $('.outside').click(function(){
                var outside = $(this);
                swal({
                    title: 'Confirmación de salida',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Salir'
                  }, function(isConfirm) {
                    if (isConfirm) {
                        $(outside).parent('.mymodal').fadeOut(300);
                    }
                  });
                
            });

            function custom_actions( form, action ){

                if( (form == 'primer_acercamiento' ) && ( action == 'insert' )){ 

                }else if( (form == 'primer_acercamiento' ) && ( action == 'update' )){ 

                }else if( (form == 'inasistencia' )&&( action == 'insert' )){

                }else if( (form == 'inasistencia')&&( action == 'update' ) ){

                    var count_buttons_dphpforms = $('.dphpforms-record .btn-dphpforms-univalle').length;
                    if( count_buttons_dphpforms == 4 ){
                        $('.dphpforms-record .btn-dphpforms-univalle:eq(0)').css( { 'margin-left' : ( ($('.dphpforms-updater').width()/2) - 72 - 30 ) + 'px'  } );
                    }
                
                }else if( (form == 'seguimiento_pares' )&&( action == 'insert' )){

                }else if( (form == 'seguimiento_pares')&&( action == 'update' ) ){

                    var rev_prof = $('.dphpforms-record').find('.revisado_profesional').find('.checkbox').find('input[type=checkbox]').prop('checked');
                    var rev_prac = $('.dphpforms-record').find('.revisado_practicante').find('.checkbox').find('input[type=checkbox]').prop('checked');
                                            
                    if( rev_prof ){
                        $('.btn-dphpforms-delete-record').remove();
                        $('.btn-dphpforms-update').remove();
                    };

                    if( rev_prac ){
                        $('.btn-dphpforms-delete-record').remove();
                    };

                    var count_buttons_dphpforms = $('.dphpforms-record .btn-dphpforms-univalle').length;
                    if( (count_buttons_dphpforms == 3 )||(count_buttons_dphpforms == 2 ) ){
                        $('.dphpforms-record .btn-dphpforms-close').css( { 'margin-left' : ( ($('.dphpforms-updater').width()/2) - 30 ) + 'px'  } );
                    }else if( count_buttons_dphpforms == 4 ){
                        $('.dphpforms-record .btn-dphpforms-univalle:eq(0)').css( { 'margin-left' : ( ($('.dphpforms-updater').width()/2) - 72 - 30 ) + 'px'  } );
                    }

                }else if( (form == 'seguimiento_geografico_')&&( action == 'insert' ) ){
                
                }else if( (form == 'seguimiento_geografico_')&&( action == 'update' ) ){

                }else if( (form=='seguimiento_grupal_')&&( action == 'insert' ) ){

                }else if( (form=='seguimiento_grupal_')&&( action == 'update' ) ){

                }

            }

            $(document).ready(function() {

                ///////////////////////////////////////////////////////////7

                $(".se-pre-con").fadeOut('slow');
                $("#reemplazarToogle").fadeIn("slow");




                //Getting information of the logged user such as name, id, email and role
                $.ajax({
                    type: "POST",
                    data: {
                        type: "getInfo",
                        instance: get_instance()
                    },
                    url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg) {
                        $data = $.parseJSON(msg);
                        name = $data.username;
                        id = $data.id;
                        email = $data.email;
                        rol = $data.rol;
                        namerol = $data.name_rol;
                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) {
                        swal({
                            title: "error al obtener información del usuario, getInfo.",
                            html: true,
                            type: "error",
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });

                name = "";
                var usuario = [];
                usuario["id"] = id;
                usuario["name"] = name;
                usuario["namerol"] = namerol;


                create_specific_counting(usuario);




                // when user is 'practicante' then has permissions
                if (namerol == "practicante_ps") {

                    consultar_seguimientos_persona(get_instance(), usuario);
                    send_email_new_form(get_instance()); 



                   // when user is 'profesional' then has permissions
                } else if (namerol == "profesional_ps") {
                    //Starts adding event

                    consultar_seguimientos_persona(get_instance(), usuario);
                    send_email_new_form(get_instance());


                    // when user is 'monitor' then has permissions
                } else if (namerol == "monitor_ps") {

                    consultar_seguimientos_persona(get_instance(), usuario);
                    send_email_new_form(get_instance());




                    // when user is 'sistemas' then has permissions
                } else if (namerol == "sistemas") {
                    anadirEvento(get_instance());
                    send_email_new_form(get_instance());
                }

            });


            function edit_tracking_new_form(){
            // Controles para editar formulario de pares
            $('.dphpforms-peer-record').on('click', function(){
                var id_tracking = $(this).attr('data-record-id');
                load_record_updater('seguimiento_pares', id_tracking);
                $('#modal_v2_edit_peer_tracking').fadeIn(300);
                  
            });}


            function edit_groupal_tracking_new_form(){
            // Controles para editar formulario grupal
            $('.dphpforms-groupal-record').on('click', function(){
                var id_tracking = $(this).attr('data-record-id');
                load_record_updater('seguimiento_grupal', id_tracking);
               $('#modal_v2_edit_groupal_tracking').fadeIn(300);

            });}


            function check_risks_tracking( flag, student_code ){
                   

                        var individual_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_individual');
                        var idv_observation = $('.comentarios_individual').find('textarea').val();;
                        var familiar_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_familiar');
                        var fam_observation = $('.comentarios_familiar').find('textarea').val();
                        var academico_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_academico');
                        var aca_observation = $('.comentarios_academico').find('textarea').val();
                        var economico_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_economico');
                        var eco_observation = $('.comentarios_economico').find('textarea').val();
                        var vida_univer_risk = get_checked_risk_value_tracking('.puntuacion_vida_uni');
                        var vid_observation = $('.comentarios_vida_uni').find('textarea').val();

                        if( 
                            ( individual_risk == '3' ) || ( familiar_risk == '3' ) || 
                            ( academico_risk == '3' ) || ( economico_risk == '3' ) || 
                            ( vida_univer_risk == '3' ) 
                        ){

                            var json_risks = {
                                "function": "send_email_dphpforms",
                                "student_code": student_code,
                                "risks": [
                                    {
                                        "name":"Individual",
                                        "risk_lvl": individual_risk,
                                        "observation":idv_observation
                                    },
                                    {
                                        "name":"Familiar",
                                        "risk_lvl": familiar_risk,
                                        "observation":fam_observation
                                    },
                                    {
                                        "name":"Académico",
                                        "risk_lvl": academico_risk,
                                        "observation":aca_observation
                                    },
                                    {
                                        "name":"Económico",
                                        "risk_lvl": economico_risk,
                                        "observation":eco_observation
                                    },
                                    {
                                        "name":"Vida Universitaria",
                                        "risk_lvl": vida_univer_risk,
                                        "observation":vid_observation
                                    }
                                ],
                                "date": $('.fecha').find('input').val(),
                                "url": window.location.href
                            };


                            $.ajax({
                                type: "POST",
                                data: JSON.stringify(json_risks),
                                url: "../managers/pilos_tracking/send_risk_email.php",
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

                    
                };

            function get_checked_risk_value_tracking( class_id ){
                    var value = 0;
                    $( class_id ).find('.opcionesRadio').find('div').each(function(){
                        if($(this).find('label').find('input').is(':checked')){
                            value = $(this).find('label').find('input').val();
                        }
                    });
                    return value;
                }; 

           $(document).on('click', '.dphpforms > #button' , function(evt) {
                    evt.preventDefault();
                    $( ':disabled' ).prop( 'disabled', false);
                    var formData = new FormData();
                    var formulario = $(this).parent();
                    var url_processor = formulario.attr('action');
                    if(formulario.attr('action') == 'procesador.php'){
                        url_processor = '../managers/dphpforms/procesador.php';
                    };
                    var student_code = formulario.find('.id_estudiante').find('input').val();

                    $.ajax({
                        type: 'POST',
                        url: url_processor,
                        data:  $('form.dphpforms').serialize(),
                                dataType: 'json',

                        success: function(data) {
                                //var response = JSON.parse(data);
                                var response = data;
                                
                                if(response['status'] == 0){
                                    $.get( "../managers/pilos_tracking/api_pilos_tracking.php?function=update_last_user_risk&arg=" + student_code + "&rid=-1", function( data ) {
                                        console.log( data );
                                    });
                                    var mensaje = '';
                                    if(response['message'] == 'Stored'){
                                        mensaje = 'Almacenado';
                                    }else if(response['message'] == 'Updated'){
                                        mensaje = 'Actualizado';
                                    }
                                    check_risks_tracking( false, student_code );
                                    swal(
                                        {title:'Información',
                                        text: mensaje,
                                        type: 'success'},
                                        function(){
                                            if(response['message'] == 'Updated'){
                                                $('#dphpforms-peer-record-' + $('#dphpforms_record_id').val()).stop().animate({backgroundColor:'rgb(175, 255, 173)'}, 400).animate({backgroundColor:'#f5f5f5'}, 4000);
                                            }
                                        }
                                    );
                                    $('.dphpforms-response').trigger("reset");
                                    $('#modal_v2_edit_peer_tracking').fadeOut(300);
                                    $('#modal_v2_peer_tracking').fadeOut(300);

                                    
                                    
                                }else if(response['status'] == -2){
                                    var mensaje = '';
                                    if(response['message'] == 'Without changes'){
                                        mensaje = 'No hay cambios que registrar';
                                        $('#modal_v2_edit_peer_tracking').fadeOut(300);
                                        $('#modal_v2_peer_tracking').fadeOut(300);
                                        $('#modal_primer_acercamiento').fadeOut(300);
                                        $('#modal_seguimiento_geografico').fadeOut(300);
                                    }else if(response['message'] == 'Unfulfilled rules'){
                                        mensaje = 'Revise los valores ingresados';
                                    }
                                    swal(
                                        'Alerta',
                                        mensaje,
                                        'warning'
                                    );
                                }else if(response['status'] == -1){
                                    swal(
                                        'ERROR!',
                                        'Oops!, informe de este error',
                                        'error'
                                    );
                                };
                            },
                            error: function(data) {
                                swal(
                                    'Error!',
                                    'Oops!, informe de este error',
                                    'error'
                                );
                            }
                            
                     });
                
                     
                });




                $('.mymodal-close').click(function(){
                    $(this).parent().parent().parent().parent().fadeOut(300);
                });


            function create_specific_counting(user){
                
                $.ajax({
                    type: "POST",
                    data: {
                        type: "user_specific_counting",
                        user: user,
                        instance:get_instance(),
                    },
                    url: "../managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg
                        ) {

                    var obj = msg;
                    $.each( obj, function( index, value ){

                        $("#counting_"+value.code).html(value.html);
                    });
                    generate_general_counting(user);
                    $("#loading").fadeOut('slow');




                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                       swal({
                            title: "Oops !",
                            text: "Se presentó un inconveniente al cargar conteo de usuarios",
                            html: true,
                            type: 'warning',
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });

                }


            function generate_general_counting(user){

                    var review_prof=0;
                    var not_review_prof=0;
                    var review_pract=0;
                    var not_review_pract=0;
                    var role;

                    $(".review_prof").each(function( index,value ) {
                        review_prof+=parseInt($(this).text(),10);
                  });

                    $(".not_review_prof").each(function( index,value ) {
                        not_review_prof+=parseInt($(this).text(),10);
                  });
                    $(".review_pract").each(function( index,value ) {
                        review_pract+=parseInt($(this).text(),10);
                  });

                    $(".not_review_pract").each(function( index,value ) {
                        not_review_pract+=parseInt($(this).text(),10);
                  });

                if(user["namerol"]=='profesional_ps'){

                  role="PROFESIONAL";  
                }else if(user["namerol"]=='practicante_ps'){

                role="PRACTICANTE";

                }else if(user["namerol"]=='monitor_ps'){
                role="MONITOR";

                }else if(user["namerol"]=='sistemas'){
                role="SISTEMAS";
                }

                advice="";
                advice+='<h2> INFORMACIÓN DE  '+role+'</h2><hr>';
                advice+='<div class="row">';
                advice+='<div class="col-sm-6">';
                advice+='<strong>Profesional</strong><br>';
                advice+='Revisado :'+review_prof+' - No revisado : '+not_review_prof+' -  Total :'+(review_prof+not_review_prof)+'</div>';
                advice+='<div class="col-sm-6">';
                advice+='<strong>Practicante</strong><br>';
                advice+='Revisado :'+review_pract+' - No revisado : '+not_review_pract+' -  Total :'+(review_pract+not_review_pract)+'</div></div>';

                $("#div-header-info").html(advice);


                }

                function generate_attendance_table(students){

                     $.ajax({
                            type: "POST",
                            data: {
                                students: students,
                                type: "consult_students_name"
                            },
                            url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                            async: false,


                            success: function(msg) {

                                if (msg != "") {
                                   var table ='<hr style="border-color:red"><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 estudiantes" id="students"><h3>Estudiantes asistentes:</h3><br>'+msg+'<br>';
                                   $('#modal_v2_edit_groupal_tracking').find('#students').remove(); 
                                   $('#modal_v2_edit_groupal_tracking').find('form').find('h1').after(table);
                                }
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                alert("Error al consultar nombres de los estudiantes pertenecientes a un seguimiento grupal");
                            },
                        });

                    


                }


                function load_record_updater(form_id, record_id){
                    $.get( "../managers/dphpforms/dphpforms_forms_core.php?form_id=&record_id="+record_id, function( data ) {
                         if(form_id =='seguimiento_grupal'){

                            $("#body_editor").html("");
                            $("#modal_v2_edit_groupal_tracking").find("#body_editor").html("");                            
                            $("#modal_v2_edit_groupal_tracking").find("#body_editor").append(data);
                            $("#modal_v2_edit_groupal_tracking").find(".btn-dphpforms-univalle").remove();
                            var students = $("#modal_v2_edit_groupal_tracking").find('form').find('.oculto.id_estudiante').find('input').val();

                            generate_attendance_table(students);


                         }else{
                            $("#modal_v2_edit_groupal_tracking").find("#body_editor").html("");                            
                            $("#body_editor").html("");
                            $('#body_editor').append( data );
                            $(".dphpforms.dphpforms-record.dphpforms-updater").append('<br><br><div class="div-observation col-xs-12 col-sm-12 col-md-12 col-lg-12 comentarios_vida_uni">Observaciones de Practicante/profesional:<br> <textarea id="observation_text" class="form-control " name="observation_text" maxlength="5000"></textarea><br><a id="send_observation" class="btn btn-sm btn-danger btn-dphpforms-univalle btn-dphpforms-send-observation">Enviar observación</a></div>');
                            $('button.btn.btn-sm.btn-danger.btn-dphpforms-univalle').attr('id', 'button');
                            var is_seguimiento_pares = data.indexOf('seguimiento_de_pares_');
                            if( is_seguimiento_pares != -1 ){
                                custom_actions( 'seguimiento_pares', 'update' );
                            };
                            var is_inasistencia = data.indexOf('inasistencia');
                            if( is_inasistencia != -1 ){
                                custom_actions( 'inasistencia', 'update' );
                            };
                         }
                            
                           
                            $("#permissions_informationr").html("");

                            var rev_prof = $('.dphpforms-record').find('.revisado_profesional').find('.checkbox').find('input[type=checkbox]').prop('checked');
                            var rev_prac = $('.dphpforms-record').find('.revisado_practicante').find('.checkbox').find('input[type=checkbox]').prop('checked');
                            
                            if(rev_prof){ 
                                $('.dphpforms-record').find('.btn-dphpforms-delete-record').remove();
                            }

                            var behaviors = JSON.parse($('#permissions_information').text());
                            
                            for(var x = 0; x < behaviors['behaviors_permissions'].length; x++){
                             
                                var current_behaviors =  behaviors['behaviors_permissions'][x]['behaviors'][0];
                                var behaviors_accessibility = current_behaviors.behaviors_accessibility;
                                
                                for( var z = 0; z <  behaviors_accessibility.length; z++){
                                    var disabled = behaviors_accessibility[z]['disabled'];
                                    if(disabled == 'true'){
                                        disabled = true;
                                    }else if(disabled == 'false'){
                                        disabled = false;
                                    }
                                    $('.dphpforms-record').find('#' + behaviors_accessibility[z]['id']).prop( 'disabled', disabled );
                                    $('.dphpforms-record').find('.' + behaviors_accessibility[z]['class']).prop( 'disabled', disabled );

                                }
                                var behaviors_fields_to_remove = current_behaviors['behaviors_fields_to_remove'];
                                for( var z = 0; z < behaviors_fields_to_remove.length; z++){
                                    $('.dphpforms-record').find('#' + behaviors_fields_to_remove[z]['id']).remove();
                                    $('.dphpforms-record').find('.' + behaviors_fields_to_remove[z]['class']).remove();
                                }
                                var limpiar_to_eliminate = current_behaviors['limpiar_to_eliminate'];
                                for( var z = 0; z <  limpiar_to_eliminate.length; z++){
                                    $('.dphpforms-record').find('.' + limpiar_to_eliminate[z]['class'] + '.limpiar ').remove();
                                }
                                
                            }

                            $("#permissions_informationr").html("");

                    });
                }




            student_load();
            monitor_load();
            professional_load();
            groupal_tracking_load();

 



            //-------- Page elements --> Listener


            function professional_load(){

            /*When click on the practicant's name, open the container with the information of 
            the assigned monitors*/

            $('a[class*="practicant"]').click(function() {
                var practicant_code = $(this).attr('href').split("#practicant")[1];
                var practicant_id = $(this).attr('href');
                //Fill container with the information corresponding to the monitor 
                $.ajax({
                    type: "POST",
                    data: {
                        type: "get_practicants_of_professional",
                        practicant_code: practicant_code,
                        instance:get_instance(),
                    },
                    url: "../managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg
                        ) {
                    $(practicant_id + " > div").empty();
                    $(practicant_id + " > div").append(msg.render);
                    var html = msg.counting;

                    $.each(html,function( index,value ) {
                        $("#counting_"+value.code).html(value.html);
                    });


                    monitor_load();
                    groupal_tracking_load();
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                       swal({
                            title: "Oops !",
                            text: "Se presentó un inconveniente con el practicante seleccionado.",
                            html: true,
                            type: 'warning',
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });
            
            });
            }






            function monitor_load(){

            /*When click on the student's name, open the container with the information of 
            the follow-ups of that date*/

            $('a[class*="monitor"]').click(function() {
                var monitor_code = $(this).attr('href').split("#monitor")[1];
                var monitor_id = $(this).attr('href');
                //Fill container with the information corresponding to the monitor 
                $.ajax({
                    type: "POST",
                    data: {
                        type: "get_monitors_of_practicant",
                        monitor_code: monitor_code,
                        instance:get_instance(),
                    },
                    url: "../managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg
                        ) {
                    $(monitor_id + " > div").empty();
                    $(monitor_id + " > div").append(msg);
                    student_load();
                    groupal_tracking_load();
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                       swal({
                            title: "Oops !",
                            text: "Se presentó un inconveniente con el monitor seleccionado.",
                            html: true,
                            type: 'warning',
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });
            
            });
            }


            /*When click on the "SEGUIMIENTOS GRUPALES", open the container with the information of 
            the follow-ups of that date*/

            function groupal_tracking_load(){

            $('a[class*="groupal"]').click(function() {
                var student_code = $(this).attr('href').split("#groupal")[1];
                var student_id = $(this).attr('href');
                //Fill container with the information corresponding to the trackings of the selected student
                $.ajax({
                    type: "POST",
                    data: {
                        type: "get_groupal_trackings",
                        student_code: student_code,
                        instance:get_instance()
                    },
                    url: "../managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg
                        ) {
                    $(student_id + " > div").empty();
                    $(student_id + " > div").append(msg);
                    edit_groupal_tracking_new_form();
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        swal({
                            title: "Oops !",
                            text: "Se presentó un inconveniente con los seguimientos grupales seleccionados.",
                            html: true,
                            type: 'warning',
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });
            
            });}




            function student_load(){

            /*When click on the student's name, open the container with the information of 
            the follow-ups of that date*/

            $('a[class*="student"]').click(function() {
                var student_code = $(this).attr('href').split("#student")[1];
                var student_id = $(this).attr('href');
                //Fill container with the information corresponding to the trackings of the selected student
                $.ajax({
                    type: "POST",
                    data: {
                        type: "get_student_trackings",
                        student_code: student_code,
                        instance:get_instance()
                    },
                    url: "../managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg
                        ) {
                    $(student_id + " > div").empty();
                    $(student_id + " > div").append(msg);
                    edit_tracking_new_form();
                    edit_groupal_tracking_new_form();
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        swal({
                            title: "Oops !",
                            text: "Se presentó un inconveniente con el estudiante seleccionado.",
                            html: true,
                            type: 'warning',
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });
            
            });

            /*When click on the button "Ver horas", open a new tab with information of report time control about a
            determinated monitor*/

            $('.see_history').unbind().click(function(e) {


             var element =  $(this).parents().eq(3).attr('href').split("#monitor")[1];

            $.ajax({
                    type: "POST",
                    data: {
                        type: "redirect_tracking_time_control",
                        monitor: element,
                    },
                    url: "../managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg) {
                        var current_url = window.location.href;
                        var next_url = current_url.replace("report_trackings", "tracking_time_control");

                        try{
                        var win = window.open(next_url+"&&monitorid="+msg, '_blank');
                        if (win) {
                            //Browser has allowed it to be opened
                            win.focus();
                        }
                        }catch(ex){
                            alert("Se ha producido un error al abrir la ventana : "+ex);
                        } 
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        swal({
                            title: "Oops !",
                            text: "Se presentó un inconveniente al reedirecionar al reporte de horas.",
                            html: true,
                            type: 'warning',
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });

            });

        }




            /**
             * @method consultar_seguimientos_persona
             * @desc Obtain track information of a certain user
             * @param {instance} instance current instance
             * @param {object} usuario current user to obtain information
             * @return {void}
             */
            function consultar_seguimientos_persona(instance, usuario) {
                $("#periodos").change(function() {
                    if (namerol != 'sistemas') {
                        var semestre = $("#periodos").val();
                        var id_persona = id;
                        $.ajax({
                            type: "POST",
                            data: {
                                id_persona: id_persona,
                                id_semestre: semestre,
                                instance: instance,
                                otro: true,
                                type: "consulta_sistemas"
                            },
                            url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                            async: false,


                            success: function(msg) {

                                if (msg == "") {
                                    $('#reemplazarToogle').html('<label> No se encontraron registros </label>');



                                } else {
                                    $('#reemplazarToogle').html(msg);
                                    student_load();
                                    monitor_load();
                                    professional_load();
                                    groupal_tracking_load();
                                }
                                $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").slideDown("slow");




                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                swal(
                                 'ERROR!',
                                 'Oops!, Se presentó un error al consultar seguimientos de personas',
                                 'error'
                                );
                            },
                        });
                        edit_tracking_new_form();
                        edit_groupal_tracking_new_form();

                    }


                });
            }


            /**
             * @method anadirEvento
             * @desc Function for 'sistemas' role. Adding an event
             * @param {instance} instance current instance
             * @return {string} message according if there's a period or person to look for
             */
            function anadirEvento(instance) {
                $("#personas").val('').change();

                //Select2 is able when user role is 'sistemas'
                $("#personas").select2({
                    placeholder: "Seleccionar persona",

                    language: {
                        noResults: function() {
                            return "No hay resultado";
                        },
                        searching: function() {
                            return "Buscando..";
                        }
                    }
                });
                $("#periodos").select2({
                    language: {
                        noResults: function() {
                            return "No hay resultado";
                        },
                        searching: function() {
                            return "Buscando..";
                        }
                    }
                });

                period_consult(instance, namerol);




                $('#consultar_persona').on('click', function() {

                    var id_persona = $("#personas").children(":selected").attr("value");
                    var id_semestre = $("#periodos").children(":selected").attr("value");
                    var fechas_epoch = [];


                    if (id_persona == undefined) {
                        swal({
                            title: "Debe escoger una persona para realizar la consulta",
                            html: true,
                            type: "warning",
                            confirmButtonColor: "#d51b23"
                        });
                    } else {
                        $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").show();

                        $(".se-pre-con").show();
                        $("#reemplazarToogle").hide();

                        //Processing in pilos_tracking_report.php
                        $.ajax({
                            type: "POST",
                            data: {
                                id_persona: id_persona,
                                id_semestre: id_semestre,
                                instance: get_instance(),
                                type: "consulta_sistemas"
                            },
                            url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                            async: false,


                            success: function(msg) {



                                //In case there are not records
                                if (msg == "") {
                                    $('#reemplazarToogle').html('<label> No se encontraron registros </label>');

                                } else {
                                    $('#reemplazarToogle').html(msg);
                                    $("input[name=practicante]").prop('disabled', true);
                                    $("input[name=profesional]").prop('disabled', true);
                                }
                                student_load();
                                monitor_load();
                                professional_load();
                                groupal_tracking_load();
                                $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").slideDown("slow");

                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                             swal(
                                 'ERROR!',
                                 'Oops!, Se presentó un error al cargar seguimientos de personas',
                                 'error'
                                );
                            },
                            complete: function(){
                               $(".se-pre-con").hide();
                               $("#reemplazarToogle").fadeIn();
                            }
                        });
                        edit_tracking_new_form();
                        edit_groupal_tracking_new_form();

                    }

                });
            }

 

            /**
             * @method send_email_new_form
             * @desc Sends an email to a monitor, given his id, text message, date, name.
             * @param {instance} instance current instance 
             * @return {void}
             */
            function send_email_new_form(instance){

                $('body').on('click', '#send_observation', function() {
                    var form = $("form").serializeArray(),dataObj = {};


                    $(form).each(function(i, field){
                        dataObj[field.name] = field.value;
                    });


                    var id_register = dataObj['id_registro'];
                    var text = $("#observation_text");


                    if (text.val() == "") {
                        swal({
                            title: "Para enviar una observación debe llenar el campo correspondiente",
                            html: true,
                            type: "error",
                            confirmButtonColor: "#d51b23"
                        });
                    } else {
                        // Gets text message and monitor id to send the email
                        var tracking_type = 'individual';
                        var monitor_code = $('.id_creado_por').find('input').val();
                        if(monitor_code == ""){
                            monitor_code = $('.in_id_creado_por').find('input').val();
                            tracking_type = 'individual_inasistencia';
                        }
                        var date = $('.fecha').find('input').val();
                        if(date == ""){
                            date = $('.in_fecha').find('input').val();
                        }
                        var message_to_send = text.val();
                        var semester=$("#periodos").val();
                        var place = $('.lugar').find('input').val();
                        if(place == ""){
                            place = $('.in_lugar').find('input').val();
                        }


                        //Text area is clear again
                        var answer = "";

                        //Ajax function to send message
                        $.ajax({
                            type: "POST",
                            data: {
                                id_tracking: id_register,
                                type: "send_email_to_user",
                                form: "new_form",
                                tracking_type: tracking_type,
                                monitor_code: monitor_code,
                                date: date,
                                message_to_send: message_to_send,
                                semester:semester,
                                instance:instance,
                                place:place
                            },
                            url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                            async: false,
                            success: function(msg) {
                                //If it was successful...

                                if (msg != "Error") {
                                    swal({
                                        title: "Correo enviado",
                                        html: true,
                                        type: "success",
                                        confirmButtonColor: "#d51b23"
                                    });
                                    text.val("");

                                } else {
                                    console.log("mensaje error : ");
                                    console.log( msg )
                                    swal({
                                        title: "error al enviar el correo al monitor",
                                        html: true,
                                        type: "error",
                                        confirmButtonColor: "#d51b23"
                                    });
                                }
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                console.log( "mensaje error : " );
                                console.log( msg );
                            swal(
                                 'ERROR!',
                                 'Oops!, Se presentó un error al enviar el correo',
                                 'error'
                                );
                            },
                        });
                    }
                });


            }




 


            /**   Auxiliary functions !!
             */



            /**
             * @method period_consult(instance, namerol)
             * @desc Functionality that sets the select2 corresponding to "PERSONA" with the same ones that are associated with the selected semester
             * @param {*} instance 
             * @param {*} namerol 
             * @return {void}
             */
            function period_consult(instance, namerol) {
                $("#periodos").change(function() {
                    var chosen_period = $("#periodos").val();
                    $.ajax({
                        type: "POST",
                        data: {
                            id: chosen_period,
                            instance: instance,
                            type: "update_people"
                        },
                        url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                        async: false,
                        success: function(msg) {


                            $('#personas').empty();
                            $("#personas").select2({
                                placeholder: "Seleccionar persona",
                                language: {
                                    noResults: function() {
                                        return "No hay resultado";
                                    },
                                    searching: function() {
                                        return "Buscando..";
                                    }
                                }
                            });
                            if (namerol == 'sistemas') {
                                var index = '<option value="">Seleccionar persona</option>';

                                $("#personas").attr('selectedIndex', '-1').find("option:selected").removeAttr("selected");
                                $('#personas').append(index + msg);

                            }

                        },
                        dataType: "text",
                        cache: "false",
                        error: function(msg) {
                            swal(
                                 'ERROR!',
                                 'Oops!, Se presentó un error al cargar personas',
                                 'error'
                                );
                        },
                    });
                });

            }

            /**
             * @method get_instance()
             * @desc Functionality to obtain the id of current instance.
             * @return {integer}
             */

            function get_instance(){
                //We get the current instance id

                var informacionUrl = window.location.search.split("&");
                for (var i = 0; i < informacionUrl.length; i++) {
                    var elemento = informacionUrl[i].split("=");
                    if (elemento[0] == "?instanceid" || elemento[0] == "instanceid") {
                        var instance = elemento[1];
                    }
                }
                return instance;
            }


        }
    };
});
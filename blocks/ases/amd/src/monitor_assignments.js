// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
  * @module block_ases/monitor_assignments
  */

 define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui','block_ases/select2'], function($, bootstrap, sweetalert, jqueryui, select2) {
    
    return {
        init: function() {

            /**
             * 
             * @param {Number} instance_id 
             * @param {Number} data_id monitor identificator
             */
            function load_assigned_students( instance_id, data_id ){


                $("#student_assigned").addClass("items_assigned_empty");
                $("#student_assigned").html("Consultando <span>.</span><span>.</span><span>.</span>");
                $(".student_item").removeClass("oculto-asignado");
                $('#student_column').animate({
                    scrollTop: $('#student_column').scrollTop() + $('#student_assigned').position().top
                }, 0);

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": "get_monitors_students_relationship_by_instance", "params": [ instance_id ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code == 0 ){

                            var monitor_assignments_monitor_students_relationship = data.data_response;
                            $(".monitor_item").removeClass("active");
                            $(".monitor_item[data-id='" + data_id + "']").addClass("active");
                            $(".student_item").find(".add").removeClass("oculto-asignar")
                            $(".student_item").removeClass("assigned");
                            $(".student_item").removeClass("not-assigned");
                            $(".student_item").addClass("not-assigned");
                            $("#student_assigned").removeClass("items_assigned_empty");
                            $("#student_assigned").text("");
                            
                            var elements = false;
                            for( var i = 0; i < monitor_assignments_monitor_students_relationship.length; i++ ){
                                if( monitor_assignments_monitor_students_relationship[i].id_monitor == data_id ){

                                    if( !elements ){
                                        elements = true;
                                        $("#student_assigned").removeClass("items_assigned_empty");
                                    }
                                    
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").removeClass("not-assigned");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").addClass("assigned");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").find(".add").addClass("oculto-asignar");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").clone().appendTo("#student_assigned");

                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").not("#student_assigned .student_item").addClass("oculto-asignado");

                                }else{
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").find(".add").addClass("oculto-asignar");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").find(".delete").addClass("oculto-eliminar");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").addClass("oculto-asignado");
                                }
                            }

                            $("#student_assigned").find(".student_item").find(".add").addClass("oculto-asignar");
                            $("#student_assigned").find(".student_item").find(".delete").removeClass("oculto-eliminar");

                            if( !elements ){
                                $("#student_assigned").addClass("items_assigned_empty");
                                $("#student_assigned").text("No tiene estudiantes asignados.");
                            }
                            
                        }else{
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });

            };

            /**
             * 
             * @param {Number} instance_id 
             * @param {Number} data_id practicant identificator
             */
            function load_assigned_monitors( instance_id, data_id ){

                $("#monitor_assigned").addClass("items_assigned_empty");
                $("#monitor_assigned").html("Consultando <span>.</span><span>.</span><span>.</span>");
                $(".monitor_item").removeClass("oculto-asignado");
                $('#monitor_column').animate({
                    scrollTop: $('#monitor_column').scrollTop() + $('#monitor_assigned').position().top
                }, 0);

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": "get_practicant_monitor_relationship_by_instance", "params": [ instance_id ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code == 0 ){
                            var monitor_assignments_practicant_monitor_relationship = data.data_response;
                            $(".practicant_item").removeClass("active");
                            $(".practicant_item[data-id='" + data_id + "']").addClass("active");
                            $(".monitor_item").find(".add").removeClass("oculto-asignar")
                            $(".monitor_item").find(".transfer").removeClass("oculto-tranferir");
                            $(".monitor_item").removeClass("assigned");
                            $(".monitor_item").removeClass("not-assigned");
                            $(".monitor_item").addClass("not-assigned");
                            $(".student_item").removeClass("assigned");
                            $(".student_item").removeClass("not-assigned");
                            $(".student_item").addClass("not-assigned");
                            $("#student_assigned").text("No ha seleccionado un monitor.");
                            $("#student_assigned").addClass("items_assigned_empty");
                            $("#monitor_assigned").text("");
                            var elements = false;
                            for( var i = 0; i < monitor_assignments_practicant_monitor_relationship.length; i++ ){
                                if( monitor_assignments_practicant_monitor_relationship[i].id_practicante == data_id ){
                                    
                                    if( !elements ){
                                        elements = true;
                                        $("#monitor_assigned").removeClass("items_assigned_empty");
                                    }

                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").removeClass("not-assigned");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").addClass("assigned");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").find(".add").addClass("oculto-asignar");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").clone().appendTo("#monitor_assigned");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").not("#monitor_assigned .monitor_item").addClass("oculto-asignado");
                                }else{
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").find(".add").addClass("oculto-asignar");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").find(".delete").addClass("oculto-eliminar");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").addClass("oculto-asignado");
                                }
                            }

                            $("#monitor_assigned").find(".monitor_item").find(".add").addClass("oculto-asignar");
                            $("#monitor_assigned").find(".monitor_item").find(".delete").removeClass("oculto-eliminar");
                            $(".monitor_item").not("#monitor_assigned .monitor_item").find(".transfer").addClass("oculto-tranferir");

                            if( !elements ){
                                $("#monitor_assigned").addClass("items_assigned_empty");
                                $("#monitor_assigned").text("No tiene monitores asignados.");
                            }

                        }else{
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });
            };

            $( "#student-name-filter" ).keyup(function() {
                $('.student_item').removeClass("oculto-filtro-nombre");
                var filter_value = $(this).val();
                if (filter_value != ""){
                    $('.student_item').not('.item-general-list.student_item[data-name*="' + filter_value.toUpperCase() + '"]').addClass("oculto-filtro-nombre");
                    $(this).addClass("filter-active");
                }else{
                    $(this).removeClass("filter-active");
                }
            });

            $( "#monitor-name-filter" ).keyup(function() {
                $('.monitor_item').removeClass("oculto-filtro-nombre");
                var filter_value = $(this).val();
                if (filter_value != ""){
                    $('.monitor_item').not('.item-general-list.monitor_item[data-name*="' + filter_value.toUpperCase() + '"]').addClass("oculto-filtro-nombre");
                    $(this).addClass("filter-active");
                }else{
                    $(this).removeClass("filter-active");
                }
            });

            $("#btn-student-name-filter").click(function(){
                $("#student-name-filter").val("");
                $('.student_item').removeClass("oculto-filtro-nombre");
                $("#student-name-filter").removeClass("filter-active");
            });

            $("#btn-monitor-name-filter").click(function(){
                $("#monitor-name-filter").val("");
                $('.monitor_item').removeClass("oculto-filtro-nombre");
                $("#monitor-name-filter").removeClass("filter-active");
            });

            /*var monitor_assignments_professional_practicant;
            
            $(document).ready(function(){
                monitor_assignments_professional_practicant = JSON.parse( $("#monitor_assignments_professional_practicant").text() );
            });*/

            $(document).on( 'click', '.practicant_item', function() {

                load_assigned_monitors( $("#monitor_assignments_instance_id").data("instance-id"), $(this).attr("data-id") );

            });

            $(document).on( 'click', '.monitor_item', function() {
                load_assigned_students( $("#monitor_assignments_instance_id").data("instance-id") , $(this).attr("data-id")  );
            });

            $(document).on( 'click', '.student_item', function(){
                var data_id = $(this).attr("data-id"); // student_id
                $(".student_item").removeClass("active");
                $(this).addClass("active");
                $(".student_item[data-id='" + data_id + "']").addClass("active");
            });

            $(document).on( 'click', '.add', function(e) {

                e.stopImmediatePropagation();

                var current_item = $(this);
                var instance_id = $("#monitor_assignments_instance_id").data("instance-id");

                var item = $(this).parent();
                var data_item_0 = -1; // monitor_id or practicant_id
                var data_item_1 = item.attr("data-id");
                var item_type = item.attr("data-item");

                var api_function = "";

                if( item_type == "student" ){
                    api_function = "create_monitor_student_relationship";
                    data_item_0 =  $(".monitor_item.active").attr("data-id");
                }else if( item_type == "monitor" ){
                    api_function = "create_practicant_monitor_relationship";
                    data_item_0 =  $(".practicant_item.active").attr("data-id");
                }

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": api_function, "params": [ instance_id, data_item_0 ,data_item_1 ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code === 0 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'Asignación registrada correctamente.',
                                    type: 'success'},
                                    function(){
                                        if( item_type == "student" ){
                                            load_assigned_students( instance_id , data_item_0 );
                                            item.find(".add").addClass("oculto-asignar");
                                        }else if( item_type == "monitor" ){
                                            load_assigned_monitors( instance_id , data_item_0 );
                                            item.find(".add").addClass("oculto-asignar");
                                        }
                                    }
                                );
                            }, 0);
                        }else if( data.status_code === -5 ){

                            if( item_type == "student" ){
                                setTimeout(function(){
                                    swal(
                                        {title:'Información',
                                        text: 'La asignación ya existe en el periodo actual, si tiene problemas con esto, puede probar de nuevo recargando la pestaña.',
                                        type: 'info'},
                                        function(){}
                                    );
                                }, 0);
                            }else if( item_type == "monitor" ){
                                setTimeout(function(){
                                    swal(
                                        {title:'Información',
                                        text: 'Este monitor ya se encuentra asignado.',
                                        type: 'info'},
                                        function(){}
                                    );
                                }, 0);
                            }

                        }else{
                            setTimeout(function(){
                                swal(
                                    {title:'Error',
                                    text: 'Reporte este error.',
                                    type: 'error'},
                                    function(){}
                                );
                            }, 0);
                        }
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });

            });

            $(document).on( 'click', '.delete', function(e) {

                e.stopImmediatePropagation();

                var current_item = $(this);
                var instance_id = $("#monitor_assignments_instance_id").data("instance-id");

                var item = $(this).parent();
                var data_item_0 = -1; // monitor_id or practicant_id
                var data_item_1 = item.attr("data-id");
                var item_type = item.attr("data-item");

                var api_function = "";

                if( item_type == "student" ){
                    api_function = "delete_monitor_student_relationship";
                    data_item_0 =  $(".monitor_item.active").attr("data-id");
                }else if( item_type == "monitor" ){
                    api_function = "delete_practicant_monitor_relationship";
                    data_item_0 =  $(".practicant_item.active").attr("data-id");
                }

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": api_function, "params": [ instance_id, data_item_0 ,data_item_1 ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code === 0 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'Asignación eliminada correctamente',
                                    type: 'success'},
                                    function(){
                                        if( item_type == "student" ){
                                            load_assigned_students( instance_id , data_item_0 );
                                            item.find(".add").removeClass("oculto-asignar");
                                        }else if( item_type == "monitor" ){
                                            load_assigned_monitors( instance_id , data_item_0 );
                                            item.find(".add").addClass("oculto-asignar");
                                        }
                                    }
                                );
                            }, 0);
                        }else if( data.status_code === 1 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'Está intentando eliminar una asignación que ya no existe, si tiene problemas con esto, puede probar de nuevo recargando la pestaña.',
                                    type: 'info'},
                                    function(){}
                                );
                            }, 0);
                        }else{
                            setTimeout(function(){
                                swal(
                                    {title:'Error',
                                    text: 'Reporte este error.',
                                    type: 'error'},
                                    function(){}
                                );
                            }, 0);
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });

            });

            $(document).on( 'click', '.transfer', function(e) {

                e.stopImmediatePropagation();

                var name_original_monitor = $(this).parent().data("name");
                var id_old_monitor = $(this).parent().data("id");

                $('#modalTransfer').modal('show');
                $("#old_monitor_name").text(name_original_monitor);
                
                var options = '<option value="" disabled selected>Seleccione un monitor</option>\n';
                $("#monitor_assigned > .monitor_item").each(function(){
                    var name = $(this).data("name");
                    var id_new_monitor = $(this).data("id");
                    options += '<option data-old="' + id_old_monitor + '" data-new="' + id_new_monitor + '">' + name + '</option>\n';
                });

                $("#transfer-monitor-list").html("");
                $("#transfer-monitor-list").append( options );

            });

            $(document).on( 'click', '#btn-execute-transfer', function(e){

                $('#modalTransfer').modal('hide');

                var instance_id = $("#monitor_assignments_instance_id").data("instance-id");
                var api_function = "transfer";
                var id_old_monitor = $("#transfer-monitor-list").find(":selected").data("old");
                var id_new_monitor = $("#transfer-monitor-list").find(":selected").data("new");

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": api_function, "params": [ instance_id, id_old_monitor ,id_new_monitor ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code === 0 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'Estudiantes transferidos correctamente.',
                                    type: 'success'},
                                    function(){
                                        load_assigned_students( instance_id, id_new_monitor );
                                    }
                                );
                            }, 0);
                        }else if( data.status_code === 1 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'El monitor no tiene estudiantes para transferir.',
                                    type: 'info'},
                                    function(){}
                                );
                            }, 0);
                        }else{
                            setTimeout(function(){
                                swal(
                                    {title:'Error',
                                    text: 'Reporte este error.',
                                    type: 'error'},
                                    function(){}
                                );
                                swal.close();
                            }, 0);
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });
            });

            $("select").change(function(){

                var user_type = $(this).attr("data-id").split("_")[0]; // i.e monitor_faculty => monitor
                var filter_type = $(this).attr("data-id").split("_")[1]; // i.e monitor_faculty => faculty

                if( (user_type == "monitor") && (filter_type == "faculty") ){
                    var faculty_id = $(this).find(":selected").attr("data-id-facultad");
                    if( faculty_id != "-1" ){
                        $(this).addClass("filter-active");
                        $(".item-general-list.monitor_item").removeClass("oculto-facultad");
                        $(".item-general-list.monitor_item").not(".item-general-list.monitor_item[data-id-facultad='" + faculty_id + "']").addClass("oculto-facultad");
                    }else{
                        $(this).removeClass("filter-active");
                        $(".item-general-list.monitor_item").removeClass("oculto-facultad");
                    }
                }else if( (user_type == "monitor") && (filter_type == "program") ){
                    var program_id = $(this).find(":selected").attr("data-cod-programa");
                    if( program_id != "-1" ){
                        $(this).addClass("filter-active");
                        $(".item-general-list.monitor_item").removeClass("oculto-programa");
                        $(".item-general-list.monitor_item").not(".item-general-list.monitor_item[data-cod-programa='" + program_id + "']").addClass("oculto-programa");
                    }else{
                        $(this).removeClass("filter-active");
                        $(".item-general-list.monitor_item").removeClass("oculto-programa");
                    }
                }else if( (user_type == "student") && (filter_type == "faculty") ){
                    var faculty_id = $(this).find(":selected").attr("data-id-facultad");
                    if( faculty_id != "-1" ){
                        $(this).addClass("filter-active");
                        $(".item-general-list.student_item").removeClass("oculto-facultad");
                        $(".item-general-list.student_item").not(".item-general-list.student_item[data-id-facultad='" + faculty_id + "']").addClass("oculto-facultad");
                    }else{
                        $(this).removeClass("filter-active");
                        $(".item-general-list.student_item").removeClass("oculto-facultad");
                    }
                }else if( (user_type == "student") && (filter_type == "program") ){
                    var program_id = $(this).find(":selected").attr("data-cod-programa");
                    if( program_id != "-1" ){
                        $(this).addClass("filter-active");
                        $(".item-general-list.student_item").removeClass("oculto-programa");
                        $(".item-general-list.student_item").not(".item-general-list.student_item[data-cod-programa='" + program_id + "']").addClass("oculto-programa");
                    }else{
                        $(this).removeClass("filter-active");
                        $(".item-general-list.student_item").removeClass("oculto-programa");
                    }
                }else if( (user_type == "professional") ){
                    var boss_id = $(this).find(":selected").attr("data-id");
                    if( boss_id != "-1" ){
                        $(this).addClass("filter-active");
                        $(".item-general-list.practicant_item").removeClass("oculto-jefe");
                        $(".item-general-list.practicant_item").not(".item-general-list.practicant_item[data-id-jefe='" + boss_id + "']").addClass("oculto-jefe");
                    }else{
                        $(this).removeClass("filter-active");
                        $(".item-general-list.practicant_item").removeClass("oculto-jefe");
                    }
                }
            });
        }
    };
});
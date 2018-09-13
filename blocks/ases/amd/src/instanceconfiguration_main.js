// Standard license block omitted.
/*
 * @package    block_ases/instanceconfiguration_main
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module block_ases/instanceconfiguration_main
  */

define(['jquery', 'block_ases/bootstrap', 'block_ases/jquery.dataTables','block_ases/sweetalert'],
        function($, bootstrap, datatables, swal) {

    return {

        init: function() {

            var self = this;
            var instance_id = self.get_id_instance();

            self.load_cohorts_assigned();

            var idnumber = $('#input_idnumber').val();
            var description = $('#input_description').val();
            
            $('span.unassigned_cohort').on('click', {object_function: self}, self.unassign_cohort);
            $('#button_assign_cohort').on('click', {object_function: self}, self.assign_cohort_instance);
            $('#button_update_instance').on('click', function(){
                var idnumber = $('#input_idnumber').val();
                var description = $('#input_description').val();
                self.update_info_instance(instance_id, idnumber, description);
            });

        },
        get_cohorts_without_assignment: function(instance_id){

            if(instance_id == undefined){
                var instance_id = this.get_id_instance();
            }

            $.ajax({
                type: "POST",
                data: {
                       function: 'load_cohorts_without_assignment',
                       instance: instance_id},
                url: "../managers/instance_management/instance_configuration_serverproc.php",
                success: function(msg) {
                    if(msg.status == 0){
                        swal(
                            'Error',
                            msg.msg,
                            'error'
                        );
                    }else if(msg.status == 1){

                        var options = "";
                        var cohorts_array = msg.msg;

                        if(cohorts_array.length == 0){
                            options += "<option>No hay cohortes disponibles para asignar</option>";
                        }else{
                            $.each(cohorts_array, function(key){
                                options += "<option value='"+cohorts_array[key].id+"'>";
                                options += cohorts_array[key].idnumber+" "+cohorts_array[key].name+"</option>";
                            });
                        }

                        $('#select_cohorts').html(options);

                    }else{
                        var error_msg = "Error al cargar las cohortes no asignadas. Por favor recargue la página.";
                        error_msg += "Si el problema persiste contacte al área de sistemas.";
                        swal(
                            'Error',
                            error_msg,
                            'error'
                        );
                    }
                },
                dataType: "json",
                cache: false,
                async: false,
                error: function() {
                    swal(
                        'Error',
                        'Error al cargar las cohortes sin asignación',
                        'error'
                    );
                },
            });
        },
        load_cohorts_assigned: function(instance_id){

            if(instance_id == undefined){
                var instance_id = this.get_id_instance();
            }

            $.ajax({
                type: "POST",
                data: {
                       function: 'load_cohorts',
                       instance: instance_id},
                url: "../managers/instance_management/instance_configuration_serverproc.php",
                success: function(msg) {
                    if(msg.status == 0){
                        $('#div_cohorts_table').html("<center><span>La instancia no tiene cohortes asignadas</span></center>");
                    }else{
                        var html = "";
                        html += "<h4>Cohortes asignadas a la instancia</h4><hr/>";
                        html += "<table id='cohorts_table' class='col-sm-12' style='width:100%'></table>";
                        $('#div_cohorts_table').html(html);
                        $('#cohorts_table').DataTable(msg.msg);
                    }
                },
                dataType: "json",
                cache: false,
                async: false,
                error: function() {
                    swal(
                        'Error',
                        'Error al cargar las cohortes asignadas',
                        'error'
                    );
                },
            });
        },
        unassign_cohort: function(obj){

            var idnumber_cohort = $(this).parent().siblings()[0].innerHTML;
            var instance_id = obj.data.object_function.get_id_instance();

            $.ajax({
                type: "POST",
                data: {
                    function: 'unassign_cohort',
                    instance_id: instance_id,
                    idnumber_cohort: idnumber_cohort},
                url: "../managers/instance_management/instance_configuration_serverproc.php",
                success: function(msg){
                    if(msg.status == 1){
                        swal(
                            'Éxito',
                            msg.msg,
                            'success'
                        );
                    }else{
                        swal(
                            'Error',
                            msg.msg,
                            'error'
                        );
                    }
                },
                error: function(msg){
                    swal(
                        'Error',
                        'Error al conctarse con el servidor.',
                        'error'
                    );
                },
                dataType: "json",
                cache: false,
                async: false
            });
            obj.data.object_function.get_cohorts_without_assignment();
            obj.data.object_function.load_cohorts_assigned();
            $('span.unassigned_cohort').on('click', {object_function: obj.data.object_function}, obj.data.object_function.unassign_cohort);
        },
        get_id_instance: function(){
            var urlParameters = location.search.split('&');
            for (var x in urlParameters) {
                if (urlParameters[x].indexOf('instanceid') >= 0) {
                    var intanceparameter = urlParameters[x].split('=');
                    return intanceparameter[1];
                }
            }
            return 0;
        },
        assign_cohort_instance: function(obj){

            var cohort_id = $('#select_cohorts').val();
            var instance_id = obj.data.object_function.get_id_instance();

            $.ajax({
                type: "POST",
                data: { function: 'insert_cohort',
                        cohort: cohort_id,
                        instance: instance_id},
                url: "../managers/instance_management/instance_configuration_serverproc.php",
                success: function(msg) {
                    if(msg.status == 0){
                        var title = 'Error';
                        var type = 'error';
                    }else{
                        var title = 'Éxito';
                        var type = 'success';
                    }
                    swal(
                        title,
                        msg.msg,
                        type
                    );

                    obj.data.object_function.get_cohorts_without_assignment();
                    obj.data.object_function.load_cohorts_assigned();
                    $('span.unassigned_cohort').on('click', {object_function: obj.data.object_function}, obj.data.object_function.unassign_cohort);
                },
                dataType: "json",
                cache: false,
                async: false,
                error: function(){
                    swal(
                        'Error',
                        'Error al comunicarse con el servidor.',
                        'error'
                    );
                },
            });
        },
        update_info_instance: function(instance_id, idnumber, description){


            data = {
                function: 'update_info_instance',
                instance_id: instance_id,
                idnumber: idnumber,
                description: description
            }

            $.ajax({
                type: "POST",
                data: data,
                url: "../managers/instance_management/instance_configuration_serverproc.php",
                success: function(msg) {
                    if(msg){
                        swal(
                            'Éxito',
                            msg.msg,
                            'success'
                        );
                    }else{
                        swal(
                            'Error',
                            msg.msg,
                            'error'
                        );
                    }
                },
                dataType: "json",
                cache: false,
                async: false,
                error: function() {
                    swal(
                        'Error',
                        'Error al actualizar la instancia',
                        'error'
                    );
                },
            });

        }
    };
});
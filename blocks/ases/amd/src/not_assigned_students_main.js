/**
 * not_assigned_students
 * @module amd/src/not_assigned_students
 * @author Isabella Serna Ramirez
 * @copyright 2018 Isabella Serna Ramirez <isabella.serna@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/not_assigned_students
 */
define(['jquery', 'block_ases/bootstrap', 'block_ases/jquery.dataTables', 'block_ases/sweetalert', 'block_ases/dphpforms_form_renderer'],
    function($, bootstrap, datatables, swal, renderer_forms) {

        return {

            init: function() {

              console.log("pass");

                var self = this;
                var instance_id = self.get_id_instance();
                self.load_not_assigned_students(instance_id);

                $("#send_form_btn").on('click', function() {
                    self.create_assign_table(instance_id);
                });

                //Asignación de estudiantes a monitores/practicantes por parte de profesional.
                $(document).on('click', '#tableAssign tbody tr td #student_assign', function() {
                    var table = $("#tableAssign").DataTable();
                    var current_row = table.row($(this).parents('tr')).data();
                    var instance = instance_id;
                    var student = current_row.username;
                    var monitores = $(this).closest('tr').find('#monitors').val();
                    var practicantes = $(this).closest('tr').find('#practicants').val();

                    var next = true;
                    var msg = "";
                    if (monitores == '-1') {
                        next = false;
                        msg += "* Debe elegir monitor a asignar \n";
                    }
                    if (practicantes == '-1') {
                        next = false;
                        msg += "*Debe elegir practicantes a asignar";
                    }

                    if (next) {
                        $.ajax({
                            type: "POST",
                            data: {
                                type: "assign_student",
                                monitor: monitores,
                                practicant: practicantes,
                                instance: instance,
                                student: student

                            },
                            url: "../managers/ases_report/asesreport.php",
                            success: function(msg) {
                                alert(msg);
                                self.load_not_assigned_students(instance_id);
                            },

                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                alert("Debe seleccionar solo un practicante y monitor a la vez");
                                console.log("Error al asignar estudiantes" + msg);
                            },
                        });
                    } else {
                        alert(msg);
                    }
                });

                //Controles para la tabla generada
                $(document).on('click', '#tableAssign tbody tr td', function() {
                    var pagina = "student_profile.php";
                    var table = $("#tableAssign").DataTable();
                    var colIndex = table.cell(this).index().column;

                    if (colIndex <= 2) {
                        $("#formulario").each(function() {
                            this.reset;
                        });
                        location.href = pagina + location.search + "&student_code=" + table.cell(table.row(this).index(), 0).data();
                    }
                })


                //Despliega monitores deacuerdo al practicante seleccionado

                $(document).on('change', '#tableAssign tbody tr td select#practicants', function() {

                    var user = $(this).val();
                    var source = "list_monitors";
                    var instancia = instance_id;

                    $.ajax({
                        type: "POST",
                        data: {
                            user: user,
                            instance: instancia,
                            source: source
                        },
                        url: "../managers/ases_report/asesreport.php",
                        success: function(msg) {
                            $("select#monitors").find('option').remove().end();
                            $("select#monitors").append(msg);
                        },
                        dataType: "json",
                        cache: "false",
                        error: function(msg) {
                            alert("Error al cargar monitores con practicante seleccionado")
                        },
                    });

                });


            },

            create_assign_table: function(instance_id) {

                var dataString = $('#form_general_report').serializeArray();

                dataString.push({
                    name: 'instance_id',
                    value: instance_id
                });

                $("#not_assigned_students").html('<img class="icon-loading" src="../icon/loading.gif"/>');
                $.ajax({
                    type: "POST",
                    data: dataString,
                    url: "../managers/ases_report/load_not_assigned_students.php",
                    success: function(msg) {
                        $("#not_assigned_students").html('');
                        $("#not_assigned_students").fadeIn(1000).append('<table id="tableAssign" class="display" cellspacing="0" width="100%"><thead> </thead></table>');


                        var table = $("#tableAssign").DataTable(msg);

                    },

                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cargar estudiantes no asignados")
                    },
                });
            },
            load_not_assigned_students: function(instance_id) {

                var dataString = $('#form_general_report').serializeArray();

                dataString.push({
                    name: 'instance_id',
                    value: instance_id
                });

                $("#not_assigned_students").html('<img class="icon-loading" src="../icon/loading.gif"/>');
                $.ajax({
                    type: "POST",
                    data: dataString,
                    url: "../managers/ases_report/load_not_assigned_students.php",
                    success: function(msg) {
                        $("#not_assigned_students").html('');
                        $("#not_assigned_students").fadeIn(1000).append('<table id="tableAssign" class="display" cellspacing="0" width="100%"><thead> </thead></table>');


                        var table = $("#tableAssign").DataTable(msg);

                    },

                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cargar estudiantes no asignados")
                    },
                });

            }, 


            //Check the current instance
            get_id_instance: function() {
                var urlParameters = location.search.split('&');
                for (var x in urlParameters) {
                    if (urlParameters[x].indexOf('instanceid') >= 0) {
                        var intanceparameter = urlParameters[x].split('=');
                        return intanceparameter[1];
                    }
                }
                return 0;
            }
        };
    });

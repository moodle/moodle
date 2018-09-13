// Standard license block omitted.
/*
 * @package    block_ases/instanceconfiguration_main
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/instanceconfiguration_main
 */
define(['jquery', 'block_ases/bootstrap', 'block_ases/jquery.dataTables', 'block_ases/sweetalert','block_ases/dphpforms_form_renderer'],
    function($, bootstrap, datatables, swal,renderer_forms) {

        return {

            init: function() {

                var self = this;
                var instance_id = self.get_id_instance();
                var list_attendance = self.generate_attendance_table();
                var form = $('#modal_v2_groupal_tracking').find('form').find('h1').after(list_attendance);
                

                self.load_attendance_list(undefined, undefined, instance_id);
                self.load_students_of_monitor(instance_id);

            },
            //Load the list of students assigned to the current monitor.
            load_students_of_monitor: function(instance) {
                var data = new Array();
                data.push({
                    name: "function",
                    value: "load_grupal"
                });
                data.push({
                    name: "idinstancia",
                    value: instance
                });

                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/usermanagement_report.php",
                    success: function(msg) {
                        $('#mytable tbody').html('');
                        if (msg.rows != 0) {

                            var content = msg.content;
                            for (x in content) {

                                $('#mytable tbody').append("<tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td class=\"hide\">" + content[x].idtalentos + "</td> </tr>");
                            }

                        } else {
                            $('#list_grupal_seg').append("<a>No registra ningún estudiante a su cargo.Por favor dirigete a la oficina de Sistemas de talentos pilos para gestionar su situación</a>");
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cargar lista de estudiantes asignados del monitor");
                    },
                });

            },
            //Generates the student attendance table in the modal
            generate_attendance_table: function() {
                var list_attendance = '';
                list_attendance += '<div class="mymodal-body mymodal-body_v2 col-sm-12">';
                list_attendance += '<div class="row">';
                list_attendance += '<div class= "form-group">';
                list_attendance += '<div data-spy="scroll" data-target=".navbar" data-offset="50" class="well col-md-12" id="list_grupal_seg_consult" name="list_grupal_seg_consult">';
                list_attendance += '<table id="list_students_attendance" class="table table-striped">';
                list_attendance += '<thead>';
                list_attendance += '<tr>';
                list_attendance += '<th>Código</th><th>Nombre</th><th>Apellido</th><th>Presente</th>';
                list_attendance += '</tr>';
                list_attendance += '</thead>';
                list_attendance += '<tbody id="mytbody"></tbody></table>';
                list_attendance += '</div></div></div></div>';

                return list_attendance;

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
            },

            //Load students to the student attendance chart in the modal
            load_attendance_list: function(list, editable, instance) {

                if (list === undefined) {
                    list = null;
                }

                if (editable === undefined) {
                    editable = null;
                }

                var data = new Array();

                data.push({
                    name: "function",
                    value: "load_grupal"
                });
                data.push({
                    name: "idinstancia",
                    value: instance
                });

                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/usermanagement_report.php",
                    success: function(msg) {
                        $('#list_students_attendance tbody').html('');

                        if (msg.rows != 0) {

                            var content = msg.content;
                            if (!list) {
                                for (x in content) {
                                    $('#modal_v2_groupal_tracking #list_students_attendance tbody').append("<tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td><input type=\"checkbox\" id=\"asistencia\" name=\"asistencia\" value=\"" + content[x].idtalentos + "\"/></td>  </tr>");
                                }
                            } else {
                                for (x in content) {

                                    if (list.indexOf(x) != -1) {
                                        $('#modal_v2_groupal_tracking #list_students_attendance tbody').append(" <tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td><input type=\"checkbox\" checked=\"checked\" id=\"asistencia\" name=\"asistencia\"  value=\"" + content[x].idtalentos + "\"/></td>  </tr>");
                                    } else {
                                        $('#modal_v2_groupal_tracking #list_students_attendance tbody').append("<tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td><input type=\"checkbox\" id=\"asistencia\" name=\"asistencia\" value=\"" + content[x].idtalentos + "\"/></td>  </tr>");
                                    }
                                }
                            }
                        } else {
                            $('#modal_v2_groupal_tracking #list_students_attendance').append("<a>No registra ningun estudiante a su cargo.Por favor dirigete a la oficina de Sistemas de talentos pilos para gestionar tu situación</a>");
                        }

                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("error al cargar listado de asistencia");
                    },
                });
            }
        };
    });
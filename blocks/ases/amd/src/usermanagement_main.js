// Standard license block omitted.
/*
 * @package    block_ases/usermanagement
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/usermanagement_main
 */
define(['jquery', 'block_ases/bootstrap', 'block_ases/jquery.dataTables', 'block_ases/sweetalert', 'block_ases/select2'], function($, bootstrap, datatables, sweetalert, select2) {

    var students;

    return {
        init: function() {

            $('#collapse_div').removeClass('hidden');

            $("#users").select2({
                language: {

                    noResults: function() {

                        return "No hay resultado";
                    },
                    searching: function() {

                        return "Buscando..";
                    }
                },
                dropdownAutoWidth: true,
            });


            $(document).ready(function() {

                var roleLoaded = false;

                $('#academic_program_li').css({
                    display: 'none'
                });
                $(".assignment_li").css({
                    display: 'none'
                });

                $("#form_mon_student").css({
                    display: 'none'
                });

                $("#search_button").on('click', function() {

                    $("#search_button").prop("disabled", true);
                    if (!roleLoaded) {
                        roleLoad();
                    }
                    userLoad();
                    $('#users').prop('disabled', true);
                    $(".assignment_li").show();
                });

                $("#ok-button").on('click', function() {
                    var rolchanged = $('#role_select').val();
                    userLoad(null, function(msg) {

                        if (msg.rol == 'monitor_ps' && msg.rol != rolchanged) {
                            var currentUser = new Array();
                            currentUser.id = $('#user_id').val();
                            currentUser.username = $("#users").val();
                            valdateStudentMonitor(currentUser, false);
                        } else {
                                updateRolUser();
                                load_users();
                        }
                    });

                });
                $("#cancel-button").on('click', function() {
                    $(".assignment_li").addClass('hidden');
                    $('#boss_li').fadeOut();
                    $("#form_mon_student").fadeOut();
                    $('#users').val('').trigger('change.select2');
                    $('#users').prop('disabled', false);
                    $("#search_button").prop("disabled", false);
                    $('#name_lastname').val(" ");
                    $("#form_prof_type").fadeOut();

                });

                $("#form_mon_estudiante").css({
                    display: 'none'
                });
                $("#form_prof_type").css({
                    display: 'none'
                });

                $("#role_select").on('change', function() {

                    if ($("#role_select").val() == "monitor_ps") {
                        $("#form_prof_type").fadeOut();
                        $("#form_mon_student").fadeIn();
                        get_boss(4);
                        $('#boss_li').fadeIn();
                        $('#academic_program_li').fadeOut();
                    } else if ($("#role_select").val() == "profesional_ps") {
                        $("#form_prof_type").fadeIn();
                        $('#boss_li').fadeOut();
                        $("#form_mon_student").fadeOut();
                        $('#academic_program_li').fadeOut();
                    } else if ($("#role_select").val() == "practicante_ps") {
                        $("#form_prof_type").fadeOut();
                        $("#form_mon_student").fadeOut();
                        get_boss(7);
                        $('#boss_li').fadeIn();
                        $('#academic_program_li').fadeOut();
                    } else if($('#role_select').val() == "director_prog"){
                        $('#form_prof_type').fadeOut();
                        $('#form_mon_student').fadeOut();
                        $('#boss_li').fadeOut();
                        $('#academic_program_li').fadeIn();
                    }else {
                        $('#boss_li').fadeOut();
                        $("#form_mon_student").fadeOut();
                        $("#form_prof_type").fadeOut();
                        $('#academic_program_li').fadeOut();
                    }
                });
                $("#list-users-panel").on('click', function() {
                    load_users();
                });


                $('#div_users').on('click', '#delete_user', function() {

                    var table = $("#div_users #tableUsers").DataTable();
                    var td = $(this).parent();
                    var childrenid = $(this).children('span').attr('id');
                    var colIndex = table.cell(td).index().column;

                    var username = table.cell(table.row(td).index(), 0).data();
                    var firstname = table.cell(table.row(td).index(), 1).data();
                    var lastname = table.cell(table.row(td).index(), 2).data();
                    var rol = table.cell(table.row(td).index(), 3).data();
                    var currentUser = new Array();
                    currentUser.id = childrenid;
                    currentUser.username = username;

                    swal({
                            title: "Estas seguro/a?",
                            text: "Al usuario <strong>" + firstname + " " + lastname + "</strong> con código <strong>" + username + "</strong> se le inhabilitará los permisos del rol <strong>" + rol + "</strong>.<br><strong>¿Estás de acuerdo con los cambios que se efectuarán?</strong>",
                            type: "warning",
                            html: true,
                            showCancelButton: true,
                            confirmButtonColor: "#d51b23",
                            confirmButtonText: "Si!",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                userLoad(username, function(msg) {
                                    currentUser.rol = msg.rol;
                                    var rol = msg.rol;
                                    switch (rol) {
                                        case 'monitor_ps':
                                            valdateStudentMonitor(currentUser, true);
                                            break;
                                        case 'profesional_ps':
                                            deleteProfesional(currentUser);
                                            break;
                                        default:
                                            deleteOtheruser(currentUser);
                                    }
                                });
                            }
                        }
                    );


                });

            });

            $("body").on("click", ".eliminar_add_fields", function(e) { //click en eliminar campo
                    var count = $("#contenedor_add_fields div").length + 1;
                    var student = $(this).parent('div').children('input').val();
                    var parent = $(this).parent('div');
                    if (student) {
                        swal({
                                title: "Estas seguro/a?",
                                text: "Se desvinculará el prensente monitor del estudiante con codigo " + student,
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#d51b23",
                                confirmButtonText: "Si!",
                                cancelButtonText: "No",
                                closeOnConfirm: true,
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    deleteStudent(student);
                                    parent.remove(); //eliminar el campo
                                    count--;
                                }
                            });
                    }
                });

                $("#agregarCampo").click(function() {
                    $.ajax({
                        type: "POST",
                        data: {
                            function: "students_consult",
                            instancia: getIdinstancia()
                        },
                        url: "../managers/user_management/usermanagement_report.php",
                        success: function(msg) {
                            students = msg;
                            student_asignment(students);

                        },
                        dataType: "json",
                        cache: "false",
                        error: function(msg) {
                            alert("error al consultar estudiantes")
                        },
                    });
                });


            function roleLoad() {
                $.ajax({
                    type: "POST",
                    url: "../managers/user_management/load_role.php",
                    async: false,
                    success: function(msg) {
                        $('#role_select').empty();
                        for (role in msg) {
                            var html = "<option value=\"" + msg[role].nombre_rol + "\">" + msg[role].nombre_rol + "</option>";
                            $('#role_select').append(html);
                        }
                        roleLoaded = true;
                    },
                    dataType: "json",
                    error: function(msg) {
                        alert("error al cargar roles");
                    }
                })
            }

            function userLoad(username, callback) {
                var dataString = username;
                if (!dataString) {
                    dataString = $("#users").val();
                }
                $.ajax({
                    type: "POST",
                    data: {
                        dat: dataString,
                        idinstancia: getIdinstancia()
                    },
                    url: "../managers/user_management/search_user.php",
                    success: function(msg) {

                        if (callback) {
                            callback(msg);
                        } else {
                            if (!msg.error) {
                                $('#contenedor_add_fields').html('');

                                if (msg.firstname == "") {
                                    swal("Error", "El usuario no existe en la base de datos", "error");
                                    $('#users').prop('disabled', false);
                                } else {
                                    $('.assignment_li').removeClass('hidden');
                                    $('#name_lastname').val(msg.firstname + " " + msg.lastname);
                                    $('#user_id').val(msg.id);
                                    if (msg.rol == "") {
                                        $('#role_select').val("no_asignado");
                                    } else {

                                        if (msg.rol == "profesional_ps") {
                                            $('#select_prof_type').val(msg.profesion);
                                            $("#form_prof_type").fadeIn();
                                            $('#boss_li').fadeOut();
                                            $("#form_mon_student").fadeOut();
                                            $('#academic_program_li').fadeOut();
                                        } else if (msg.rol == "monitor_ps") {
                                            loadStudents();
                                            get_boss(4, msg.boss);
                                            $("#form_mon_student").fadeIn();
                                            $("#form_prof_type").fadeOut();
                                            $('#academic_program_li').fadeOut();
                                        } else if (msg.rol == "practicante_ps") {
                                            get_boss(7, msg.boss);
                                            $("#form_mon_student").fadeOut();
                                            $("#form_prof_type").fadeOut();
                                            $('#academic_program_li').fadeOut();
                                        } else if(msg.rol == "director_prog"){
                                            $("#form_mon_student").fadeOut();
                                            $("#form_prof_type").fadeOut();
                                            $('#boss_li').fadeOut();
                                            $('#academic_program_li').fadeIn();
                                        } else {
                                            $('#boss_li').fadeOut();
                                            $("#form_mon_student").fadeOut();
                                            $("#form_prof_type").fadeOut();
                                        }
                                        $('#role_select').val(msg.rol);
                                    }
                                }

                            } else {
                                swal("Error", msg.error, "error");
                                $(".assignment_li").addClass('hidden');
                                $("#form_mon_student").css({
                                    display: 'none'
                                });

                                $('#users').val('').trigger('change.select2');
                                $('#users').prop('disabled', false);
                                $('#name_lastname').val(" ");
                                $("#search_button").prop("disabled", false);
                            }
                        }

                    },
                    dataType: "json",
                    error: function(msg) {
                        swal("Error", "El usuario no existe en la base de datos", "error");
                        $(".assignment_li").addClass('hidden');
                        $("#form_mon_student").css({
                            display: 'none'
                        });

                        $('#users').val('').trigger('change.select2');
                        $('#users').prop('disabled', false);
                        $('#name_lastname').val(" ");
                        $("#search_button").prop("disabled", false);
                    }
                });
            }

            function updateRolUser() {

                var dataRole = $('#role_select').val();
                var dataUsername = $('#users').val();
                var dataStudents = new Array();

                        $('input[name="array_students[]"]').each(function() {
                        dataStudents.push($(this).val().split(" - ")[0]);

                    });

                    $('select[name="array_students[]"]').each(function() {
                        dataStudents.push($(this).val().split(" - ")[0]);
                    });

                    if (dataRole == "profesional_ps") {

                        var dataProfessional = $('#select_prof_type').val();
                        if (dataProfessional == "no_asignado") {
                            swal("Error", "El usuario no tiene un \"tipo de profesional\" asignado, debe asignar un \"tipo de profesional\".", "error")
                        } else {
                            var data = {
                                role: dataRole,
                                username: dataUsername,
                                professional: dataProfessional,
                                idinstancia: getIdinstancia()
                            };
                            $.ajax({
                                type: "POST",

                                data: data,
                                url: "../managers/user_management/update_role_user.php",
                                success: function(msg) {

                                    swal("Información!", msg, "info");
                                    userLoad(dataUsername);
                                },
                                dataType: "text",
                                cache: "false",
                                error: function(msg) {
                                    swal("Error", "Ha ocurrido un error asignando profesional", "error")
                                },
                            });
                        }
                    } else if (dataRole == "monitor_ps") {
                        var boss_id = $('#boss_select').val();

                        $.ajax({
                            type: "POST",
                            data: {
                                role: dataRole,
                                username: dataUsername,
                                students: dataStudents,
                                boss: boss_id,
                                idinstancia: getIdinstancia()
                            },
                            url: "../managers/user_management/update_role_user.php",
                            success: function(msg) {

                                swal({
                                    title: "Información!",
                                    text: msg,
                                    type: "info",
                                    html: true,
                                    confirmButtonColor: "#d51b23",
                                    confirmButtonText: "Ok!",
                                    closeOnConfirm: true
                                });
                                userLoad(dataUsername);

                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                swal("Error", "Ha ocurrido un error", "error")
                            },
                        });
                    } else if (dataRole == "practicante_ps") {
                        var boss_id = $('#boss_select').val();

                        $.ajax({
                            type: "POST",
                            data: {
                                role: dataRole,
                                username: dataUsername,
                                boss: boss_id,
                                idinstancia: getIdinstancia()
                            },
                            url: "../managers/user_management/update_role_user.php",
                            success: function(msg) {
                                swal({
                                    title: "Información!",
                                    text: msg,
                                    type: "info",
                                    html: true,
                                    confirmButtonColor: "#d51b23",
                                    confirmButtonText: "Ok!",
                                    closeOnConfirm: true
                                });
                                userLoad(dataUsername);

                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                swal("Error", "Ha ocurrido un error", "error")
                            },
                        });
                    } else if(dataRole == "director_prog"){

                        var academic_program_id = $('#academic_program_select').val();

                        $.ajax({
                            type: "POST",
                            data: {
                                role: dataRole,
                                username: dataUsername,
                                academic_program: academic_program_id,
                                idinstancia: getIdinstancia()
                            },
                            url: "../managers/user_management/update_role_user.php",
                            success: function(msg) {
                                swal({
                                    title: "Información!",
                                    text: msg,
                                    type: "info",
                                    html: true,
                                    confirmButtonColor: "#d51b23",
                                    confirmButtonText: "Ok!",
                                    closeOnConfirm: true
                                });
                                userLoad(dataUsername);
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                swal("Error", "Ha ocurrido un error", "error")
                            },
                        });
                    } else {
                        $.ajax({
                            type: "POST",
                            data: {
                                role: dataRole,
                                username: dataUsername,
                                idinstancia: getIdinstancia()
                            },
                            url: "../managers/user_management/update_role_user.php",
                            success: function(msg) {
                                swal("Información!", msg, "info");
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                swal("Error", "Ha ocurrido un error", "error")
                            },
                        });
                    }
            }

            function create_select2(name) {

                $("#" + name).select2({
                    language: {
                        noResults: function() {
                            return "No hay resultado";
                        },
                        searching: function() {
                            return "Buscando..";
                        }
                    },
                    dropdownAutoWidth: true,
                });
            }

            function student_asignment(students) {

                var contenedor = $("#contenedor_add_fields"); //ID del contenedor
                var count = $(".inputs_students").length + 1;
                var FieldCount = count; //para el seguimiento de los campos

                    FieldCount++;
                    var text = '<option value="-1">-----------------------</option>';

                    for (var student in students) {
                        text += '<option value="' + students[student].username + '">' + students[student].username + ' - ' + students[student].firstname + ' ' + '' + students[student].lastname + '</option>';
                    }

                    $("#contenedor_add_fields").append('<div class="select-pilos"><select class="inputs_students" name="array_students[]" id="campo_' + FieldCount + '"">' + text + '</select></div>');
                    create_select2('campo_' + FieldCount);
                    count++;

            }

            function loadStudents() {
                var data = new Array();
                var user_id = $('#user_id').val();

                data.push({
                    name: "function",
                    value: "load_grupal"
                });
                data.push({
                    name: "user_management",
                    value: user_id
                });
                data.push({
                    name: "idinstancia",
                    value: getIdinstancia()
                });

                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/usermanagement_report.php",
                    success: function(msg) {

                        $('#contenedor_add_fields').html('');
                        if (msg.rows != 0) {

                            var text = "";
                            for (var student in students) {
                                text += '<option value="' + students[student].username + '">' + students[student].username + ' - ' + students[student].firstname + ' ' + '' + students[student].lastname + '</option>';
                            }

                            var content = msg.content;
                            for (x in content) {

                                $('#contenedor_add_fields').append('<div id="contenedor_add_fields"> <div class="added_add_fields"> <input type="text"  class="inputs_students" name="array_students[]" id="campo_1" value="' + content[x].username + ' - ' + content[x].firstname + ' ' + content[x].lastname + '" readonly/> <a href="#" class="eliminar_add_fields"><img src="../icon/ico_wrong.png"></a> </div> </div>');

                            }

                        } else {}
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("error al cargar estudiantes")
                    },
                });
            }

            function get_boss(role, selected) {
                var data = new Array();
                var selected_index = 0;
                data.push({
                    name: "function",
                    value: "cargar"
                });
                data.push({
                    name: "role",
                    value: role
                });
                data.push({
                    name: "idinstancia",
                    value: getIdinstancia()
                });
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/search_user.php",
                    success: function(msg) {

                        $('#boss_select').html('');
                        $('#boss_select').append('<option value="ninguno">Ninguno</option>');
                        for (x in msg) {
                            var firstnamearray = msg[x].firstname.split(" ");
                            var lastnamearray = msg[x].lastname.split(" ");
                            var isequal = false;
                            if (selected == msg[x].id) {
                                //$('#boss_select').append('<option value="'+msg[x].id+'" selected>'+msg[x].username+'-'+firstnamearray[0]+' '+lastnamearray[0]+'-'+msg[x].nombre_profesional+'</option>');
                                $('#boss_select').append('<option value="' + msg[x].id + '" selected>' + msg[x].username + '-' + firstnamearray[0] + ' ' + lastnamearray[0] + '</option>');
                                isequal = true;
                                selected_index = msg[x].id;
                            } else {
                                //$('#boss_select').append('<option value="'+msg[x].id+'" >'+msg[x].username+'-'+firstnamearray[0]+' '+lastnamearray[0]+'-'+msg[x].nombre_profesional+'</option>');
                                $('#boss_select').append('<option value="' + msg[x].id + '" >' + msg[x].username + '-' + firstnamearray[0] + ' ' + lastnamearray[0] + '</option>');
                            }
                        }

                        $('#boss_li').removeClass('hide');
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("error al cargar profesionales")
                    },
                });
            }

            function deleteStudent(student) {
                var data = new Array();
                var user_id = $('#user_id').val();

                var dataUsername = $('#users').val();
                data.push({
                    name: "deleteStudent",
                    value: "delete"
                });
                data.push({
                    name: "student",
                    value: student.split(" - ")[0]
                });
                data.push({
                    name: "username",
                    value: dataUsername
                });
                data.push({
                    name: 'idinstancia',
                    value: getIdinstancia()
                });
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/update_role_user.php",
                    success: function(msg) {

                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al eliminar estudiante")
                    },
                });
            }

            function load_users() {
                $.ajax({
                    type: "POST",
                    data: {
                        idinstancia: getIdinstancia()
                    },
                    url: "../managers/user_management/load_role_users.php",
                    success: function(msg) {
                        $("#div_users").empty();
                        $("#div_users").append('<table id="tableUsers" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                        var table = $("#tableUsers").DataTable(msg);
                        $('#div_users #delete_user').css('cursor', 'pointer');
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cargar usuarios")
                    },
                })
            }

            function valdateStudentMonitor(currentUser, isdelete) {
                var data = new Array();
                data.push({
                    name: "function",
                    value: "load_grupal"
                });
                data.push({
                    name: "user_management",
                    value: currentUser.id
                });
                data.push({
                    name: 'idinstancia',
                    value: getIdinstancia()
                });
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/usermanagement_report.php",
                    success: function(msg) {
                        //console.log(msg);
                        //deleteMonitor(username,msg);
                        if (msg.rows != 0) {
                            var data = msg.content;

                            var message = '<p align=justify">Se ha encontrado que el usuario tiene asignado el rol MONITOR y tiene a cargo los siguientes estudiantes:</p><br><br> ';
                            message += '<div class="pre-scrollable" style="max-height:200px"><table id="tableStudent" class="table table-striped"> <thead> <tr> <th>Código</th> <th>Nombre</th> <th>Apellido</th></tr> </thead><tbody>';

                            for (x in data) {
                                message += '<tr> <td>' + data[x].username + '</td> <td>' + data[x].firstname + '</td> <td>' + data[x].lastname + '</td> </tr>';
                            }
                            message += '</tbody></table></div><br><br><p align=justify">Para continuar se creará un nuevo usuario con rol monitor quien se hará a cargo de los anteriores estudiante(s).<br> Por favor escribe el código del nuevo usuario:</p>';
                            var title = "";
                            if (isdelete) {
                                title = "Antes de eliminar!";
                            } else {
                                title = "Antes de Actualizar!";
                            }

                            swal({
                                    title: title,
                                    html: true,
                                    text: message,
                                    type: "input",
                                    showCancelButton: true,
                                    closeOnConfirm: false,
                                    confirmButtonColor: "#d51b23",
                                    confirmButtonText: "Continuar",
                                    cancelButtonText: "Cancelar!",
                                    animation: "slide-from-top",
                                    inputPlaceholder: "Código"
                                },
                                function(inputValue) {


                                    if (inputValue === false) return false;

                                    if (inputValue === "") {
                                        swal.showInputError("Escibe el código del nuevo monitor!");
                                        return false;
                                    }

                                    userLoad(inputValue, function(msg) {
                                        if (msg.error) {
                                            swal.showInputError(msg.error)
                                        } else {

                                            confirmNewMonitor(msg, currentUser, isdelete);
                                        };

                                        return false;
                                    });

                                    //swal("Nice!", "You wrote: " + inputValue, "success");
                                });
                        } else {
                            deleteMonitorWithoutStudents(currentUser,getIdinstancia());
                            updateRolUser();
                            load_users();
                        }



                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("error al validar monitor")
                    },
                });
            }

            function confirmNewMonitor(newUser, currentUser, isdelete) {
                var message = "";
                if (newUser.rol == 'ninguno') {
                    message = 'El usuario <strong>' + newUser.firstname + ' ' + newUser.lastname + '</strong> con código <strong>' + newUser.username + '</strong> se le asiganara el rol monitor y tendrá a cargo los estudiantes del anterior monitor<br>¿Está de acuerdo con los cambios que se efectuarán?';
                } else {
                    message = 'El usuario <strong>' + newUser.firstname + ' ' + newUser.lastname + '</strong> con código <strong>' + newUser.username + '</strong> ya tiene el rol <strong>' + newUser.rol + '</strong>  en el sistema.<br>Perderá el presente rol y se le asiganará el rol monitor.<br> Tendrá a cargo los estudiantes del anterior monitor.<br><br><strong>¿Estás de acuerdo con los cambios que se efectuarán?</strong>';
                }


                swal({
                        title: "Estás seguro/a?",
                        text: message,
                        type: "warning",
                        html: true,
                        showCancelButton: true,
                        confirmButtonColor: "#d51b23",
                        confirmButtonText: "Si!",
                        cancelButtonText: "Atras!",
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        animation: "slide-from-top",
                    },
                    function(isConfirm) {
                        if (isConfirm) {

                            changeMonitor(newUser, currentUser, isdelete);

                        } else {
                            valdateStudentMonitor(currentUser, isdelete);
                        }
                    });
            }

            function deleteMonitorWithoutStudents(currentUser,isdelete){
             var data = new Array();
                data.push({
                    name: 'deleteMonitorWithoutStudents',
                    value: 'deleteMonitorWithoutStudents'
                });
                data.push({
                  name: 'oldUser',
                  value: JSON.stringify([currentUser.id, currentUser.username])
                });
                data.push({
                    name: 'idinstancia',
                    value: getIdinstancia()
                });
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/update_role_user.php",
                    success: function(msg) {
                        if (msg == 1) {
                                swal("Hecho!", "El usuario ha sido eliminado satisfactoriamente.", "success");
                                updateRolUser();
                                load_users();
                        } else {
                            swal("Error!", msg, "error");
                        }


                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al eliminar monitor sin estudiantes asignados")
                    },
                });
            }

            function changeMonitor(newUser, currentUser, isdelete) {
                var data = new Array();
                data.push({
                    name: 'changeMonitor',
                    value: 'changeMonitor'
                });
                data.push({
                    name: 'oldUser',
                    value: JSON.stringify([currentUser.id, currentUser.username])
                });
                data.push({
                    name: 'newUser',
                    value: JSON.stringify([newUser.id, newUser.username])
                });
                data.push({
                    name: 'isdelete',
                    value: isdelete
                });
                data.push({
                    name: 'idinstancia',
                    value: getIdinstancia()
                });
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/update_role_user.php",
                    success: function(msg) {
                        if (msg == 1) {
                            if (isdelete) {
                                swal("Eliminado!", "El proceso se eliminación de completó satisfactoriamente.", "success")
                            } else {
                                swal("Hecho!", "El proceso de reasignación de estudiantes se completó satisfactoriamente. Por favor actualiza y guarda el nuevo rol", "success");
                                updateRolUser();
                                load_users();
                            }


                            load_users();
                        } else {
                            swal("Error!", msg, "error");
                        }


                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cambiar monitor")
                    },
                });
            }

            function deleteProfesional(currentUser) {
                var data = new Array();
                data.push({
                    name: 'deleteProfesional',
                    value: 'deleteProfesional'
                });
                data.push({
                    name: 'user',
                    value: JSON.stringify([currentUser.id, currentUser.username])
                });
                data.push({
                    name: 'idinstancia',
                    value: getIdinstancia()
                });
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/update_role_user.php",
                    success: function(msg) {
                        if (msg == 1) {
                            swal("Eliminado!", "El proceso se eliminación de completó satisfactoriamente.", "success");
                            load_users();
                        } else {
                            swal("Error!", msg, "error");
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al eliminar profesional")
                    },
                });
            }

            function deleteOtheruser(currentUser) {
                var data = new Array();
                data.push({
                    name: 'deleteOtheruser',
                    value: 'deleteOtheruser'
                });
                data.push({
                    name: 'user',
                    value: JSON.stringify([currentUser.id, currentUser.username, currentUser.rol])
                });
                data.push({
                    name: 'idinstancia',
                    value: getIdinstancia()
                });
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/update_role_user.php",
                    success: function(msg) {

                        if (msg == 1) {
                            swal("Eliminado!", "El proceso se eliminación de completó satisfactoriamente.", "success");
                            load_users();
                        } else {
                            swal("Error!", msg, "error");
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al eliminar otros usuarios")
                    },
                });
            }


            function getIdinstancia() {
                var urlParameters = location.search.split('&');

                for (x in urlParameters) {
                    if (urlParameters[x].indexOf('instanceid') >= 0) {
                        var intanceparameter = urlParameters[x].split('=');
                        return intanceparameter[1];
                    }
                }
                return 0;
            }

        }
    };
});

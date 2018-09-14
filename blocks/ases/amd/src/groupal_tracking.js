// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/groupal_tracking
 */
define(['jquery', 'block_ases/bootstrap', 'block_ases/validator', 'block_ases/sweetalert'], function($, bootstrap, validator, sweetalert) {


    return {
        init: function() {

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
                        console.log( $(this).parent('.mymodal') );
                    }
                  });
                
            });

            $(document).ready(function() {
                load_students();
                loadAll_seg();

                $('#socioedu_add_grupal').click(function() {
                    $('#save_seg').removeClass("hide");
                    $('#div_created').addClass('hide');
                    $('#upd_seg').addClass('hide');
                    $('#myModalLabel').attr('name', 'GRUPAL');
                    initFormSeg();
                    load_attendance_list();
                });

                $('#close_seg').on('click', function() {
                    $('#edit_seg').addClass('hide');
                });

                $('#go_back').on('click', function() {
                    window.history.back();
                });

                $('#edit_seg').click(function() {
                    var students_id = new Array();
                    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
                    var id_seg = $(this).prop('name');

                    $('#seguimiento input[id=asistencia]:checked').each(
                        function() {
                            var id = $(this).val();
                            students_id.push(id);
                        }
                    );
                    $('#myModalLabel').attr('name', 'GRUPAL');
                    var tipo = $('#myModalLabel').attr('name');
                    var data = $('#seguimiento').serializeArray();
                    data.push({
                        name: "function",
                        value: "update"
                    });
                    data.push({
                        name: "tipo",
                        value: tipo
                    });
                    data.push({
                        name: "id_seg",
                        value: id_seg
                    });
                    data.push({
                        name: "idtalentos",
                        value: students_id
                    });
                    data.push({
                        name: "idinstancia",
                        value: parameters.instanceid
                    });
                    data.push({
                        name: "idmonitor",
                        value: 120
                    });


                    var validation = validateModal(data);
                    if (validation.isvalid) {
                        $.ajax({
                            type: "POST",
                            data: data,
                            url: "../managers/seguimiento_grupal/groupal_trackings_report.php",
                            success: function(msg) {
                                var error = msg.error;
                                if (!error) {
                                    swal({
                                        title: "Actualizado con exito!!!",
                                        html: true,
                                        type: "success",
                                        text: msg.msg,
                                        confirmButtonColor: "#d51b23"
                                    });
                                    $('#myModal').modal('toggle');
                                    $('#myModal').modal('toggle');
                                    $('#save_seg').addClass('hide');
                                    $('.modal-backdrop').remove();
                                    loadAll_seg();
                                } else {
                                    swal({
                                        title: error,
                                        html: true,
                                        type: "error",
                                        text: msg.msg,
                                        confirmButtonColor: "#D3D3D3"
                                    });
                                }
                            },
                            dataType: "json",
                            cache: "false",
                            error: function(msg) {
                                alert("error al actualizar seguimiento");
                            },
                        });

                    } else {
                        swal({
                            title: "Error",
                            html: true,
                            type: "warning",
                            text: "Detalles del error:<br>" + validation.detalle,
                            confirmButtonColor: "#D3D3D3"
                        });
                    }
                });

                $('#save_seg').click(function() {
                    var students_id = new Array();
                    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

                    $('#seguimiento input[id=asistencia]:checked').each(
                        function() {
                            var id = $(this).val();
                            students_id.push(id);
                        }
                    );

                    var tipo = $('#myModalLabel').attr('name');
                    var data = $('#seguimiento').serializeArray();
                    data.push({
                        name: "function",
                        value: "new"
                    });
                    data.push({
                        name: "tipo",
                        value: tipo
                    });
                    data.push({
                        name: "idtalentos",
                        value: students_id
                    });
                    data.push({
                        name: "idinstancia",
                        value: parameters.instanceid
                    });
                    data.push({
                        name: "idmonitor",
                        value: 120
                    });

                    var validation = validateModal(data);
                    if (validation.isvalid) {
                        $.ajax({
                            type: "POST",
                            data: data,
                            url: "../managers/seguimiento_grupal/groupal_trackings_report.php",
                            success: function(msg) {
                                var error = msg.error;
                                if (!error) {
                                    swal({
                                        title: "Actualizado con exito!!",
                                        html: true,
                                        type: "success",
                                        text: msg.msg,
                                        confirmButtonColor: "#d51b23"
                                    });
                                    $('#myModal').modal('toggle');
                                    $('#myModal').modal('toggle');
                                    $('#save_seg').addClass('hide');
                                    $('.modal-backdrop').remove();
                                    loadAll_seg();
                                } else {
                                    swal({
                                        title: error,
                                        html: true,
                                        type: "error",
                                        text: msg.msg,
                                        confirmButtonColor: "#D3D3D3"
                                    });
                                }
                            },
                            dataType: "json",
                            cache: "false",
                            error: function(msg) {
                                alert("error al guardar seguimiento");
                            },
                        });

                    } else {
                        swal({
                            title: "Error",
                            html: true,
                            type: "warning",
                            text: "Detalles del error:<br>" + validation.detalle,
                            confirmButtonColor: "#D3D3D3"
                        });
                    }
                });

                $('#upd_seg').click(function() {
                    var id_seg = $(this).attr('name');
                    $("#seguimiento :input").prop("disabled", false);
                    var estudiantes = obtener_estudiantes();
                    load_attendance_list(estudiantes, true);
                    $('#edit_seg').removeClass('hide');
                    $('#upd_seg').addClass("hide");
                });
            });

            function obtener_estudiantes() {
                var estudiantes = [];
                $('#mytable_consult > tbody  > tr > td').each(function() {
                    if ($(this).attr('id') == "talentos") {
                        estudiantes.push($(this).html());
                    }
                });
                return estudiantes;
            }

            function getUrlParams(page) {
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


            function load_students() {
                var data = new Array();
                var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
                data.push({
                    name: "function",
                    value: "load_grupal"
                });
                data.push({
                    name: "idinstancia",
                    value: parameters.instanceid
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
                            $('#list_grupal_seg').append("<a>No registra ningun estudiante a su cargo.Por favor dirigete a la oficina de Sistemas de talentos pilos para gestionar tu situación</a>");
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("error al cargar estudiantes");
                    },
                });
            }


            function load_attendance_list(list, editable) {

                if(list === undefined){
                    list = null;
                }

                if(editable === undefined){
                    editable = null;
                }

                var data = new Array();
                var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

                data.push({
                    name: "function",
                    value: "load_grupal"
                });
                data.push({
                    name: "idinstancia",
                    value: parameters.instanceid
                });

                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/usermanagement_report.php",
                    success: function(msg) {
                        $('#seguimiento #mytable_consult tbody').html('');

                        if (msg.rows != 0) {

                            var content = msg.content;
                            if (!list) {
                                for (x in content) {
                                    $('#seguimiento #mytable_consult tbody').append("<tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td><input type=\"checkbox\" id=\"asistencia\" name=\"asistencia\" value=\"" + content[x].idtalentos + "\"/></td>  </tr>");
                                }
                            } else {
                                for (x in content) {

                                    if (list.indexOf(x) != -1) {
                                        $('#seguimiento #mytable_consult tbody').append(" <tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td><input type=\"checkbox\" checked=\"checked\" id=\"asistencia\" name=\"asistencia\"  value=\"" + content[x].idtalentos + "\"/></td>  </tr>");
                                    } else {
                                        $('#seguimiento #mytable_consult tbody').append("<tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td><input type=\"checkbox\" id=\"asistencia\" name=\"asistencia\" value=\"" + content[x].idtalentos + "\"/></td>  </tr>");
                                    }
                                }
                            }
                        } else {
                            $('#seguimiento #list_grupal_seg').append("<a>No registra ningun estudiante a su cargo.Por favor dirigete a la oficina de Sistemas de talentos pilos para gestionar tu situación</a>");
                        }
                        var id_seg = $('#upd_seg').prop('name');
                        $("#edit_seg").prop('name', id_seg);

                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("error al cargar listado de asistencia");
                    },
                });
            }

            function update_seg_grupal(id_seg) {

                var students_id = new Array();

                $('#seguimiento input[id=asistencia]:checked').each(
                    function() {
                        var id = $(this).val();
                        students_id.push(id);
                    }
                );

                var data = $('#myModal #seguimiento').serializeArray();

                data.push({
                    name: "id_seg",
                    value: id_seg
                });
                data.push({
                    name: "function",
                    value: "update"
                });
                data.push({
                    name: "tipo",
                    value: "GRUPAL"
                });
                data.push({
                    name: "idtalentos",
                    value: students_id
                });


                $.each(data, function(i, item) {
                    if (item.name == "optradio") {
                        item.value = $('#seguimiento input[name=optradio]:checked').parent().attr('id');
                    }
                });

                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/seguimiento_grupal/groupal_trackings_report.php",
                    success: function(msg) {
                        var error = msg.error;
                        if (!error) {
                            swal({
                                title: "Actualizado con exito!!",
                                html: true,
                                type: "success",
                                text: msg.msg,
                                confirmButtonColor: "#d51b23"
                            });
                            $('#myModal').modal('toggle');
                            $('#myModal').modal('toggle');
                            $('#upd_seg').addClass('hide');
                            $('.modal-backdrop').remove();
                            loadAll_seg();
                        } else {
                            swal({
                                title: error,
                                html: true,
                                type: "error",
                                text: msg.msg,
                                confirmButtonColor: "#D3D3D3"
                            });
                        }


                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert(msg);
                    },
                });
            }

            function initFormSeg() {

                var date = new Date();
                var day = date.getDate();
                var month = date.getMonth() + 1;
                var year = date.getFullYear();
                var minutes = date.getMinutes();
                var hour = date.getHours();

                //   // inicializar fecha

                //incializar hora
                var hora = "";
                for (var i = 0; i < 24; i++) {
                    if (i == hour) {
                        if (hour < 10) hour = "0" + hour;
                        hora += "<option value=\"" + hour + "\" selected>" + hour + "</option>";
                    } else if (i < 10) {
                        hora += "<option value=\"0" + i + "\">0" + i + "</option>";
                    } else {
                        hora += "<option value=\"" + i + "\">" + i + "</option>";
                    }
                }

                var min = "";
                for (var i = 0; i < 60; i++) {

                    if (i == minutes) {
                        if (minutes < 10) minutes = "0" + minutes;
                        min += "<option value=\"" + minutes + "\" selected>" + minutes + "</option>";
                    } else if (i < 10) {
                        min += "<option value=\"0" + i + "\">0" + i + "</option>";
                    } else {
                        min += "<option value=\"" + i + "\">" + i + "</option>";
                    }
                }


                $('#seguimiento #h_ini').append(hora);
                $('#seguimiento #m_ini').append(min);

                $('#seguimiento #h_fin').append(hora);
                $('#seguimiento #m_fin').append(min);

                $("#seguimiento").find("input:text, textarea").val('');
                $('#seguimiento #infomonitor').addClass('hide');
                $('#upd_seg').attr('disabled', false);
                $('#upd_seg').attr('title', '');
                $('#seguimiento').find('select, textarea, input').attr('disabled', false);

            }

            /*
            **Function that loads the list of follow-ups belonging to a monitor (from the OLD FORMS)  
            **
            */
            function loadAll_seg() {
                $('#list_grupal').html('');
                var data = new Array();
                var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

                data.push({
                    name: "function",
                    value: "loadSegMonitor"
                });
                data.push({
                    name: "tipo",
                    value: "GRUPAL"
                });
                data.push({
                    name: "idinstancia",
                    value: parameters.instanceid
                });
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/seguimiento_grupal/groupal_trackings_report.php",
                    success: function(msg) {
                        var error = msg.error;
                        if (!error) {

                            var result = msg.result;
                            var rows = msg.rows;
                            if (rows > 0) {
                                for (x in result) {

                                    $('#list_grupal').append('<div class="container well col-md-12"> <div class="container-fluid col-md-10" name="info"><div class="row"><label class="col-md-3" for="fecha_des">Fecha</label><label class="col-md-9" for="tema_des">Tema</label> </div> <div class="row"> <input type="text" class="col-md-3" value=' + result[x].fecha + ' id="fecha_seg" name="fecha_seg" disabled> <input type="text" class="col-md-9" value=' + result[x].tema + ' id="tema_seg" name="tema_seg" disabled> </div></div> <div id=' + result[x].id + ' class="col-md-2" name="div_button_seg"> <span class="btn btn-danger" id="consult_grupal" name="consult_grupal" data-toggle="modal" data-target="#myModal">Detalle</span><span class="btn btn-warning" id="delete_grupal" name="delete_grupal">Borrar</span> </div></div>');
                                }
                                $('#list_grupal').on('click', '#consult_grupal', function() {
                                    var id_seg = $(this).parent().attr('id');
                                    $('#upd_seg').removeClass('hide');
                                    $("#upd_seg").prop('name', id_seg);

                                    get_seguimiento(id_seg, 'GRUPAL');
                                });

                                $('#list_grupal').on('click', '#delete_grupal', function() {
                                    var id_seg = $(this).parent().attr('id');
                                    delete_seguimiento(id_seg);

                                });
                            } else {
                                $('#list_grupal').append("<label>No registra</label><br>");
                            }

                        } else {
                            swal({
                                title: error,
                                html: true,
                                type: "error",
                                text: msg.msg,
                                confirmButtonColor: "#D3D3D3"
                            });
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("error al cargar seguimiento monitor");
                    },
                });
            }


            function delete_seguimiento(id) {
                swal({
                        title: "¿Seguro que desea eliminar el registro?",
                        text: "No podrás deshacer este paso",
                        type: "warning",
                        showCancelButton: true,
                        cancelButtonText: "No",
                        confirmButtonColor: "#d51b23",
                        confirmButtonText: "Si",
                        closeOnConfirm: false
                    },


                    function() {

                        $.ajax({
                            type: "POST",
                            data: {
                                id: id,
                                "function": "delete",
                            },
                            url: "../../../blocks/ases/managers/seguimiento_grupal/groupal_trackings_report.php",
                            async: false,
                            success: function(msg) {
                                if (msg == 0) {
                                    swal({
                                        title: "error al borrar registro",
                                        html: true,
                                        type: "error",
                                        confirmButtonColor: "#d51b23"
                                    });
                                } else {

                                    setTimeout('document.location.reload()', 500);

                                    swal("¡Hecho!",
                                        "El registro ha sido eliminado",
                                        "success");
                                    //

                                }
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                alert("error al eliminar seguimiento")
                            },
                        });
                    });
            }

            function get_seguimiento(id_seg, tipo, instancia) {
                var data = new Array();
                var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
                $('#save_seg').addClass("hide");


                initFormSeg();

                data.push({
                    name: "function",
                    value: "getSeguimiento"
                });
                data.push({
                    name: "id",
                    value: id_seg
                });
                data.push({
                    name: "idinstancia",
                    value: parameters.instanceid
                });
                data.push({
                    name: "tipo",
                    value: tipo
                });
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/seguimiento_grupal/groupal_trackings_report.php",
                    success: function(msg) {
                        var error = msg.error;
                        if (!error) {
                            $("#place").val(msg.seguimiento.lugar);
                            $("#tema").val(msg.seguimiento.tema);
                            $("#objetivos").val(msg.seguimiento.objetivos);
                            $("#actividades").val(msg.seguimiento.actividades);
                            $("#observaciones").val(msg.seguimiento.observaciones);
                            $("#h_ini option[value=" + msg.hour.h_ini + "]").attr("selected", true);
                            $("#m_ini option[value=" + msg.hour.m_ini + "]").attr("selected", true);
                            $("#h_fin option[value=" + msg.hour.h_fin + "]").attr("selected", true);
                            $("#m_fin option[value=" + msg.hour.m_fin + "]").attr("selected", true);
                            $("#date").val(msg.hour.seguimiento.fecha);
                            $("#seguimiento :input").prop("disabled", true);
                            $('#mytable_consult tbody').html('');
                            if (msg.rows != 0) {
                                var content = msg.content;
                                for (x in content) {
                                    $('#mytable_consult tbody').append("<tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td id=\"talentos\"  class = \"hide\">" + content[x].idtalentos + "</td> </tr>");
                                }
                            } else {
                                $('#list_grupal_seg_consult').append("<a>No registra ningun estudiante a su cargo.Por favor dirigete a la oficina de Sistemas de talentos pilos para gestionar tu situación</a>");
                            }
                        } else {
                            swal({
                                title: error,
                                html: true,
                                type: "error",
                                text: msg.msg,
                                confirmButtonColor: "#D3D3D3"
                            });
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("error al ver detalles de seguimientos");
                    },
                });
            }


            function validateModal(data) {
                var isvalid = true;
                var detalle = "";
                var date, h_ini, m_ini, h_fin, m_fin, tema, objetivos, idtalentos, place;
                $.each(data, function(i, field) {
                    switch (field.name) {
                        case 'date':
                            date = field.value;
                            break;
                        case 'place':
                            place = field.value;
                            break;

                        case 'h_ini':
                            h_ini = field.value;
                            break;

                        case 'm_ini':
                            m_ini = field.value;
                            break;

                        case 'h_fin':
                            h_fin = field.value;
                            break;
                        case 'm_fin':
                            m_fin = field.value;
                            break;
                        case 'tema':
                            tema = field.value;
                            break;
                        case 'objetivos':
                            objetivos = field.value;
                            break;
                        case 'actividades':
                            actividades = field.value;
                            break;
                        case 'observaciones':
                            observaciones = field.value;
                            break;
                        case 'idtalentos':
                            idtalentos = field.value;
                            break;
                    }
                });
                if (!date) {
                    isvalid = false;
                    detalle += "* Selecciona una Fecha de seguimiento valida: date<br>";
                }


                if (place == undefined || place == "") {
                    detalle += "* Seleccione el lugar : lugar<br>";
                    isvalid = false;
                }

                if (actividades == undefined || actividades == "") {
                    detalle += "* Seleccione las actividades que se realizaron : actividades<br>";
                    isvalid = false;
                }

                if (observaciones == undefined || observaciones == "") {
                    detalle += "* Seleccione las observaciones : observaciones<br>";
                    isvalid = false;
                }

                if (h_ini > h_fin) {
                    isvalid = false;
                    detalle += "* La hora final debe ser mayor a la inicial<br>";
                } else if (h_ini == h_fin) {
                    if (m_ini > m_fin) {
                        isvalid = false;
                        detalle += "* La hora final debe ser mayor a la inicial<br>";
                    }
                }

                if (idtalentos.length === 0) {
                    isvalid = false;
                    detalle += "* Selecciona los estudiantes que asistieron al seguimiento: " + idtalentos.length + "<br>";
                }


                if (tema == "") {
                    isvalid = false;
                    detalle += "* La informacion de \"observaciones\" es obligatoria :" + tema + "<br>";
                }

                if (objetivos == "") {
                    isvalid = false;
                    detalle += "* La informacion de \"Objetivos\" es obligatoria:" + objetivos + "<br>";
                }

                var result = {
                    isvalid: isvalid,
                    detalle: detalle
                };


                return result;
            }

            /* CONTROLES PARA LA FICHA DEL ESTUDIANTE */
            $("#ficha_estudiante #editar_ficha").click(function() {
                $("#ficha_estudiante").find("input, textarea").prop("readonly", false);
                $("#profesional_ps").prop("readonly", true);
                $("#practicante_ps").prop("readonly", true);
                $("#monitor_ps").prop("readonly", true);
                $("#ficha_estudiante").find("select").prop("disabled", false);
                $(this).hide();
                $("#ficha_estudiante #cancel").fadeIn();
                $("#ficha_estudiante #save").fadeIn();
                $('#ficha_estudiante #codigo').attr('readonly', true);
                $('#ficha_estudiante #search').fadeOut();
            });

            $("#ficha_estudiante #cancel").click(function() {

                swal({
                        title: "Estas seguro/a de cancelar?",
                        text: "Los cambios realizados no serán tomados en cuenta y se perderán",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d51b23",
                        confirmButtonText: "Si!",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            $("#ficha_estudiante").find("input, textarea").prop("readonly", true);
                            $("#ficha_estudiante").find("select").prop("disabled", true);
                            $(this).hide();
                            $("#ficha_estudiante #save").fadeOut();
                            $('#ficha_estudiante #cancel').fadeOut();
                            $("#ficha_estudiante #editar_ficha").fadeIn();
                            $('#ficha_estudiante #codigo').attr('readonly', false);
                            $('#ficha_estudiante #search').fadeIn();
                            searchStudent();
                        }
                    });
            });

            $("#ficha_estudiante #go_back").on('click', function() {

                var page = 'index.php';
                var search = location.search.split('&');

                location.href = page + search[0] + '&' + search[1];
            });


            // funcion que gestiona los toogle, que agrupan la informacion por semestres
            $('#ficha_estudiante').on('click', '.accordion-toggle', function(event) {
                //alert('asdf');
                event.preventDefault();
                // create accordion variables
                var accordion = $(this);
                var accordionContent = accordion.next('.accordion-content');
                var accordionToggleIcon = $(this).children('.toggle-icon');

                // toggle accordion link open class
                accordion.toggleClass("open");


                // change plus/minus icon
                if (accordion.hasClass("open")) {
                    accordionToggleIcon.html("<i class='glyphicon glyphicon-chevron-down whitesmoke'></i>");
                } else {
                    accordionToggleIcon.html("<i class='glyphicon glyphicon-chevron-left'></i>");
                }

                // toggle accordion content
                accordionContent.slideToggle(250);

            });




            //para que los div toggle se contraigan con un clic
            $(document).on('click', 'panel-heading', function(e) {
                $(".panel-collapse.in").removeClass("in").addClass("collapse");
            });

            function searchStudent() {

                var data = $('#ficha_estudiante #codigo').serializeArray();
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/search_profile.php",
                    success: function(msg) {
                        //se captura  mensaje de error. Si existe. de lo contrario deria nulo
                        var error = msg.error;

                        //se limpia la pagina
                        $('#ficha_estudiante input,textarea').val('');
                        $('#ficha_estudiante #nombreficha').text('');
                        $('#ficha_estudiante #email').text('');
                        $('#ficha_estudiante #cohorte').text('');

                        //si no hay  error que proceda a actualizar los datos obtenidos
                        if (!error) {
                            var parameters = getUrlParams(document.location.search); //funcion definida en checkrole.js

                            canSeeStudent(msg.idtalentos, parameters, function(canSee) { //funcion definida en checkrole.js

                                //console.log(canSee);
                                if (canSee.result) {
                                    //se obtienen los atributos
                                    var search = location.search.split('&');
                                    var newpage = location.href.split('?')[0] + search[0] + "&" + search[1] + "&talento_id=" + msg.username;
                                    location.href = newpage;

                                } else {
                                    swal({
                                        title: "ÁREA RESTRINGIDA",
                                        html: true,
                                        type: "warning",
                                        text: "No tienes permisos para ver la información de este estudiante.<br> Dirigete a la oficina de Sistemas del plan talentos pilos para gestionar tu situación",
                                        confirmButtonColor: "#d51b23"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            //window.history.back();
                                            location.href = canSee.pagina;
                                        }
                                    });
                                }
                            });

                        } else {
                            swal({
                                title: "No encotrado",
                                html: true,
                                type: "error",
                                text: error,
                                confirmButtonColor: "#d51b23"
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    var search = location.search.split('&');
                                    window.history.pushState(null, null, "talentos_profile.php" + search[0] + "&" + search[1]);
                                }
                            });

                        }

                        //location.reload(true);

                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error " + msg);
                    },
                });
            }

            function openAccordionToggle(acordionTitle) {
                var accordion = $(acordionTitle);
                var accordionContent = accordion.next('.accordion-content');
                var accordionToggleIcon = accordion.children('.toggle-icon');
                accordionToggleIcon.html("<i class='glyphicon glyphicon-chevron-down whitesmoke'></i>");

                // toggle accordion link open class
                accordion.toggleClass("open");
                // toggle accordion content
                accordionContent.slideToggle(250);

                return true;

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

            function checkAsesStatus() {
                var status = $('#ficha_estudiante #estadoAses').val();
                $('#div_ases_status').empty();

                if (status == 'RETIRADO') {
                    var data = new Array();
                    var talentosid = $('#idtalentos').val();
                    data.push({
                        name: "talentosid",
                        value: talentosid
                    });
                    data.push({
                        name: "function",
                        value: "loadMotivoRetirostudent"
                    });
                    $.ajax({
                        type: "POST",
                        data: data,
                        url: "../managers/motivos_retiros.php",
                        success: function(msg) {
                            var error = msg.error;
                            if (!error) {
                                $('#div_ases_status').append('<h3>MOTIVO RETIRO ASES</h3><p><strong>' + msg.decripcion + ': </strong>' + msg.detalle + '</p>');

                            } else {

                                alert(msg.msg);
                            }
                        },
                        dataType: "json",
                        cache: "false",
                        error: function(msg) {
                            alert(msg);
                        },
                    });
                }
            }




        }
    };
});
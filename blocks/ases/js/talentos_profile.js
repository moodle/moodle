$(document).ready(function() {

    //-----incion carga de estados----
    manageIcetexStatus();
    manageAsesStatus();
    get_programa_facultad($('#iduser').val());
    loadPsicosocialInfo($('#idtalentos').val());
    //checkAsesStatus();
    //--- fin carga de estados ----

    //---se carga el mapa
    loadGoogleMapsEmbed();
    loadGeograficInfo();
    //---fin se carga el mapa


    // -----incio getion retiros ----
    $('#myModalRetiro #save_retiro').on('click', function() {
        saveMotivoRetiro();
    });
    //---- fin gestion retiros

    //se carga el json file de las sugerencias
    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
    writejson1(parameters); //metodo definido en checkrole.js

    //-----------

    var img = $('#photo').attr('src');
    var token = img.split('/');
    var newimg = token[0] + "/" + token[1] + "/" + token[2] + "/" + token[3] + "/" + token[4] + "/" + token[5] + "/f2.jpg";
    $('#photo').attr('src', newimg);
    setTimeout(
        function() {
            newimg = token[0] + "/" + token[1] + "/" + token[2] + "/" + token[3] + "/" + token[4] + "/" + token[5] + "/f1.jpg";
            $('#photo').attr('src', newimg);
        }, 1000);

    //activar pestañas
    var variables = getVariableGetByName();
    if (variables.ficha == "asistencia") {
        $("#1a").removeClass("active");
        $("#3a").removeClass("hide");
        $("#3a").addClass("active");
        $('#general_li').removeClass("active");
        $('#attendance_li').addClass("active");
    }

    //funcion sugerencia inteligente
    $('#codigo').sugerirInteligente({
        src: '../managers/search_suggest.php',
        minChars: 2,
        fillBox: true,
        fillBoxWith: 'codigo',
        idinstancia: getIdinstancia(),
    });

    //Se carga la info  del seguimiento socioeducativo
    $('#pes_socioeducativo').on('click', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        loadAll_trackPeer();
        loadAll_segGroup();
        loadAll_primerAcerca();
        loadAll_AcompaSocio();
        loadAll_SegSocio();
    });

    $('#socioedu_add_pares').on('click', function() {
        $('#save_seg').removeClass("hide");
        $('#div_created').addClass('hide');
        $('#upd_seg').addClass('hide');
        $('#myModalLabel').attr('name', 'PARES');

        initFormSeg();
    });

    $('#socioedu_primerAcerca').on('click', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        $('#myModalPrimerAcerca #save_seg').removeClass("hide");
        $('#myModalPrimerAcerca #div_created').addClass('hide');
        $('#myModalPrimerAcerca #upd_seg').addClass('hide');
        $('#myModalPrimerAcerca #infomonitor').addClass('hide');
        $('#myModalPrimerAcerca #myModalLabel').attr('name', 'PARES');
    });

    $('#socioedu_add_AcompaSocio').on('click', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        intiAcompaSocioForm();
        $('#myModalAcompaSocio #save_seg').removeClass("hide");
        $('#myModalAcompaSocio #div_created').addClass('hide');
        $('#myModalAcompaSocio #upd_seg').addClass('hide');
        $('#myModalAcompaSocio #infoMonitor').addClass('hide');
        $('#myModalAcompaSocio #myModalLabel').attr('name', 'PARES');
    });

    $('#socioedu_add_segsocio').on('click', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        $('#myModalSegSocio #save_seg').removeClass("hide");
        $('#myModalSegSocio #div_created').addClass('hide');
        $('#myModalSegSocio #infomonitor').addClass('hide');
        $('#myModalSegSocio #upd_seg').addClass('hide');
    });

    $('#AcompaSocio #mytableRiesgo').on('click', 'tbody tr', function() {

        if ($(this).find('td input').eq(0).is(':checked')) {
            $(this).find('td input').eq(1).prop("disabled", false);
        }

        if ($(this).find('td input').eq(0).prop("checked") == false) {
            $(this).find('td input').eq(1).prop("disabled", true);
        }
    });

    $('#AcompaSocio').on('click', '#plusingresos', function() {
        $('#AcompaSocio #mytableIngresos tbody').append('<tr> <td><input id="descripIngreso" name="descripIngreso" size="8" maxlength="15" type="text" /></td> <td><input id="valorIngreso" name="valorIngreso" type="text" size="8" maxlength="8"/></td> <td><a href="#" id="removeEco"><span class="glyphicon glyphicon-remove"></span></a></td> <td class="hide"><input id="idIngreso" name="idIngreso" size="1" value="0" maxlenght="1" type="text" /></td> </tr>');
    });
    $('#AcompaSocio').on('click', '#plusEgresos', function() {
        $('#AcompaSocio #mytableEgresos tbody').append('<tr> <td><input id="descripEgreso" name="descripEgreso" size="8" maxlength="15" type="text" /></td> <td><input id="valorEgreso" name="valorEgreso" type="text" size="8" maxlength="8"/></td> <td><a href="#" id="removeEco"><span class="glyphicon glyphicon-remove"></span></a></td> <td class="hide"><input id="idEgreso" name="idEgreso" size="1" value="0" maxlenght="1" type="text" /></td> </tr>');
    });

    $('#AcompaSocio').on('click', '#plusFamilia', function() {
        $('#AcompaSocio #mytablefamilia tbody').append(' <tr> <td><input id="nombreFamilia" name="nombreFamilia" size="8" maxlength="8" type="text" /></td> <td><select id="parentescoFamilia"  name="parentescoFamilia"> <option value="MADRE" selected>MADRE</option>  <option value="PADRE">PADRE</option> <option value="HERMANO/A">HERMANO/A</option> <option value="TIO/A">TIO/A</option> <option value="ABUELO/A">ABUELO/A</option> <option value="PRIMO">PRIMO</option> <option value="OTRO">OTRO</option> </select> <td><input id="ocupacionFamilia" name="ocupacionFamilia" size="8" maxlenght="8" type="text" /></td> <td><input id="telefonoFamilia" name="telefonoFamilia" size="8" maxlenght="8" type="text" /></td> <td><a href="#" id="removeFamilia"><span class="glyphicon glyphicon-remove"></span></a></td>  <td class="hide"><input id="idFamilia" name="idFamilia" size="1" value="0" maxlenght="1" type="text" /></td>  </tr>');
    });

    $('#AcompaSocio').on('click', '#removeEco', function() {
        var idEco = $(this).closest('tr').find('td input[id="idIngreso"], td input[id="idEgreso"] ').val();
        var tipo = $(this).attr('id');
        var ts = $(this).closest('tr');

        swal({
                title: "¿Está seguro/a que desea eliminar esta información económica?",
                text: "La información se perderá y no se podrá recuperar",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d51b23",
                confirmButtonText: "Si!",
                cancelButtonText: "No",
                closeOnConfirm: true,
            },
            function(isConfirm) {
                if (isConfirm) {
                    if (idEco != 0) {
                        dropEcono(idEco);
                    }
                    ts.remove();
                    caculateSum('#AcompaSocio #valorIngreso', '#AcompaSocio #totalIngresos');
                    caculateSum('#AcompaSocio #valorEgreso', '#AcompaSocio #totalEgresos');

                }
            });
        return false;
    });

    //Función que carga los riesgos asociados al estudiante
    loadRisk($('#idtalentos').val());

    $('#AcompaSocio').on('click', '#removeFamilia', function() {
        var idFamilia = $(this).closest('tr').find('td input[id="idFamilia"]').val();
        var ts = $(this).closest('tr');

        swal({
                title: "Estas seguro/a que desea eliminar esta informacion Familiar?",
                text: "La información se perderá y no se podrá recurar",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d51b23",
                confirmButtonText: "Si!",
                cancelButtonText: "No",
                closeOnConfirm: true,
            },
            function(isConfirm) {
                if (isConfirm) {
                    if (idFamilia != 0) {
                        dropFamilia(idFamilia);
                    }
                    ts.remove();
                }
            });
        return false;
    });

    $('#AcompaSocio').on('keydown keyup', '#valorIngreso', function() {
        caculateSum('#AcompaSocio #valorIngreso', '#AcompaSocio #totalIngresos');
    });

    $('#AcompaSocio').on('keydown keyup', '#valorEgreso', function() {
        caculateSum('#AcompaSocio #valorEgreso', '#AcompaSocio #totalEgresos');
    });

    $('#AcompaSocio').on('keydown keyup', '#telefonoFamilia', function() {
        if (!isNaN(this.value)) {
            $(this).css("background-color", "#FEFFB0");
        }
        else if (this.value.length != 0) {
            $(this).css("background-color", "red");
        }
    });

    $('#myModalSegSocio').on('click', '#save_seg', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        var data = $('#myModalSegSocio #SegSocioForm').serializeArray();
        var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

        data.push({
            name: "function",
            value: "saveSegSocio"
        });

        var idtalentos = $('#ficha_estudiante #idtalentos').val();
        data.push({
            name: "idtalentos",
            value: idtalentos
        });
        data.push({
            name: "idinstancia",
            value: parameters.instanceid
        });

        //console.log(data);
        $.ajax({
            type: "POST",
            data: data,
            url: "../managers/socioeducativo.php",
            success: function(msg) {
                var error = msg.error;
                if (!error) {
                    swal({
                        title: "Actualizado con exito!!",
                        html: true,
                        type: "success",
                        text: msg.msg,
                        confirmButtonColor: "#d51b23"
                    }, function(isConfirm) {
                        if (isConfirm) {}
                    });
                    $('#myModalSegSocio').modal('toggle');
                    $('#myModalSegSocio').modal('toggle');
                    $('#myModalSegSocio #save_seg').addClass('hide');
                    $('.modal-backdrop').remove();
                    loadAll_SegSocio();
                    loadAll_trackPeer();
                }
                else {
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
                console.log(msg)
            },
        });
    });

    $('#myModalAcompaSocio').on('click', '#save_seg', function() {
        var validation = validateAcompasocio();
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        if (validation.isvalid) {
            var data = $('#myModalAcompaSocio #AcompaSocio').serializeArray();
            var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

            data = addInfoDataAcompaSocio(data);
            data.push({
                name: "function",
                value: "newAcompaSocio"
            });
            data.push({
                name: "idinstancia",
                value: parameters.instanceid
            });
            //console.log(data);
            $.ajax({
                type: "POST",
                data: data,
                url: "../managers/socioeducativo.php",
                success: function(msg) {
                    var error = msg.error;
                    if (!error) {
                        swal({
                            title: "Actualizado con exito!!",
                            html: true,
                            type: "success",
                            text: msg.msg,
                            confirmButtonColor: "#d51b23"
                        }, function(isConfirm) {
                            if (isConfirm) {}
                        });
                        $('#myModalAcompaSocio').modal('toggle');
                        $('#myModalAcompaSocio').modal('toggle');
                        $('#myModalAcompaSocio #save_seg').addClass('hide');
                        $('.modal-backdrop').remove();
                        loadAll_AcompaSocio();
                    }
                    else {
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
                    console.log(msg)
                },
            });
        }
        else {
            swal({
                title: "Error",
                html: true,
                type: "warning",
                text: "Detalles del error:<br>" + validation.detalle,
                confirmButtonColor: "#D3D3D3"
            });
        }
    });

    $('#myModalAcompaSocio').on('click', '#upd_seg', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        var id_seg = $(this).parent().attr('id');
        updateAcompaSocio(id_seg);
    });

    $('#myModalPrimerAcerca').on('click', '#upd_seg', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        var id_seg = $(this).parent().attr('id');
        updatePrimerAcerca(id_seg);
    });

    $('#myModalSegSocio').on('click', '#upd_seg', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        var id_seg = $(this).parent().attr('id');
        updateSegsocio(id_seg);
    });

    // 	$('#myModalPares').on('click','#save_seg', function() {
    // 	    var data = $('#myModalPares #seguimiento').serializeArray();
    // 	    console.log(data);
    // 	});

    $('#myModalPares #save_seg').on('click', function() {
        var idtalentos = $('#ficha_estudiante #idtalentos').val();
        var tipo = 'PARES';
        var id = new Array();
        var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

        id.push(idtalentos);

        send_email();

        var data = $('#myModalPares #seguimiento').serializeArray();
        //console.log(data);
        data.push({
            name: "function",
            value: "new"
        });
        data.push({
            name: "idtalentos",
            value: id
        });
        data.push({
            name: "tipo",
            value: tipo
        });
        data.push({
            name: "idinstancia",
            value: parameters.instanceid
        });
        var validation = validateSegPares(data);
        //$('#seguimiento input[name=optradio]').parent().attr('id')
        if (validation.isvalid) {

            $.ajax({
                type: "POST",
                data: data,
                url: "../managers/seguimiento.php",
                success: function(msg) {
                    var error = msg.error;
                    if (!error) {
                        swal({
                            title: "Actualizado con exito!!",
                            html: true,
                            type: "success",
                            text: msg.msg,
                            confirmButtonColor: "#d51b23"
                        }, function(isConfirm) {
                            if (isConfirm) {}
                        });
                        $('#myModalPares').modal('toggle');
                        $('#myModalPares').modal('toggle');
                        $('#save_seg').addClass('hide');
                        $('.modal-backdrop').remove();
                        loadAll_trackPeer();
                    }
                    else {
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
                    console.log(msg)
                },
            });
        }
        else {
            swal({
                title: "Error",
                html: true,
                type: "warning",
                text: "Detalles del error:<br>" + validation.detalle,
                confirmButtonColor: "#D3D3D3"
            });
        }
    });

    $('#myModalPrimerAcerca #save_seg').on('click', function() {
        var data = $('#myModalPrimerAcerca #PrimerAcercaForm').serializeArray();
        var idtalentos = $('#ficha_estudiante #idtalentos').val();
        var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

        data.push({
            name: "function",
            value: "saveprimerAcerca"
        });
        data.push({
            name: "idtalentos",
            value: idtalentos
        });
        data.push({
            name: "idinstancia",
            value: parameters.instanceid
        });

        $.ajax({
            type: "POST",
            data: data,
            url: "../managers/socioeducativo.php",
            success: function(msg) {
                var error = msg.error;
                if (!error) {
                    swal({
                        title: "Actualizado con exito!",
                        html: true,
                        type: "success",
                        text: msg.msg,
                        confirmButtonColor: "#d51b23"
                    }, function(isConfirm) {
                        if (isConfirm) {}
                    });
                    $('#myModalPrimerAcerca').modal('toggle');
                    $('#myModalPrimerAcerca').modal('toggle');
                    $('.modal-backdrop').remove();
                    loadAll_primerAcerca();
                }
                else {
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
                console.log(msg)
            },
        });

    });

    $('#upd_seg').on('click', function() {
        var id_seg = $(this).parent().attr('id');
        update_seg(id_seg);
    });

    $('#seg_socio_title').on('click', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        loadAll_SegSocio();
    });

    $('#seg_grupal_title').on('click', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        loadAll_segGroup();
    });

    $('#seg_pares_title').on('click', function() {
        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        loadAll_trackPeer();
        print_r(loadAll_SegSocio());
    });
});

$(document).on('click', '#AcompaSocio .antecedentes', function() {
    if ($(this).is(':checked') && $(this).val() == 1) {
        $('#AcompaSocio #motivo').prop("disabled", false);
    }
    var ant = 0;
    $('#AcompaSocio .antecedentes').each(function() {
        if ($(this).is(':checked') && $(this).val() == 0) {
            ant += 1;
        }
    });

    if (ant == 3) {
        $('#AcompaSocio #motivo').prop("disabled", true);
    }
});

//funciones requeridas

function loadPsicosocialInfo(idStudent) {

    $.ajax({
        type: "POST",
        data: {
            idStudent: idStudent,
            fun: 'get_professional'
        },
        url: "../managers/get_info_psicosocial.php",
        success: function(msg) {
            console.log(msg);
            $('#profesional_ps').val(msg);
        },
        dataType: "text",
        cache: "false",
        error: function(msg) {
            console.log(msg);
        }
    });

    $.ajax({
        type: "POST",
        data: {
            idStudent: idStudent,
            fun: 'get_practicante'
        },
        url: "../managers/get_info_psicosocial.php",
        success: function(msg) {
            console.log(msg);
            $('#practicante_ps').val(msg);
        },
        dataType: "text",
        cache: "false",
        error: function(msg) {
            console.log(msg);
        }
    });

    $.ajax({
        type: "POST",
        data: {
            idStudent: idStudent,
            fun: 'get_monitor'
        },
        url: "../managers/get_info_psicosocial.php",
        success: function(msg) {
            console.log(msg);
            $('#monitor_ps').val(msg);
        },
        dataType: "text",
        cache: "false",
        error: function(msg) {
            console.log(msg);
        }
    });
}

function caculateSum(itemvalues, itemShow) {
    var ingresoParcial = 0;

    $(itemvalues).each(function() {

        //add only if the value is number
        if (!isNaN(this.value) && this.value.length != 0) {
            ingresoParcial += parseFloat(this.value);
            $(this).css("background-color", "#FEFFB0");
        }
        else if (this.value.length != 0) {
            $(this).css("background-color", "red");
        }
    });

    $(itemShow).text(ingresoParcial);
}

function loadAll_trackPeer() {

    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

    $('#list_pares').empty();
    var data = new Array();
    var idtalentos = $('#ficha_estudiante #idtalentos').val();

    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    data.push({
        name: "function",
        value: "load"
    });
    data.push({
        name: "idinstancia",
        value: parameters.instanceid
    })
    data.push({
        name: "tipo",
        value: "PARES"
    });

    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/seguimiento.php",
        success: function(msg) {
            console.log(msg);
            var error = msg.error;
            if (!error) {
                var isfirst = true;
                var array_semestres_segumientos = msg.semesters_segumientos;

                if (array_semestres_segumientos.length > 0) {
                    for (y in array_semestres_segumientos) {
                        var panel = '<div class="accordion-container"><a id="title' + array_semestres_segumientos[y].id_semester + '" class="accordion-toggle">Semestre ' + array_semestres_segumientos[y].name_semester + '<span class="toggle-icon"><i class="glyphicon glyphicon-chevron-left"></i></span></a>';
                        panel += '<div id="panel-body' + array_semestres_segumientos[y].id_semester + '" class="accordion-content ScrollStyle"></div></div>';
                        var result = array_semestres_segumientos[y].result;
                        var rows = array_semestres_segumientos[y].rows;

                        $('#list_pares').append(panel);

                        if (rows > 0) {
                            for (x in result) {
                                $("#list_pares #panel-body" + array_semestres_segumientos[y].id_semester).append("<div class=\"container well col-md-12\"> <div class=\"container-fluid col-md-10\" name=\"info\"><div class=\"row\"><label class=\"col-md-3\" for=\"fecha_des\">Fecha</label><label class=\"col-md-9\" for=\"tema_des\">Tema</label> </div> <div class=\"row\"> <input type=\"text\" class=\"col-md-3\" value=\"" + result[x].fecha + "\" id=\"fecha_seg\" name=\"fecha_seg\" disabled> <input type=\"text\" class=\"col-md-9\" value=\"" + result[x].tema + "\" id=\"tema_seg\" name=\"tema_seg\" disabled> </div></div> <div id=\"" + result[x].id_seg + "\" class=\"col-md-2\" name=\"div_button_seg\"> <button type=\"submit\" id=\"consult_pares\" name=\"consult_pares\" class=\"submit\" data-toggle=\"modal\" data-target=\"#myModalPares\">Detalle</button> </div></div>");
                            }
                            $('#list_pares').on('click', '#consult_pares', function() {
                                checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
                                var id_seg = $(this).parent().attr('id');

                                loadJustOneSeg(id_seg, 'PARES');
                            });
                        }
                        else {
                            $("#list_pares #panel-body" + array_semestres_segumientos[y].id_semester).append("<label>No registra</label><br>");
                        }

                        if (isfirst) {
                            openAccordionToggle('#list_pares #title' + array_semestres_segumientos[y].id_semester);

                            //mostrar promedio
                            // 	var promedio = getPromedioString(parseInt(array_semestres_segumientos[y].promedio));
                            // 	$('#promedio').text(promedio);
                            //     $('#pes_socioeducativo').attr('title','Calificacion global de Riesgo = '+promedio);
                            isfirst = false;
                        }
                    }
                }
                else {
                    $('#list_pares').append('No hay registros de alguna matricula del estudiante');
                }
            }
            else {
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
            console.log(msg);
        },
    });
}

function getPromedioString(val) {
    if (val == 0) {
        return "Sin Contacto";
    }
    else if (val > 0 && val < 2) {
        return "Bajo";
    }
    else if (val >= 2 && val < 3) {
        return "Medio Bajo";
    }
    else if (val >= 3 && val < 4) {
        return "Medio";
    }
    else if (val >= 4 && val < 5) {
        return "Medio Alto";
    }
    else if (val == 5) {
        return "Alto";
    }
}

function loadAll_segGroup() {
    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
    $('#list_grupal').empty();
    var data = new Array();
    var idtalentos = $('#ficha_estudiante #idtalentos').val();

    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    data.push({
        name: "function",
        value: "load"
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
        url: "../managers/seguimiento.php",
        success: function(msg) {
            var error = msg.error;
            if (!error) {
                var isfirst = true;
                var array_semestres_segumientos = msg.semesters_segumientos;

                for (y in array_semestres_segumientos) {

                    var panel = '<div class="accordion-container"><a id="title' + array_semestres_segumientos[y].id_semester + '" class="accordion-toggle">Semestre ' + array_semestres_segumientos[y].name_semester + '<span class="toggle-icon"><i class="glyphicon glyphicon-chevron-left"></i></span></a>';
                    panel += '<div id="panel-body' + array_semestres_segumientos[y].id_semester + '" class="accordion-content ScrollStyle"></div></div>';

                    var result = array_semestres_segumientos[y].result;
                    var rows = array_semestres_segumientos[y].rows;

                    $('#list_grupal').append(panel);

                    if (rows > 0) {
                        for (x in result) {
                            $("#list_grupal #panel-body" + array_semestres_segumientos[y].id_semester).append("<div class=\"container well col-md-12\"> <div class=\"container-fluid col-md-10\" name=\"info\"><div class=\"row\"><label class=\"col-md-3\" for=\"fecha_des\">Fecha</label><label class=\"col-md-9\" for=\"tema_des\">Tema</label> </div> <div class=\"row\"> <input type=\"text\" class=\"col-md-3\" value=\"" + result[x].fecha + "\" id=\"fecha_seg\" name=\"fecha_seg\" disabled> <input type=\"text\" class=\"col-md-9\" value=\"" + result[x].tema + "\" id=\"tema_seg\" name=\"tema_seg\" disabled> </div></div> <div id=\"" + result[x].id_seg + "\" class=\"col-md-2\" name=\"div_button_seg\"> <button type=\"submit\" id=\"consult_grupal\" name=\"consult_grupal\" class=\"submit\" data-toggle=\"modal\" data-target=\"#myModal\">Detalle</button> </div></div>");
                        }
                        $('#list_grupal').on('click', '#consult_grupal', function() {
                            checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
                            var id_seg = $(this).parent().attr('id');
                            loadJustOneSeg(id_seg, 'GRUPAL');
                        });
                    }
                    else {
                        $("#list_grupal #panel-body" + array_semestres_segumientos[y].id_semester).append("<label>No registra</label><br>");
                    }

                    if (isfirst) {
                        openAccordionToggle('#list_grupal #title' + array_semestres_segumientos[y].id_semester);
                        isfirst = false;
                    }
                }
            }
            else {
                swal({
                    title: error,
                    html: true,
                    type: "error",
                    text: msg.msg,
                    confirmButtonColor: "#D3D3D3"
                }, function(isConfirm) {
                    if (isConfirm) {}
                });
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg);
        },
    });
}

function update_seg(id_seg) {
    var data = $('#myModalPares #seguimiento').serializeArray();
    var idtalentos = $('#ficha_estudiante #idtalentos').val();
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
        value: "PARES"
    });
    data.push({
        name: "idtalentos",
        value: idtalentos
    });

    var validation = validateSegPares(data);
    //$('#seguimiento input[name=optradio]').parent().attr('id')
    if (validation.isvalid) {

        $.ajax({
            type: "POST",
            data: data,
            url: "../managers/seguimiento.php",
            success: function(msg) {
                var error = msg.error;
                if (!error) {
                    swal({
                        title: "Actualizado con exito!!",
                        html: true,
                        type: "success",
                        text: msg.msg,
                        confirmButtonColor: "#d51b23"
                    }, function(isConfirm) {
                        if (isConfirm) {}
                    });
                    $('#myModalPares').modal('toggle');
                    $('#myModalPares').modal('toggle');
                    $('#upd_seg').addClass('hide');
                    $('.modal-backdrop').remove();
                    loadAll_trackPeer();
                }
                else {
                    swal({
                        title: error,
                        html: true,
                        type: "error",
                        text: msg.msg,
                        confirmButtonColor: "#D3D3D3"
                    }, function(isConfirm) {
                        if (isConfirm) {}
                    });
                }

                send_email();
            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                console.log(msg)
            },
        });
    }
    else {
        swal({
            title: "Error",
            html: true,
            type: "warning",
            text: "Detalles del error:<br>" + validation.detalle,
            confirmButtonColor: "#D3D3D3"
        });
    }
}

function loadJustOneSeg(id_seg, tipo) {

    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
    var data = new Array();
    var idtalentos = $('#ficha_estudiante #idtalentos').val();
    data.push({
        name: "id_seg",
        value: id_seg
    });
    data.push({
        name: "function",
        value: "loadJustOne"
    });
    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    data.push({
        name: "tipo",
        value: tipo
    });
    data.push({
        name: "idinstancia",
        value: parameters.instanceid
    });
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/seguimiento.php",
        success: function(msg) {
            initFormSeg();

            var error = msg.error;
            if (!error) {

                var result = msg.result;
                var rows = msg.rows;
                if (rows > 0) {
                    for (x in result) {

                        var idSelector = '';
                        if (tipo == 'GRUPAL') {
                            idSelector = '#myModal ';

                            $(idSelector + '#seguimiento #actividades').val(result[x].actividades);


                        }
                        else if (tipo == 'PARES') {
                            idSelector == '#myModalPares ';

                            $(idSelector + '#seguimiento #individual').val(result[x].individual);
                            $(idSelector + '#seguimiento input[name="riesgo_ind"]').each(function() {
                                if ($(this).val() == result[x].individual_riesgo) $(this).prop("checked", true);
                            });

                            $(idSelector + '#seguimiento #familiar').val(result[x].familiar_desc);
                            $(idSelector + '#seguimiento input[name="riesgo_familiar"]').each(function() {
                                if ($(this).val() == result[x].familiar_riesgo) $(this).prop("checked", true);
                            });

                            $(idSelector + '#seguimiento #academico').val(result[x].academico);
                            $(idSelector + '#seguimiento input[name="riesgo_aca"]').each(function() {
                                if ($(this).val() == result[x].academico_riesgo) $(this).prop("checked", true);
                            });

                            $(idSelector + '#seguimiento #economico').val(result[x].economico);
                            $(idSelector + '#seguimiento input[name="riesgo_econom"]').each(function() {
                                if ($(this).val() == result[x].economico_riesgo) $(this).prop("checked", true);
                            });

                            $(idSelector + '#seguimiento #vida_uni').val(result[x].vida_uni);
                            $(idSelector + '#seguimiento input[name="riesgo_uni"]').each(function() {
                                if ($(this).val() == result[x].vida_uni_riesgo) $(this).prop("checked", true);
                            });

                        }

                        $(idSelector + '#seguimiento #date').val(result[x].fecha);
                        $(idSelector + '#seguimiento #place').val(result[x].lugar);
                        $(idSelector + '#seguimiento #h_ini').val(result[x].h_ini);
                        $(idSelector + '#seguimiento #m_ini').val(result[x].m_ini);
                        $(idSelector + '#seguimiento #h_fin').val(result[x].h_fin);
                        $(idSelector + '#seguimiento #m_fin').val(result[x].m_fin);
                        $(idSelector + '#seguimiento #tema').val(result[x].tema);
                        $(idSelector + '#seguimiento #objetivos').val(result[x].objetivos);
                        $(idSelector + '#seguimiento #observaciones').val(result[x].observaciones);
                        $(idSelector + '#seguimiento #monitor').text(result[x].infoMonitor);
                        $(idSelector + '#seguimiento #infomonitor').removeClass('hide');


                        //se muetra el boton actualizar i se asigina un id al contenedor para identificar el seguimient

                        $('#upd_seg').removeClass('hide');
                        $('#upd_seg').parent().attr('id', id_seg);
                        $('#save_seg').addClass('hide');

                        if (result[x].editable == false || tipo == 'GRUPAL') {
                            $('#upd_seg').attr('disabled', true);
                            $('#upd_seg').attr('title', 'Han trasncurrido más de 24 horas desde su creación por lo tanto no se puede actualizar');
                            $(idSelector + '#seguimiento').find('select, textarea, input').attr('disabled', true);

                            if (tipo == 'GRUPAL') {
                                $('#upd_seg').addClass('hide');
                            }
                        }
                        else {
                            $('#upd_seg').attr('disabled', false);
                            $('#upd_seg').attr('title', '');
                            $(idSelector + '#seguimiento').find('select, textarea, input').attr('disabled', false);
                        }

                        //se muestra los datos de creacion
                        $('#created_date').text("Creado el " + result[x].createdate);
                        $('#div_created').removeClass('hide');
                    }
                }
                else {
                    swal("No se encontraron resultados", "warning");
                }
            }
            else {
                swal({
                    title: error,
                    html: true,
                    type: "error",
                    text: msg.msg,
                    confirmButtonColor: "#D3D3D3"
                }, function(isConfirm) {
                    if (isConfirm) {}
                });
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    });
}

function loadAll_primerAcerca() {
    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
    $('#list_primerA').empty();
    var data = new Array();
    var idtalentos = $('#ficha_estudiante #idtalentos').val();

    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    data.push({
        name: "function",
        value: "load"
    });
    data.push({
        name: "idinstancia",
        value: parameters.instanceid
    });
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/socioeducativo.php",
        success: function(msg) {
            var error = msg.error;
            if (!error) {

                var result = msg.result;
                var rows = msg.rows;
                if (rows > 0) {

                    $('#socioedu_primerAcerca').prop("disabled", true);
                    $('#socioedu_primerAcerca').addClass('hide');
                    $('#socioedu_add_AcompaSocio').removeClass('hide');

                    for (x in result) {

                        $('#list_primerA').append("<div class=\"container well col-md-12\"> <div class=\"container-fluid col-md-10\" name=\"info\"><div class=\"row\"><label class=\"col-md-3\" for=\"fecha_des\">Fecha</label><label class=\"col-md-9\" for=\"tema_des\">Tema</label> </div> <div class=\"row\"> <input type=\"text\" class=\"col-md-3\" value=\"" + result[x].fecha + "\" id=\"fecha_seg\" name=\"fecha_seg\" disabled> <input type=\"text\" class=\"col-md-9\" value=\"" + result[x].motivo + "\" id=\"tema_seg\" name=\"tema_seg\" disabled> </div></div> <div id=\"" + result[x].id + "\" class=\"col-md-2\" name=\"div_button_seg\"> <button type=\"submit\" id=\"consult_primerA\" name=\"consult_primerA\" class=\"submit\" data-toggle=\"modal\" data-target=\"#myModalPrimerAcerca\">Detalle</button> </div></div>");
                    }
                    $('#list_primerA').on('click', '#consult_primerA', function() {
                        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
                        var id = $(this).parent().attr('id');
                        $('#update_primerAcerca').removeClass('hide');
                        loadJustOnePrimerAcerca(id);
                    });

                }
                else {
                    $('#list_primerA').append("<label>No registra</label><br>");
                    $('#socioedu_primerAcerca').prop("disabled", false);
                    $('#socioedu_primerAcerca').removeClass('hide');
                    $('#socioedu_add_AcompaSocio').addClass('hide');
                }
            }
            else {
                swal({
                    title: error,
                    html: true,
                    type: "error",
                    text: msg.msg,
                    confirmButtonColor: "#D3D3D3"
                }, function(isConfirm) {
                    if (isConfirm) {}
                });
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            alert("Error");
        },
    });
}

function loadJustOnePrimerAcerca(id_pacerca) {
    var data = new Array();
    var idtalentos = $('#ficha_estudiante #idtalentos').val();
    data.push({
        name: "function",
        value: "load"
    });
    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    intiAcompaSocioForm();
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/socioeducativo.php",
        success: function(msg) {

            var error = msg.error;
            if (!error) {
                intiAcompaSocioForm();
                var result = msg.result;
                var rows = msg.rows;

                //console.log(msg.result);
                if (rows > 0) {
                    for (x in result) {
                        //se muestra los datos de creacion
                        $('#myModalPrimerAcerca #created_date').text("Creado el " + result[x].created);
                        $('#myModalPrimerAcerca #div_created').removeClass('hide');
                        $('#myModalPrimerAcerca #comp_familiar').val(result[x].comp_familiar);
                        $('#myModalPrimerAcerca #freetime').val(result[x].observaciones);
                        $('#myModalPrimerAcerca #motivo').val(result[x].motivo);
                        $('#myModalPrimerAcerca #' + result[x].act_status + '').children().prop('checked', true);
                        $('#myModalPrimerAcerca #monitor').text(result[x].infoProfesional);
                        $('#myModalPrimerAcerca #infomonitor').removeClass('hide');

                        $('#myModalPrimerAcerca #upd_seg').removeClass('hide');
                        $('#myModalPrimerAcerca #upd_seg').parent().attr('id', result[x].id);
                        $('#myModalPrimerAcerca #save_seg').addClass('hide');
                    }
                }
                else {
                    swal("No se ecnotraron resultados", "warning");
                }
            }
            else {
                swal({
                    title: error,
                    html: true,
                    type: "error",
                    text: msg.msg,
                    confirmButtonColor: "#D3D3D3"
                }, function(isConfirm) {
                    if (isConfirm) {}
                });
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    });
}

function updatePrimerAcerca(idPA) {
    var data = $('#myModalPrimerAcerca #PrimerAcercaForm').serializeArray();
    data.push({
        name: "function",
        value: "updatePrimerAcerca"
    });
    data.push({
        name: "idPA",
        value: idPA
    });

    var idtalentos = $('#ficha_estudiante #idtalentos').val();
    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    //console.log(data);
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/socioeducativo.php",
        success: function(msg) {
            var error = msg.error;
            if (!error) {
                swal({
                    title: "Actualizado con exito!!",
                    html: true,
                    type: "success",
                    text: msg.msg,
                    confirmButtonColor: "#d51b23"
                }, function(isConfirm) {
                    if (isConfirm) {}
                });
                $('#myModalPrimerAcerca').modal('toggle');
                $('#myModalPrimerAcerca').modal('toggle');
                $('#myModalPrimerAcerca #save_seg').addClass('hide');
                $('.modal-backdrop').remove();
                loadAll_AcompaSocio();
            }
            else {
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
            console.log(msg)
        },
    });
}

function loadAll_SegSocio() {
    $('#list_SegSocio').empty();

    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

    var data = new Array();
    var idtalentos = $('#ficha_estudiante #idtalentos').val();

    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    data.push({
        name: "function",
        value: "loadSegSocio"
    });
    data.push({
        name: "idinstancia",
        value: parameters.instanceid
    });

    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/socioeducativo.php",
        success: function(msg) { //console.log(msg);
            var error = msg.error;
            if (!error) {
                var isfirst = true;

                var array_semestres_segumientos = msg.semesters_segumientos;
                //console.log(msg.semesters_segumientos);
                for (y in array_semestres_segumientos) {

                    var panel = '<div class="accordion-container"><a id="title' + array_semestres_segumientos[y].id_semester + '" class="accordion-toggle">Semestre ' + array_semestres_segumientos[y].name_semester + '<span class="toggle-icon"><i class="glyphicon glyphicon-chevron-left"></i></span></a>';
                    panel += '<div id="panel-body' + array_semestres_segumientos[y].id_semester + '" class="accordion-content ScrollStyle"></div></div>';

                    var result = array_semestres_segumientos[y].result;
                    var rows = array_semestres_segumientos[y].rows;

                    $('#list_SegSocio').append(panel);

                    if (rows > 0) {
                        for (x in result) {
                            $("#list_SegSocio #panel-body" + array_semestres_segumientos[y].id_semester).append("<div class=\"container well col-md-12\"> <div class=\"container-fluid col-md-10\" name=\"info\"><div class=\"row\"><label class=\"col-md-3\" for=\"fecha_des\">Fecha</label><label class=\"col-md-9\" for=\"tema_des\">Tema</label> </div> <div class=\"row\"> <input type=\"text\" class=\"col-md-3\" value=\"" + result[x].fecha + "\" id=\"fecha_seg\" name=\"fecha_seg\" disabled> <input type=\"text\" class=\"col-md-9\" value=\"" + result[x].tema + "\" id=\"tema_seg\" name=\"tema_seg\" disabled> </div></div> <div id=\"" + result[x].id + "\" class=\"col-md-2\" name=\"div_button_seg\"> <button type=\"submit\" id=\"consult_SegSocio\" name=\"consult_SegSocio\" class=\"submit\" data-toggle=\"modal\" data-target=\"#myModal\">Detalle</button> </div></div>");
                        }
                        $('#list_SegSocio').on('click', '#consult_SegSocio', function() {
                            checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
                            var id_seg = $(this).parent().attr('id');
                            loadJustOneSegSocio(id_seg);
                        });

                    }
                    else {
                        $("#list_SegSocio #panel-body" + array_semestres_segumientos[y].id_semester).append("<label>No registra</label><br>");
                    }

                    if (isfirst) {
                        openAccordionToggle('#list_SegSocio #title' + array_semestres_segumientos[y].id_semester); //metodo definido en main
                        isfirst = false;
                    }
                }
            }
            else {
                swal({
                    title: error,
                    html: true,
                    type: "error",
                    text: msg.msg,
                    confirmButtonColor: "#D3D3D3"
                }, function(isConfirm) {
                    if (isConfirm) {}
                });
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg);
        },
    });
}

function loadJustOneSegSocio(idseg) {

    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
    var data = new Array();
    var idtalentos = $('#ficha_estudiante #idtalentos').val();
    data.push({
        name: "function",
        value: "loadJustOneSegSocio"
    });
    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    data.push({
        name: "idSegSocio",
        value: idseg
    });
    data.push({
        name: "idinstancia",
        value: parameters.instanceid
    });
    //console.log(data);
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/socioeducativo.php",
        success: function(msg) {

            var error = msg.error;
            if (!error) {
                intiAcompaSocioForm();
                var result = msg.result;
                var rows = msg.rows;

                console.log(msg.result);
                if (rows > 0) {
                    for (x in result) {
                        //se muestra los datos de creacion
                        $('#myModalSegSocio #created_date').text("Creado el " + result[x].created);
                        $('#myModalSegSocio #div_created').removeClass('hide');

                        $('#myModalSegSocio #date').val(result[x].fecha);
                        $('#myModalSegSocio #seg').val(result[x].seguimiento);
                        $('#myModalSegSocio #motivo').val(result[x].motivo);
                        $('#myModalSegSocio #' + result[x].act_status + '').children().prop('checked', true);

                        $('#myModalSegSocio #upd_seg').removeClass('hide');
                        $('#myModalSegSocio #upd_seg').parent().attr('id', result[x].id);
                        $('#myModalSegSocio #save_seg').addClass('hide');
                        $('#myModalSegSocio #monitor').text(result[x].infoProfesional);
                        $('#myModalSegSocio #infomonitor').removeClass('hide');
                    }

                }
                else {
                    swal("No se ecnotraron resultados", "warning");
                }
            }
            else {
                swal({
                    title: error,
                    html: true,
                    type: "error",
                    text: msg.msg,
                    confirmButtonColor: "#D3D3D3"
                }, function(isConfirm) {
                    if (isConfirm) {}
                });
            }


        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    });
}

function updateSegsocio(idseg) {
    var data = $('#myModalSegSocio #SegSocioForm').serializeArray();
    data.push({
        name: "function",
        value: "updateSegSocio"
    });

    var idtalentos = $('#ficha_estudiante #idtalentos').val();
    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    data.push({
        name: "idSegSocio",
        value: idseg
    });
    console.log(data);
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/socioeducativo.php",
        success: function(msg) {
            var error = msg.error;
            if (!error) {
                swal({
                    title: "Actualizado con exito!!",
                    html: true,
                    type: "success",
                    text: msg.msg,
                    confirmButtonColor: "#d51b23"
                }, function(isConfirm) {
                    if (isConfirm) {}
                });
                $('#myModalSegSocio').modal('toggle');
                $('#myModalSegSocio').modal('toggle');
                $('#myModalSegSocio #save_seg').addClass('hide');
                $('.modal-backdrop').remove();
                loadAll_SegSocio();
                loadAll_trackPeer();
            }
            else {
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
            console.log(msg)
        },
    });
}

function loadAll_AcompaSocio() {

    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

    $('#list_AcompaSocio').empty();
    var data = new Array();
    var idtalentos = $('#ficha_estudiante #idtalentos').val();

    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    data.push({
        name: "function",
        value: "load_AcompaSocio"
    });
    data.push({
        name: "idinstancia",
        value: parameters.instanceid
    });

    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/socioeducativo.php",
        success: function(msg) {
            var error = msg.error;
            if (!error) {
                var result = msg.result;
                var rows = msg.rows;
                if (rows > 0) {
                    $('#socioedu_add_segsocio').removeClass('hide');
                    $('#socioedu_add_AcompaSocio').addClass('hide');
                    $('#socioedu_add_AcompaSocio').prop("disabled", true);

                    for (x in result) {

                        $('#list_AcompaSocio').append("<div class=\"container well col-md-12\"> <div class=\"container-fluid col-md-10\" name=\"info\"><div class=\"row\"><label class=\"col-md-3\" for=\"fecha_des\">Fecha</label><label class=\"col-md-9\" for=\"tema_des\">Segumiento</label> </div> <div class=\"row\"> <input type=\"text\" class=\"col-md-3\" value=\"" + result[x].fecha + "\" id=\"fecha_seg\" name=\"fecha_seg\" disabled> <input type=\"text\" class=\"col-md-9\" value=\"" + result[x].seguimiento + "\" id=\"tema_seg\" name=\"tema_seg\" disabled> </div></div> <div id=\"" + result[x].id + "\" class=\"col-md-2\" name=\"div_button_seg\"> <button type=\"submit\" id=\"consult_acompasocio\" name=\"consult_acompasocio\" class=\"submit\" data-toggle=\"modal\" data-target=\"#myModalAcompaSocio\">Detalle</button> </div></div>");
                    }
                    $('#list_AcompaSocio').on('click', '#consult_acompasocio', function() {
                        checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
                        var id = $(this).parent().attr('id');
                        loadJustOneAcompaSocio();
                    });
                }
                else {
                    $('#list_AcompaSocio').append("<label>No registra</label><br>");
                    $('#socioedu_add_AcompaSocio').prop("disabled", false);
                    $('#socioedu_add_segsocio').addClass('hide');
                }
            }
            else {
                swal({
                    title: error,
                    html: true,
                    type: "error",
                    text: msg.msg,
                    confirmButtonColor: "#D3D3D3"
                }, function(isConfirm) {
                    if (isConfirm) {}
                });
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg);
        },
    });
}

function loadJustOneAcompaSocio() {
    var data = new Array();
    var idtalentos = $('#ficha_estudiante #idtalentos').val();
    data.push({
        name: "function",
        value: "load_AcompaSocio"
    });
    data.push({
        name: "idtalentos",
        value: idtalentos
    });
    intiAcompaSocioForm();
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/socioeducativo.php",
        success: function(msg) {
            var error = msg.error;
            if (!error) {
                intiAcompaSocioForm();
                var result = msg.result;
                var rows = msg.rows;

                console.log(msg.result);
                if (rows > 0) {
                    for (x in result) {

                        //se muestra los datos de creacion
                        $('#created_date').text("Creado el " + result[x].createdate);
                        $('#div_created').removeClass('hide');

                        //se llena el formulario

                        $('#AcompaSocio #date').val(result[x].fecha);

                        $('#AcompaSocio .antecedentes').each(function(i, item) {
                            if (item.name == 'psicologia') {
                                if (item.value == result[x].antecedente_psicosocial) {
                                    $(this).prop('checked', true);
                                }
                            }
                            else if (item.name == 'tsocial') {
                                if (item.value == result[x].antecedente_tsocial) {
                                    $(this).prop('checked', true);
                                }
                            }
                            else if (item.name == 'teo') {
                                if (item.value == result[x].antecedente_terapiao) {
                                    $(this).prop('checked', true);
                                }
                            }
                        });

                        if ((result[x].antecedente_terapiao == 1) || (result[x].antecedente_tsocial == 1) || (result[x].antecedente_psicosocial == 1)) {
                            $('#AcompaSocio #motivo').prop('disabled', false);
                        }

                        $('#AcompaSocio #motivo').val(result[x].descripcion_antecedente);

                        var ingresos = result[x].ingresos;
                        $('#AcompaSocio #mytableIngresos tbody').html('');

                        for (y in ingresos) {
                            $('#AcompaSocio #mytableIngresos tbody').append('<tr> <td><input id="descripIngreso" name="descripIngreso" size="8" maxlength="15" type="text" value="' + ingresos[y].concepto + '" /></td> <td><input id="valorIngreso" name="valorIngreso" type="text" size="8" maxlength="8" value="' + ingresos[y].monto + '"   /></td> <td><a href="#" id="removeEco"><span class="glyphicon glyphicon-remove"></span></a></td> <td class="hide"><input id="idIngreso" name="idIngreso" size="1" value="' + ingresos[y].id + '" maxlenght="1" type="text" /></td> </tr>');
                        }

                        caculateSum('#AcompaSocio #valorIngreso', '#AcompaSocio #totalIngresos');

                        var egresos = result[x].egresos
                        $('#AcompaSocio #mytableEgresos tbody').html('');
                        for (y in egresos) {
                            $('#AcompaSocio #mytableEgresos tbody').append('<tr> <td><input id="descripEgreso" name="descripEgreso" size="8" maxlength="15" type="text" value ="' + egresos[y].concepto + '" /></td> <td><input id="valorEgreso" name="valorEgreso" type="text" size="8" maxlength="8" value ="' + egresos[y].monto + '" /></td> <td><a href="#" id="removeEco"><span class="glyphicon glyphicon-remove"></span></a></td> <td class="hide"><input id="idEgreso" name="idEgreso" size="1" value="' + egresos[y].id + '" maxlenght="1" type="text" /></td> </tr>');
                        }

                        caculateSum('#AcompaSocio #valorEgreso', '#AcompaSocio #totalEgresos');

                        var familia = result[x].familia;
                        $('#AcompaSocio #mytablefamilia tbody').html('');
                        for (y in familia) {
                            $('#AcompaSocio #mytablefamilia tbody').append(' <tr> <td><input id="nombreFamilia" name="nombreFamilia" size="8" maxlength="8" type="text" value="' + familia[y].nombre_pariente + '" /></td> <td><select id="parentescoFamilia"  name="parentescoFamilia" > <option value="MADRE" selected>MADRE</option>  <option value="PADRE">PADRE</option> <option value="HERMANO/A">HERMANO/A</option> <option value="TIO/A">TIO/A</option> <option value="ABUELO/A">ABUELO/A</option> <option value="PRIMO">PRIMO</option> <option value="OTRO">OTRO</option> </select></td>  <td><input id="ocupacionFamilia" name="ocupacionFamilia" size="8" maxlenght="8" type="text" value="' + familia[y].ocupacion + '" /></td> <td><input id="telefonoFamilia" name="telefonoFamilia" size="8" maxlenght="8" type="text" value="' + familia[y].telefono + '" /></td> <td><a href="#" id="removeFamilia"><span class="glyphicon glyphicon-remove"></span></a></td>  <td class="hide"><input id="idFamilia" name="idFamilia" size="1" value="' + familia[y].id + '" maxlenght="1" type="text" /></td>  </tr>');
                            //por si se adiciona <td><input id="edadFamilia" name="edadFamilia" size="8" maxlenght="8" type="text" /></td> <td><input id="estadoCivilfamilia" name="estadoCivilfamilia" size="8" maxlenght="8" type="text" /></td>
                            $('#AcompaSocio #mytablefamilia tbody tr').each(function() {
                                if ($(this).find('td input[id="idFamilia"]').val() == familia[y].id) {
                                    $(this).find('td select[id="parentescoFamilia"]').val(familia[y].parentesco);
                                }
                            });

                        }

                        $('#AcompaSocio #composicionFamiliar').val(result[x].comp_familiar);
                        $('#AcompaSocio #dinamicaFamiliar').val(result[x].dinamica_familiar);
                        $('#AcompaSocio #apoyoFamiliar').val(result[x].red_familiar);
                        $('#AcompaSocio #apoyoEducativo').val(result[x].red_edu);
                        $('#AcompaSocio #apoyoSocial').val(result[x].red_social);
                        $('#AcompaSocio #apoyoLaboral').val(result[x].red_laboral);
                        $('#AcompaSocio #monitor').text(result[x].infoProfesional);
                        $('#AcompaSocio #infomonitor').removeClass('hide');

                        if (result[x].fr_spa == 1) {
                            $('#AcompaSocio #resgo1').prop('checked', true);
                            $('#AcompaSocio #input_r1').prop('disabled', false);
                            $('#AcompaSocio #input_r1').val(result[x].fr_spa_observaciones);
                        }
                        if (result[x].fr_embarazo == 1) {
                            $('#AcompaSocio #resgo2').prop('checked', true);
                            $('#AcompaSocio #input_r2').prop('disabled', false);
                            $('#AcompaSocio #input_r2').val(result[x].fr_embarazo_observaciones);
                        }
                        if (result[x].fr_maltrato == 1) {
                            $('#AcompaSocio #resgo3').prop('checked', true);
                            $('#AcompaSocio #input_r3').prop('disabled', false);
                            $('#AcompaSocio #input_r3').val(result[x].fr_maltrato_observaciones);
                        }
                        if (result[x].fr_abusosexual == 1) {
                            $('#AcompaSocio #resgo4').prop('checked', true);
                            $('#AcompaSocio #input_r4').prop('disabled', false);
                            $('#AcompaSocio #input_r4').val(result[x].fr_abusosexual_observaciones);
                        }
                        if (result[x].fr_otros == 1) {
                            $('#AcompaSocio #resgo5').prop('checked', true);
                            $('#AcompaSocio #input_r5').prop('disabled', false);
                            $('#AcompaSocio #input_r5').val(result[x].fr_otros_observaciones);
                        }


                        $('#AcompaSocio #observacionGeneral').val(result[x].observaciones);
                        $('#AcompaSocio #acuerdos').val(result[x].acuerdos);
                        $('#AcompaSocio #descripSeg').val(result[x].seguimiento);

                        $('#AcompaSocio #' + result[x].act_status + '').children().prop('checked', true);


                        $('#myModalAcompaSocio #upd_seg').removeClass('hide');
                        $('#myModalAcompaSocio #upd_seg').parent().attr('id', result[x].id);
                        $('#myModalAcompaSocio #save_seg').addClass('hide');
                    }
                }
                else {
                    swal("No se ecnotraron resultados", "warning");
                }
            }
            else {
                swal({
                    title: error,
                    html: true,
                    type: "error",
                    text: msg.msg,
                    confirmButtonColor: "#D3D3D3"
                }, function(isConfirm) {
                    if (isConfirm) {}
                });
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    });
}

function addInfoDataAcompaSocio(data) {
    //la informacion económica se va almacenar en los siguientes arreglos
    var descripIngresos = new Array();
    var valorIngresos = new Array();
    var idIngresos = new Array();
    var descripEgresos = new Array();
    var valorEgreso = new Array();
    var idEgresos = new Array();

    //información familiar se va almacenar en los siguientes arregñps
    var nombreFamilia = new Array();
    var parentescoFamilia = new Array();
    var edadFamilia = new Array();
    var estadoCivilfamilia = new Array();
    var ocupacionFamilia = new Array();
    var telefonoFamilia = new Array();
    var idFamilia = new Array();

    $.each(data, function(i, item) {

        //informacion economica

        if (item.name == "descripIngreso") {
            descripIngresos.push(item.value);
        }

        else if (item.name == "valorIngreso") {
            valorIngresos.push(item.value);
        }

        else if (item.name == "idIngreso") {
            idIngresos.push(item.value);
        }

        else if (item.name == "descripEgreso") {
            descripEgresos.push(item.value);
        }

        else if (item.name == "valorEgreso") {
            valorEgreso.push(item.value);
        }

        else if (item.name == "idEgreso") {
            idEgresos.push(item.value);
        }

        //informacion familiar

        else if (item.name == "nombreFamilia") {
            nombreFamilia.push(item.value);
        }

        else if (item.name == "parentescoFamilia") {
            parentescoFamilia.push(item.value);
        }

        else if (item.name == "edadFamilia") {
            edadFamilia.push(item.value);
        }

        else if (item.name == "estadoCivilfamilia") {
            estadoCivilfamilia.push(item.value);
        }

        else if (item.name == "ocupacionFamilia") {
            ocupacionFamilia.push(item.value);
        }

        else if (item.name == "telefonoFamilia") {
            telefonoFamilia.push(item.value);
        }

        else if (item.name == "idFamilia") {
            idFamilia.push(item.value);
        }
    });

    //informacion economica
    data.push({
        name: "descripIngresos",
        value: descripIngresos
    }); //envia por POST el contenido del ultimo item con el name especificado en este caso "descripIngreso"
    data.push({
        name: "valorIngresos",
        value: valorIngresos
    });
    data.push({
        name: "idIngresos",
        value: idIngresos
    });
    data.push({
        name: "descripEgresos",
        value: descripEgresos
    });
    data.push({
        name: "valorEgresos",
        value: valorEgreso
    });
    data.push({
        name: "idEgresos",
        value: idEgresos
    });
    //informacion familiar
    data.push({
        name: "nombreFamilia",
        value: nombreFamilia
    });
    data.push({
        name: "parentescoFamilia",
        value: parentescoFamilia
    });
    data.push({
        name: "edadFamilia",
        value: edadFamilia
    });
    data.push({
        name: "estadoCivilfamilia",
        value: estadoCivilfamilia
    });
    data.push({
        name: "ocupacionFamilia",
        value: ocupacionFamilia
    });
    data.push({
        name: "telefonoFamilia",
        value: telefonoFamilia
    });
    data.push({
        name: "idFamilia",
        value: idFamilia
    });

    //se almacena el idtalentos
    var idtalentos = $('#ficha_estudiante #idtalentos').val();
    data.push({
        name: "idtalentos",
        value: idtalentos
    });

    return data;
}

function updateAcompaSocio(id_acompa) {
    var validation = validateAcompasocio();
    if (validation.isvalid) {
        var data = $('#myModalAcompaSocio #AcompaSocio').serializeArray();
        data = addInfoDataAcompaSocio(data);
        data.push({
            name: "function",
            value: "updateAcompaSocio"
        });
        data.push({
            name: "idAcompaSocio",
            value: id_acompa
        });

        $.ajax({
            type: "POST",
            data: data,
            url: "../managers/socioeducativo.php",
            success: function(msg) {
                var error = msg.error;
                if (!error) {
                    swal({
                        title: "Actualizado con exito!!",
                        html: true,
                        type: "success",
                        text: msg.msg,
                        confirmButtonColor: "#d51b23"
                    }, function(isConfirm) {
                        if (isConfirm) {}
                    });
                    $('#myModalAcompaSocio').modal('toggle');
                    $('#myModalAcompaSocio').modal('toggle');
                    $('#myModalAcompaSocio #save_seg').addClass('hide');
                    $('.modal-backdrop').remove();
                    loadAll_AcompaSocio();
                }
                else {
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
                console.log(msg)
            },
        });
    }
    else {
        swal({
            title: "Error",
            html: true,
            type: "warning",
            text: "Detalles del error:<br>" + validation.detalle,
            confirmButtonColor: "#D3D3D3"
        });
    }

}

function validateSegPares(data) {
    var isvalid = true;
    var detalle = "";
    var date, h_ini, m_ini, h_fin, m_fin, tema, objetivos, individual, riesgo_ind, familiar, riesgo_familiar, academico, riesgo_aca, economico, riesgo_econom, vida_uni, riesgo_uni;

    $.each(data, function(i, field) {

        switch (field.name) {
            case 'date':
                date = field.value;
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
            case 'individual':
                individual = field.value;
                break;
            case 'riesgo_ind':
                riesgo_ind = field.value;
                break;
            case 'familiar':
                familiar = field.value;
                break;
            case 'riesgo_familiar':
                riesgo_familiar = field.value;
                break;
            case 'academico':
                academico = field.value;
                break;
            case 'riesgo_aca':
                riesgo_aca = field.value;
                break;
            case 'economico':
                economico = field.value;
                break;
            case 'riesgo_econom':
                riesgo_econom = field.value;
                break;
            case 'vida_uni':
                vida_uni = field.value;
                break;
            case 'riesgo_uni':
                riesgo_uni = field.value;
                break;
        }
    });
    if (!date) {
        isvalid = false;
        detalle += "* Selecciona una Fecha de seguimiento valida<br>";
    }

    if (h_ini > h_fin) {
        isvalid = false;
        detalle += "* La hora final debe ser mayor a la inicial<br>";
    }
    else if (h_ini == h_fin) {
        if (m_ini > m_fin) {
            isvalid = false;
            detalle += "* La hora final debe ser mayor a la inicial<br>";
        }
    }



    if (tema == "") {
        isvalid = false;
        detalle += "* La informacion del \"tema\" es obligatoria<br>";
    }

    if (objetivos == "") {
        isvalid = false;
        detalle += "* La informacion de \"objetivos\" es obligatoria<br>";
    }

    if (individual != "" && riesgo_ind == null) {
        isvalid = false;
        detalle += "Por favor califica el seguimiento \"individual\"<br>";
    }

    if (familiar != "" && riesgo_familiar == null) {
        isvalid = false;
        detalle += "Por favor califica el seguimiento \"familiar\"<br>";
    }

    if (academico != "" && riesgo_aca == null) {
        isvalid = false;
        detalle += "Por favor califica el seguimiento \"académico\"<br>";
    }

    if (economico != "" && riesgo_econom == null) {
        isvalid = false;
        detalle += "Por favor califica el seguimiento \"económico\"<br>";
    }

    if (vida_uni != "" && riesgo_uni == null) {
        isvalid = false;
        detalle += "Por favor califica el seguimiento \"Vida universitaria y ciudad\"<br>";
    }


    var result = {
        isvalid: isvalid,
        detalle: detalle
    };

    return result;
}

function getVariableGetByName() {
    var variables = {};
    var arreglos = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        variables[key] = value;
    });
    return variables;
}



function initFormSeg() {

    var date = new Date();
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    var minutes = date.getMinutes();
    var hour = date.getHours();

    //incializar hora
    var hora = "";
    for (var i = 0; i < 24; i++) {
        if (i == hour) {
            if (hour < 10) hour = "0" + hour;
            hora += "<option value=\"" + hour + "\" selected>" + hour + "</option>";
        }
        else if (i < 10) {
            hora += "<option value=\"0" + i + "\">0" + i + "</option>";
        }
        else {
            hora += "<option value=\"" + i + "\">" + i + "</option>";
        }
    }

    var min = "";
    for (var i = 0; i < 60; i++) {

        if (i == minutes) {
            if (minutes < 10) minutes = "0" + minutes;
            min += "<option value=\"" + minutes + "\" selected>" + minutes + "</option>";
        }
        else if (i < 10) {
            min += "<option value=\"0" + i + "\">0" + i + "</option>";
        }
        else {
            min += "<option value=\"" + i + "\">" + i + "</option>";
        }
    }

    $('#seguimiento #h_ini').append(hora);
    $('#seguimiento #m_ini').append(min);

    $('#seguimiento #h_fin').append(hora);
    $('#seguimiento #m_fin').append(min);
    $("#seguimiento #infomonitor").addClass('hide');
    $("#seguimiento").find("input:text, textarea").val('');
    $("#seguimiento").find("input:radio,input:checkbox").prop('checked', false);
    $('#upd_seg').attr('disabled', false);
    $('#upd_seg').attr('title', '');
    $('#seguimiento').find('select, textarea, input').attr('disabled', false);

}

function intiAcompaSocioForm() {

    $('#AcompaSocio .antecedentes').each(function() {
        if ($(this).val() == 0) {
            $(this).prop("checked", "checked");
        }
    });
    $('#AcompaSocio #mytableIngresos tbody').html('');
    $('#AcompaSocio #mytableEgresos tbody').html('');
    $('#AcompaSocio #mytablefamilia tbody').html('');
    $('#AcompaSocio #motivo').prop('disabled', true);

    $('#AcompaSocio #input_r1').prop('disabled', true);
    $('#AcompaSocio #input_r2').prop('disabled', true);
    $('#AcompaSocio #input_r3').prop('disabled', true);
    $('#AcompaSocio #input_r4').prop('disabled', true);
    $('#AcompaSocio #input_r5').prop('disabled', true);

    for (var i = 1; i <= 5; i++) {
        $('#AcompaSocio #resgo' + i).prop("checked", false);
        $('#AcompaSocio #input_r' + i).val('');
    }

    $('#AcompaSocio').each(function() {

        var name = $(this).attr("name");
        if (name != 'psicologia' && name != 'tsocial' && name != 'teo') {
            $(this).val('');
        }
    });

    $('#AcompaSocio  #upd_seg').attr('disabled', false);
    $('#AcompaSocio  #upd_seg').attr('title', '');
}

function validateAcompasocio() {
    var detalle = "";
    var isvalid = true;
    $('#AcompaSocio #valorIngreso').each(function() {

        //add only if the value is number
        if (!isNaN(this.value) && this.value.length != 0) {
            $(this).css("background-color", "#FEFFB0");
        }
        else {
            $(this).css("background-color", "red");
            isvalid = false;
            detalle += "* Asegúrate de que los valores en los montos de los ingresos sean numéricos y NO sean nulos.<br>";
        }
    });

    $('#AcompaSocio #valorEgreso').each(function() {
        //add only if the value is number
        if (!isNaN(this.value) && this.value.length != 0) {
            $(this).css("background-color", "#FEFFB0");
        }
        else {
            $(this).css("background-color", "red");
            isvalid = false;
            detalle += "* Asegúrate de que los valores en los montos de los egresos sean numéricos y NO sean nulos.<br>";
        }
    });

    $('#AcompaSocio #telefonoFamilia').each(function() {
        //add only if the value is number
        if (!isNaN(this.value)) {
            $(this).css("background-color", "#FEFFB0");
        }
        else if (this.value.length != 0) {
            $(this).css("background-color", "red");
            isvalid = false;
            detalle += "* Asegúrate de que los valores de los telefonos de la familia sean numéricos.<br>";
        }
    });

    if (!$('#AcompaSocio').find('input[name="optradio"]').is(':checked')) {
        $(this).css("background-color", "red");
        isvalid = false;
        detalle += "* por favor califica el seguimiento.<br>";
    }

    return {
        isvalid: isvalid,
        detalle: detalle
    };
}

function dropEcono(idEcono) {
    var data = new Array();
    data.push({
        name: "function",
        value: "deleteEconomica"
    });
    data.push({
        name: "idEco",
        value: idEcono
    });

    // var idtalentos = $('#ficha_estudiante #idtalentos').val();
    //data.push({name:"idtalentos",value:idtalentos});
    console.log(data);
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/socioeducativo.php",
        success: function(msg) {
            var error = msg.error;
            if (!error) {
                // swal({title: "Actualizado con exito!!", html:true, type: "success",  text: msg.msg, confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) {  } });
                console.log(msg.msg);
            }
            else {
                //swal({title: error, html:true, type: "error",  text: msg.msg, confirmButtonColor: "#D3D3D3" });
                console.log(msg.msg);
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    });
}

function dropFamilia(idfamilia) {
    var data = new Array();
    data.push({
        name: "function",
        value: "deleteFamilia"
    });
    data.push({
        name: "idFamilia",
        value: idfamilia
    });

    // var idtalentos = $('#ficha_estudiante #idtalentos').val();
    //data.push({name:"idtalentos",value:idtalentos});
    console.log(data);
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/socioeducativo.php",
        success: function(msg) {
            var error = msg.error;
            if (!error) {
                //swal({title: "Actualizado con exito!!", html:true, type: "success",  text: msg.msg, confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) {  } });
                console.log(msg.msg);

            }
            else {
                //swal({title: error, html:true, type: "error",  text: msg.msg, confirmButtonColor: "#D3D3D3" });
                console.log(msg.msg);
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    });
}

function validatePrimerAcerca() {
    var isvalid = true;
    var detalle = "";
    if (!$('#myModalPrimerAcerca').find('input[name="optradio"]').is(':checked')) {

    }
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

function loadRisk(idUser) {

    $.ajax({
        type: "POST",
        data: {
            id: idUser
        },
        url: "../managers/get_risk.php",
        success: function(msg) {

            var enumRisk = ['NO REGISTRA', 'BAJO', 'MEDIO', 'ALTO'];

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

            if (geo_risk > 0) {
                $('#geo_risk').append('<span>-' + enumRisk[geo_risk] + '</span>');
                if (geo_risk == 1) {
                    $('#geo_risk').removeClass('div_no_risk');
                    $('#geo_risk').addClass('div_low_risk');
                }
                else if (geo_risk == 2) {
                    $('#geo_risk').removeClass('div_no_risk');
                    $('#geo_risk').addClass('div_medium_risk');
                }
                else if (geo_risk == 3) {
                    $('#geo_risk').removeClass('div_no_risk');
                    $('#geo_risk').addClass('div_high_risk');
                }
            }
            else {
                $('#geo_risk').append('<span>-No registra</em></span>');
            }


            if (individual_r > 0) {
                $('#individual_risk').append('<span>-' + enumRisk[individual_r] + '</span>');
                if (individual_r == 1) {
                    $('#individual_risk').removeClass('div_no_risk');
                    $('#individual_risk').addClass('div_low_risk');
                }
                else if (individual_r == 2) {
                    $('#individual_risk').removeClass('div_no_risk');
                    $('#individual_risk').addClass('div_medium_risk');
                }
                else if (individual_r == 3) {
                    $('#individual_risk').removeClass('div_no_risk');
                    $('#individual_risk').addClass('div_high_risk');
                }

            }
            else {
                $('#individual_risk').append('<span>-No registra</span>');
            }

            if (familiar_r > 0) {
                $('#familiar_risk').append('<span>-' + enumRisk[familiar_r] + '</span>');
                if (familiar_r == 1) {
                    $('#familiar_risk').removeClass('div_no_risk');
                    $('#familiar_risk').addClass('div_low_risk');
                }
                else if (familiar_r == 2) {
                    $('#familiar_risk').removeClass('div_no_risk');
                    $('#familiar_risk').addClass('div_medium_risk');
                }
                else if (familiar_r == 3) {
                    $('#familiar_risk').removeClass('div_no_risk');
                    $('#familiar_risk').addClass('div_high_risk');
                }
            }
            else {
                $('#familiar_risk').append('<span>-No registra</span>');
            }

            if (economic_r > 0) {
                $('#economic_risk').append('<span>-' + enumRisk[economic_r] + '</span>');
                if (economic_r == 1) {
                    $('#economic_risk').removeClass('div_no_risk');
                    $('#economic_risk').addClass('div_low_risk');
                }
                else if (economic_r == 2) {
                    $('#economic_risk').removeClass('div_no_risk');
                    $('#economic_risk').addClass('div_medium_risk');
                }
                else if (economic_r == 3) {
                    $('#economic_risk').removeClass('div_no_risk');
                    $('#economic_risk').addClass('div_high_risk');
                }
            }
            else {
                $('#economic_risk').append('<span>-No registra</span>');
            }

            if (academic_r > 0) {
                $('#academic_risk').append('<span>-' + enumRisk[academic_r] + '</span>');
                if (academic_r == 1) {
                    $('#academic_risk').removeClass('div_no_risk');
                    $('#academic_risk').addClass('div_low_risk');
                }
                else if (academic_r == 2) {
                    $('#academic_risk').removeClass('div_no_risk');
                    $('#academic_risk').addClass('div_medium_risk');
                }
                else if (academic_r == 3) {
                    $('#academic_risk').removeClass('div_no_risk');
                    $('#academic_risk').addClass('div_high_risk');
                }
            }
            else {
                $('#academic_risk').append('<span>-No registra</span>');
            }

            if (life_risk > 0) {
                $('#life_risk').append('<span>-' + enumRisk[life_risk] + '</span>');
                if (life_risk == 1) {
                    $('#life_risk').removeClass('div_no_risk');
                    $('#life_risk').addClass('div_low_risk');
                }
                else if (life_risk == 2) {
                    $('#life_risk').removeClass('div_no_risk');
                    $('#life_risk').addClass('div_medium_risk');
                }
                else if (life_risk == 3) {
                    $('#life_risk').removeClass('div_no_risk');
                    $('#life_risk').addClass('div_high_risk');
                }
            }
            else {
                $('#life_risk').append('<span>-No registra</span>');
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log("Error al cargar el riesgo");
        }
    });

}

function loadMotivosRetiros(previous) {
    var data = new Array();
    data.push({
        name: "function",
        value: "loadMotivos"
    });
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/motivos_retiros.php",
        success: function(msg) {
            $('#retiroForm #motivo').empty();
            $('#retiroForm #detalle').val('');
            var error = msg.error;
            if (!error) {
                if (msg.size > 0) {
                    var data = msg.data;
                    for (x in data) {
                        $('#retiroForm #motivo').append('<option value="' + data[x].id + '">' + data[x].decripcion + '</option>');
                    }
                }
                else {
                    $('#retiroForm #motivo').append('<option value="0" selected>No Definidos</option>');
                }

                $('#retiroForm')

            }
            else {

                console.log(msg.msg);
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    });
}

function manageIcetexStatus() {
    //validar cambio en estado
    var previous;
    $('#ficha_estudiante #estado').on('focus', function() {
        // se guarda el valor previo con focus
        previous = this.value;

    }).change(function() {
        var newstatus = $(this).val();
        swal({
                title: "Estás seguro/a de realizar este cambio?",
                text: "El estado del estudiante pasará de " + previous + " a " + newstatus,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d51b23",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                allowEscapeKey: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    if (newstatus == "RETIRADO") {
                        swal({
                                title: "Causa del retiro",
                                text: "Describir la causa del retiro",
                                type: "input",
                                showCancelButton: true,
                                closeOnConfirm: false,
                                animation: "slide-from-top",
                                inputPlaceholder: "Por que?",
                                allowEscapeKey: false
                            },
                            function(inputValue) {
                                if (inputValue === false) return false;

                                if (inputValue === "") {
                                    swal.showInputError("Por favor describir la causa del retiro");
                                    return false;
                                }
                                else {
                                    var today = new Date();
                                    var dd = today.getDate();
                                    var mm = today.getMonth() + 1; //enero es 0!
                                    var yyyy = today.getFullYear();

                                    if (dd < 10) {
                                        dd = '0' + dd;
                                    }

                                    if (mm < 10) {
                                        mm = '0' + mm;
                                    }
                                    $('#ficha_estudiante #observaciones').append('\nCausa del retiro Ases ' + dd + '/' + mm + '/' + yyyy + ': ' + inputValue);
                                    swal("Estado cambiado!", "El estado del estudiante ha sido cambiado. No olvides guardar tus cambios", "success");
                                }

                            });
                    }
                    else {
                        swal("Estado cambiado!", "El estado del estudiante ha sido cambiado. No olvides guardar tus cambios", "success");
                    }

                }
                else {
                    $('#estado').val(previous);
                }

            });
    });
}



function manageAsesStatus() {
    //validar cambio en estado
    var previous;
    $('#ficha_estudiante #estadoAses').on('focus', function() {
        // se guarda el valor previo con focus
        previous = this.value;
    }).change(function() {
        var newstatus = $(this).val();
        swal({
                title: "Estás seguro/a de realizar este cambio?",
                text: "El estado del estudiante pasará de " + previous + " a " + newstatus,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d51b23",
                confirmButtonText: "Yes",
                closeOnConfirm: true,
                allowEscapeKey: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    if (newstatus == "RETIRADO") {
                        loadMotivosRetiros(previous);
                        $('#myModalRetiro').modal('show');
                    }
                    else {
                        swal("Estado cambiado!", "El estado del estudiante ha sido cambiado. No olvides guardar tus cambios", "success");
                    }

                }
                else {
                    $('#estado').val(previous);
                }
            });
    });
}

function saveMotivoRetiro() {
    var data = new Array();
    var talentosid = $('#idtalentos').val();
    var motivoid = $('#retiroForm #motivo').val();
    var detalle = $('#retiroForm #detalle').val();
    console.log(motivoid);

    data.push({
        name: "function",
        value: "saveMotivoRetiro"
    });
    data.push({
        name: "talentosid",
        value: talentosid
    });
    data.push({
        name: "motivoid",
        value: motivoid
    });
    data.push({
        name: "detalle",
        value: detalle
    });
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/motivos_retiros.php",
        success: function(msg) {
            var error = msg.error;
            if (!error) {
                if (msg == true) {
                    swal("Estado cambiado!", "El estado del estudiante ha sido cambiado.", "success");
                    $('#myModalRetiro').modal('toggle');
                    $('.modal-backdrop').remove();
                    update();
                }
                else {
                    swal("Error!", "No se ha podido almacenar el motivo de retiro.", "error");
                }
            }
            else {

                console.log(msg.msg);
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    });
}



function loadRiskValues() {
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
                console.log(individual_r);
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

function riskGraphic(individual_r, familiar_r, economic_r, academic_r, life_r, geo_r) {

    var w = 500,
        h = 500;

    var colorscale = d3.scale.category10();

    //Data
    var d = [
        [{
                axis: "Individual",
                value: individual_r
            }, {
                axis: "Económico",
                value: economic_r
            }, {
                axis: "Familiar",
                value: familiar_r
            }, {
                axis: "Vida Universitaria",
                value: life_r
            }, {
                axis: "Académico",
                value: academic_r
            }, {
                axis: "Geogr��fico",
                value: geo_r
            },

        ]
    ];

    //Options for the Radar chart, other than default
    var mycfg = {
        w: w,
        h: h,
        maxValue: 3,
        levels: 3,
        ExtraWidthX: 300
    }

    //Call function to draw the Radar chart
    //Will expect that data is in %'s
    RadarChart.draw("#myModalBody", d, mycfg);

    ////////////////////////////////////////////
    /////////// Initiate legend ////////////////
    ////////////////////////////////////////////

    var svg = d3.select('#body')
        .selectAll('svg')
        .append('svg')
        .attr("width", w + 300)
        .attr("height", h)
}

function get_programa_facultad(idStudent) {

    $.ajax({
        type: 'POST',
        url: '../managers/get_programa_facultad.php',
        data: {
            idStudent: idStudent,
            fun: 'get_program'
        },
        success: function(msg) {
            $('#input_programa').empty();
            $('#input_programa').append(msg);
        },
        dataType: 'text',
        error: function(msg) {
            console.log(msg)
        }
    });

    $.ajax({
        type: 'POST',
        url: '../managers/get_programa_facultad.php',
        data: {
            idStudent: idStudent,
            fun: 'get_school'
        },
        success: function(msg) {
            $('#input_facultad').empty();
            $('#input_facultad').append(msg);
        },
        dataType: 'text',
        error: function(msg) {
            console.log(msg)
        }
    });

}

// function loadGoogleMapsEmbed(){

//     var data = new Array();
//     var talentosid = $('#idtalentos').val();

//     data.push({name:"function",value:"loadCoordinates"});
//     data.push({name:"talentosid",value:talentosid});

//     var id = $('#idtalentos').val();
//     $.ajax({
//         type: "POST",
//         data: data,
//         url: "../managers/demographic.php",
//         success: function(msg)
//         {   
//             $('#ficha_estudiante #map_div').empty();
//             var iframeMap = '';
//             if(msg){
//                 iframeMap = '<iframe width="550" height="300" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/directions?key=AIzaSyAoE-aPVfruphY4V4BbE8Gdwi93x-5tBTM&origin='+msg.latitud+','+msg.longitud+'&destination=3.3759493,-76.5355789&mode=transit" allowfullscreen> </iframe>';
//             }else{
//                 iframeMap = '<iframe width="550" height="300" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/directions?key=AIzaSyAoE-aPVfruphY4V4BbE8Gdwi93x-5tBTM&origin=3.3759493,-76.5355789&destination=3.3759493,-76.5355789&mode=transit" allowfullscreen> </iframe>';
//             }

//             $('#ficha_estudiante #map_div').append(iframeMap);
//         },
//         dataType: "json",
//         cache: "false",
//         error: function(msg){console.log(msg)},
//     });
// }


function loadGeograficInfo() {

    data = new Array();
    data.push({
        name: "function",
        value: "load_neighborhood"
    });
    $('#neighborhood_select').empty();

    $.ajax({
        type: 'POST',
        data: data,
        url: "../managers/demographic.php",
        success: function(msg) {

            $('#neighborhood_select').append('<option>Seleccione un barrio</option>');

            for (n in msg) {
                $('#neighborhood_select').append('<option id="' + msg[n].id + '" value="' + msg[n].id + '">' + msg[n].nombre + '</option>');
                console.log(msg);
            }
        },
        dataType: 'json',
        cache: 'false',
        error: function(msg) {
            console.log(msg)
        }
    });

    var data = new Array();
    var talentosid = $('#idtalentos').val();

    data.push({
        name: "function",
        value: "loadCoordinates"
    });
    data.push({
        name: "talentosid",
        value: talentosid
    });

    var id = $('#idtalentos').val();
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/demographic.php",
        success: function(msg) {
            // $('#ficha_estudiante #demografica').empty();
            var iframeMap = '';
            if (msg) {
                $('#demografica #longitud').val(msg.longitud);
                $('#demografica #latitud').val(msg.latitud);
                $('#' + msg.barrio).prop('selected', true);
                $('#demografica #geographic_risk').val(msg.riesgo);
            }
            else {
                $('#demografica #longitud').val(0);
                $('#demografica #latitud').val(0);
                $('#demografica #barrio').val(0);
                $('#demografica #geographic_risk').val(1);
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    });
}

function edit_geographic(element) {
    $(element).hide();
    $('#save_button_div').show();
    $('#latitud').prop('readonly', false);
    $('#longitud').prop('readonly', false);
    $('#neighborhood_select').prop('disabled', false);
    $('#geographic_risk').prop('disabled', false);
}

function cancel_edition_geographic() {
    swal({
            title: "¿Está seguro(a) que desea cancelar?",
            text: "La información se perderá y no se podrá recuperar",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d51b23",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm: true,
        },
        function(isConfirm) {
            if (isConfirm) {
                loadGeograficInfo();
                $('#save_button_div').hide();
                $('#edit_demo').show();
                $('#latitud').prop('readonly', true);
                $('#longitud').prop('readonly', true);
                $('#neighborhood_select').prop('disabled', true);
                $('#geographic_risk').prop('disabled', true);
            }
        });

}

function save_geographic() {

    var array_geographic = new Array();
    array_geographic = $('#geographic_form').serializeArray();

    var latitude = $('#latitud').val();
    var length = $('#longitud').val();
    var neighborhood_id = $('#neighborhood_select').val();
    var risk = $('#geographic_risk').val();

    console.log(array_geographic);

    var data = new Array();
    data.push({
        name: "function",
        value: "save_info_geographic"
    });
    data.push({
        name: "id_student",
        value: $('#idtalentos').val()
    });
    data.push({
        name: "latitude",
        value: latitude
    });
    data.push({
        name: "length",
        value: length
    });
    data.push({
        name: "id_neighborhood",
        value: neighborhood_id
    });
    data.push({
        name: "geographic_risk",
        value: risk
    });

    $.ajax({
        type: 'POST',
        data: data,
        url: "../managers/demographic.php",
        success: function(msg) {
            if (msg == '1') {
                sweetAlert("Éxito",
                    "La información ha sido actualizada exitosamente",
                    "success");
                $('#save_button_div').hide();
                $('#edit_demo').show();
                $('#latitud').prop('readonly', true);
                $('#longitud').prop('readonly', true);
                $('#neighborhood_select').prop('disabled', true);
                $('#geographic_risk').prop('disabled', true);
            }
            else {
                sweetAlert("Error",
                    "Ha ocurrido un error al comunicarse con el servidor.",
                    "error");
            }
        },
        dataType: "text",
        cache: false,
        error: function(msg) {
            console.log(msg)
        }
    });

    $('#save_button_div').hide();
    $('#edit_demo').show();
    $('#latitud').prop('readonly', true);
    $('#longitud').prop('readonly', true);
    $('#neighborhood_select').prop('disabled', true);
    $('#geographic_risk').prop('disabled', true);
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

    console.log(observations_array);

    if (high_risk_array.length != 0) {
        $.ajax({
            type: "POST",
            data: data_email,
            url: "../managers/seguimiento.php",
            success: function(msg) {
                // console.log(msg);
            },
            dataType: "text",
            cache: "false",
            error: function(msg) {
                console.log(msg)
            }
        });
    }
}

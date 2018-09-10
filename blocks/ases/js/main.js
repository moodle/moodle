$(document).ready(function() {
    /*
     CONTROLES PARA EL FORMULARIO GENERAL DE CONSULTAS
    */

    //actualizar cohortes dinamicamente
    loadDinamicSelect();
    loadDinamicRisk();

    $("#camposContacto legend input, #camposAcudiente legend input").change(function() {
        var $fieldset = $(this).parent().parent();
        $fieldset.find("input[name='chk[]']").prop('checked', $(this).prop("checked"));
        console.log($(this).prop("checked"));
    });

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
        }
        else {
            accordionToggleIcon.html("<i class='glyphicon glyphicon-chevron-left'></i>");
        }

        // toggle accordion content
        accordionContent.slideToggle(250);

    });



});

//para que los div toggle se contraigan con un clic
$(document).on('click', 'panel-heading', function(e) {
    $(".panel-collapse.in").removeClass("in").addClass("collapse");
});

/**
 * Carga dinamicamente los checkbos de riesgos a listar
 * @author Edgar Mauricio Ceron Florez
 */
function loadDinamicRisk() {
    $.ajax({
        url: "../managers/get_riesgo_dinamico.php",
        type: "POST",
        dataType: "html",
        cache: "false",
        success: function(html) {
            document.getElementById("div_riesgo").innerHTML = html;
        }
    });
}


function loadDinamicSelect() {
    $.ajax({
        type: "POST",
        data: {
            cohorte: "cohorte",
            idinstancia: getIdinstancia()
        },
        url: "../managers/for_index.php",
        success: function(msg) {
            var cohorts = msg.cohorts;
            var enfasis = msg.enfasis;

            //se cargan las cohortes
            for (cohort in cohorts) {
                var html = "<option value=\"" + cohorts[cohort].idnumber + "\">" + cohorts[cohort].name + "</option>";
                $('#cohorte').append(html);
            }

            //se cargan los enfasis
            for (enf in enfasis) {
                var html = "<option value=\"" + enfasis[enf].nombre + "\">" + enfasis[enf].nombre + "</option>";
                $('#enfasis').append(html);
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    });
}


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

                        //     var nombre = msg.firstname;
                        //     var apellido = msg.lastname;
                        //     var num_doc = msg.num_doc;
                        //     var tipo_doc = msg.tipo_doc;
                        //     var email = msg.email;
                        //     var grupo = msg.grupo;
                        //     var cohorte = msg.namech;
                        //     var dir1 = msg.direccion_res;
                        //     var tel1 = msg.tel_ini;
                        //     var tel2 = msg.tel_res;
                        //     var tel3 = msg.celular;
                        //     var emailpilos = msg.emailpilos;
                        //     var nom_acu = msg.acudiente;
                        //     var tel_acu =msg.tel_acudiente;
                        //     var observacion = msg.observacion;
                        //     var estado = msg.estado;
                        //     var username = msg.username;
                        //     var userid = msg.id_user;
                        //     var nom_enfasis = msg.nom_enfasis;
                        //     var cod_programa = msg.cod_programa;
                        //     var nom_programa = msg.nom_programa;
                        //     var age = msg.age;
                        //     var idtalentos = msg.idtalentos;
                        //     var status_ases = msg.estado_ases;

                        //     //se actualizan los valores
                        //     $("#ficha_estudiante #codigo").val(username);
                        //     $('#ficha_estudiante #nombreficha').text(nombre+" "+apellido);
                        //     $('#ficha_estudiante #cedula').val(num_doc);
                        //     $('#ficha_estudiante #tipo_doc').val(tipo_doc);
                        //     $('#ficha_estudiante #email').text(email);
                        //     $('#ficha_estudiante #grupo').val(grupo);
                        //     $('#ficha_estudiante #fcohorte').text(cohorte);
                        //     $('#ficha_estudiante #estado').val(estado);
                        //     $('#ficha_estudiante #estadoAses').val(status_ases);
                        //     $('#ficha_estudiante #dir1').val(dir1);
                        //     $('#ficha_estudiante #tel1').val(tel1);
                        //     $('#ficha_estudiante #tel2').val(tel2);
                        //     $('#ficha_estudiante #ficha_estudiante #tel3').val(tel3);
                        //     $('#ficha_estudiante #email2').val(emailpilos);
                        //     $('#ficha_estudiante #nombre_acudiente').val(nom_acu);
                        //     $('#ficha_estudiante #tel4').val(tel_acu);
                        //     $('#ficha_estudiante #Observaciones').val(observacion);
                        //     $('#ficha_estudiante #ficha_estudiante #f_enfasis').text(nom_enfasis);
                        //     $('#ficha_estudiante #programa').text(cod_programa + " " + nom_programa);
                        //     $('#ficha_estudiante #age').text(age);
                        //     $('#ficha_estudiante #idtalentos').val(idtalentos);
                        //     $('#ficha_estudiante #iduser').val(userid);

                        //     checkAsesStatus();
                        //     //se actualiza la foto
                        //     var img = $('#photo').attr('src');
                        //     var token = img.split('/');
                        //     var newimg = token[0]+"/"+token[1]+"/"+token[2]+"/"+token[3]+"/"+token[4]+"/"+userid+"/f2.jpg";
                        //     $('#photo').attr('src',newimg);
                        //     setTimeout(
                        //         function() 
                        //         {
                        //             newimg = token[0]+"/"+token[1]+"/"+token[2]+"/"+token[3]+"/"+token[4]+"/"+userid+"/f1.jpg";
                        //             $('#photo').attr('src',newimg);
                        //          }, 1000);

                        //         $.getScript("../js/checkrole.js", function(){
                        //             verificarPermisosFicha(parameters);
                        //         });

                        //     //se actualiza la url location.href = ismonitor.pagina;
                        //     var search = location.search.split('&');

                        //     window.history.pushState(null, null, "talentos_profile.php"+search[0]+"&"+search[1]+"&talento_id="+$("#codigo").val());


                        //     //se actualiza socioeducativo
                        //     loadAll_trackPeer();
                        // loadAll_segGroup();
                        // loadAll_primerAcerca();
                        // loadAll_AcompaSocio();
                        // loadAll_SegSocio();

                        // //se actualiza asistencia
                        // executeAttendance();

                        //     //se acualiza ficha academica
                        //     create_semesters_panel(userid);



                        //se habilita el metodo de psicocial encargado del antecedente
                        // $('#AcompaSocio').on('click','.antecedentes', function() {

                        //     loadeventAcompasocio($(this));
                        // });

                    }
                    else {
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

            }
            else {
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
            alert("Error " + msg)
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
}

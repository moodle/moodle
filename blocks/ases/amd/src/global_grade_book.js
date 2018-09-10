/**
* Grade book management
* @module amd/src/global_grade_book
* @author Camilo José Cruz rivera
* @copyright 2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co> 
* @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui'], function ($, bootstrap, sweetalert, jqueryui) {

    return {

        init: function () {
            var grade;

            $(document).ready(function () {
                var pilos = getIDs();
                deleteNoPilos(pilos);
                bloquearTotales();
                if ($('.gradingerror').length != 0) {
                    //Redirect to a new page given the course id 
                    var new_page = location.origin + "/moodle/grade/report/grader/index.php?id=" + getCourseid();
                    swal({
                        title: "Recalculando ítems.",
                        text: "Debido al proceso de actualización del campus virtual se debe realizar este paso.\nGracias por su paciencia \nDe seguir presentando este problema, por favor dirigirse al area de sistemas Ases",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Continuar",
                        closeOnConfirm: false
                    },
                        function () {
                            update_grade_items(getCourseid());
                            location.reload();
                        });
                }
            });



            $(document).on('blur', '.text', function () {
                if (validateNota($(this))) {
                    var id = $(this).attr('id').split("_");
                    var userid = id[1];
                    var itemid = id[2];
                    var nota = $(this).val();
                    var curso = getCourseid();
                    var data = { user: userid, item: itemid, finalgrade: nota, course: curso };
                    $.ajax({
                        type: "POST",
                        data: data,
                        url: "../managers/grade_categories/grader_processing.php",
                        async: false,
                        success: function (msg) {

                            var updGrade = msg.nota;

                            if (updGrade == true) {
                                console.log("Nota actualizada");

                                if (nota < 3) {
                                    var menMonitor = msg.monitor;
                                    var menPracticante = msg.practicante;
                                    var menProfesional = msg.profesional;

                                    if (menMonitor == true) {
                                        console.log("mensaje al monitor enviado correctamente");
                                    } else {
                                        console.log("error monitor");
                                        swal('Error',
                                            'Error al enviar correo al monitor',
                                            'error');
                                    }

                                    if (menPracticante == true) {
                                        console.log("mensaje al practicante enviado correctamente");
                                    } else {
                                        console.log("error practicante");
                                        swal('Error',
                                            'Error al enviar correo al practicante',
                                            'error');
                                    }

                                    if (menProfesional == true) {
                                        console.log("mensaje al profesional enviado correctamente");
                                    } else {
                                        console.log("error profesional");
                                        swal('Error',
                                            'Error al enviar correo al profesional',
                                            'error');
                                    }
                                }
                            } else {
                                swal('Error',
                                    'Error al actualizar la nota',
                                    'error');
                            }

                        },
                        dataType: "json",
                        cache: "false",
                        error: function (msg) {
                             console.log(msg); 
                        }
                    });
                }
            });

            $(document).on('focus', '.text', function () {
                grade = $(this).val();
                //console.log(grade);
            });

            $(document).on('keypress', '.text', function (e) {

                var tecla = (document.all) ? e.keyCode : e.which;

                //backspace to delete and (.)  always allows it
                if (tecla == 8 || tecla == 46) {
                    return true;
                }
                // entry pattern, just accept numbers
                patron = /[0-9]/;
                tecla_final = String.fromCharCode(tecla);
                return patron.test(tecla_final);
            });


            $(document).on('click', '.reload', function () {

                location.reload();
            });

            /**
             * @method update_grade_items 
             * @desc uptade the items which needsupdate from a course
             * @param {integer} course_id
             * @return {boolean} 
             */
            function update_grade_items(course_id) {

                var curso = course_id;
                var data = { type: 'update_grade_items', course: curso };

                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/grade_categories/grader_processing.php",
                    async: false,
                    success: function (msg) {
                        if (msg == '1') {
                            console.log('update ok');
                        } else {
                            console.log('update fail');
                        }
                    },
                    dataType: "text",
                    cache: "false",
                    error: function (msg) { console.log(msg); },
                });
            }
            /**
             * @method validateNota
             * @desc Verifies if a grade is correct value (not empty or within a range)
             * @param {DOM element} selector represents all inputs where every grade is registered
             * @return {boolean} Return false in case there was any mistake or error, true if the grade is correct or there isn't a selected grade
             */
            function validateNota(selector) {
                var bool = false;
                var nota = selector.val();

                if (nota > 5 || nota < 0) {
                    swal({
                        title: "Ingrese un valor valido, entre 0 y 5. \n\rUsted ingresó: " + nota,
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
                    selector.val(grade);
                    bool = false;
                } else if (nota == '' && grade != '') {
                    selector.val('0');
                    bool = true;
                } else if (nota == '' && grade == '' || nota == grade) {
                    bool = false;
                } else {
                    bool = true;
                }



                return bool;
            }

            /**
             * @method bloquearTotales
             * @desc disable some fields on front page and changes CSS (font weight and size)
             * @return {void}
             */
            function bloquearTotales() {
                $("#user-grades a").removeAttr("href");

                $('.cat').each(function () {
                    var input = $(this).children().next('.text');
                    input.attr('disabled', true);
                    input.css('font-weight', 'bold');
                });

                $('.course').each(function () {
                    var input = $(this).children().next('.text');
                    input.attr('disabled', true);
                    input.css('font-weight', 'bold');
                    input.css('font-size', 16);
                });

                $('.header').children().each(function () {
                    $(this).removeAttr('href');
                });
            }


            /**
             * @method deleteNoPilos
             * @desc Removes every student who's not 'pilo'. IF the student is 'pilo' remove href attribute (link to other page)
             * @param {array} pilos 'pilos' students to filtrate with the entry from DOM
             * @return {void}
             */
            function deleteNoPilos(pilos) {
                $("#user-grades").children().children().each(function () {
                    if ($(this).attr('data-uid') != undefined) {
                        if (!isPilo($(this).attr('data-uid'), pilos)) {
                            $(this).remove();
                        } else {
                            $(this).children('th').children().each(function () {
                                $(this).removeAttr('href');
                                $(this).click(function () {
                                    var id = $(this).parent().parent().attr('data-uid');
                                    var code = $('#idmoodle_' + id).attr('data-code');
                                    var pagina = "student_profile.php";
                                    var url = pagina + location.search + "&student_code=" + code;
                                    //window.open(url, '_blank');
                                });
                            });
                        }
                    }
                });
            }

            /**
             * @method isPilo
             * @desc verifies if a student id is in an array of 'pilos'
             * @param {integer} id 
             * @param {array} pilos 
             * @return {boolean} True if the student is 'pilo', false otherwise
             */
            function isPilo(id, pilos) {
                for (var i = 0; i < pilos.length; i++) {
                    if (pilos[i].split("_")[1] === id) {
                        return true;
                    }
                }
                return false;
            }


            /**
             * @method getIDs
             * @desc Returns an array of ids, belonging to students 'pilos'
             * @return {array} array of students id
             */
            function getIDs() {
                var pilos = new Array;
                $("#students-pilos").children().each(function () {
                    pilos.push($(this).attr("id"));
                });
                return pilos;
            }

            /**
             * @method getCourseid
             * @description Obtains the course id present on the url
             * @return {integer} course id
             */
            function getCourseid() {
                var informacionUrl = window.location.search.split("&");
                for (var i = 0; i < informacionUrl.length; i++) {
                    var elemento = informacionUrl[i].split("=");
                    if (elemento[0] == "?id_course" || elemento[0] == "id_course") {
                        var curso = elemento[1];
                        return curso;
                    }
                }
            }
        }
    };
});


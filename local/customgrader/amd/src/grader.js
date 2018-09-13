/**
* Grade book management
* @module local_customgrader/grader
* @author Camilo José Cruz rivera
* @copyright 2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co> 
* @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
define(['jquery', 'local_customgrader/bootstrap', 'local_customgrader/sweetalert', 'local_customgrader/jqueryui'], function ($, bootstrap, sweetalert, jqueryui) {

    return {
 
        init: function () {
            var grade;

            $(document).ready(function () {
                ////////////////////////////////////////////////////////////////////////////////////////////
                ////SOLO RAMA UNIVALLE
                var ases = getIDs();
                marckAses(ases);
                ////////////////////////////////////////////////////////////////////////////////////////////

                bloquearTotales();
                if ($('.gradingerror').length != 0) {
                    //if gradingerror, update items that needsupdate 
                    swal({
                        title: "Recalculando ítems.",
                        text: "Debido al proceso de actualización de moodle se debe realizar este paso.\nGracias por su paciencia \nDe seguir presentando este problema, por favor dirigirse a la configuración de calificaciones de moodle",
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
                        url: "managers/ajax_processing.php",
                        async: false,
                        success: function (msg) {

                            var updGrade = msg.nota;

                            if (updGrade == true) {
                                console.log("Nota actualizada");
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
                var patron = /[0-9]/;
                var tecla_final = String.fromCharCode(tecla);
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
                    url: "managers/ajax_processing.php",
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

                $('.topleft').attr('colspan', '3');

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

            }

            ////////////////////////////////////////////////////////////////////////////////////////////
            ////SOLO RAMA UNIVALLE
            /**
             * @method marckAses
             * @desc Removes every student who's not 'pilo'. IF the student is 'pilo' remove href attribute (link to other page)
             * @param {array} ases 'ases' students to filtrate with the entry from DOM
             * @return {void}
             */
            function marckAses(ases) {
                $("#user-grades").children().children().each(function () {
                    if ($(this).attr('data-uid') != undefined) {
                        if (isAses($(this).attr('data-uid'), ases)) {
                            $(this).attr('class' , 'ases');
                        }
                    }
                });
            }


            /**
             * @method isAses
             * @desc verifies if a student id is in an array of 'ases'
             * @param {integer} id 
             * @param {array} ases 
             * @return {boolean} True if the student is 'ases', false otherwise
             */
            function isAses(id, ases) {
                for (var i = 0; i < ases.length; i++) {
                    if (ases[i].split("_")[1] === id) {
                        return true;
                    }
                }
                return false;
            }


            /**
             * @method getIDs
             * @desc Returns an array of ids, belonging to students 'ases'
             * @return {array} array of students id
             */
            function getIDs() {
                var ases = new Array;
                $("#students-ases").children().each(function () {
                    ases.push($(this).attr("id"));
                });
                return ases;
            }
            ////////////////////////////////////////////////////////////////////////////////////////////

            /**
             * @method getCourseid
             * @description Obtains the course id present on the url
             * @return {integer} course id
             */
            function getCourseid() {
                var informacionUrl = window.location.search.split("&");
                for (var i = 0; i < informacionUrl.length; i++) {
                    var elemento = informacionUrl[i].split("=");
                    if (elemento[0] == "?id" || elemento[0] == "id") {
                        var curso = elemento[1];
                        return curso;
                    }
                }
            }
        }
    };
});


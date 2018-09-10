 /**
 * Management - grade book, disabling fields (not editing).
 * @module amd/src/report_grade_book_main
 * @author Juan Pablo Moreno Muñoz
 * @copyright 2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui'], function ($, bootstrap, sweetalert, jqueryui) {

    return {

        init: function () {
            var grade;

            $(document).ready(function () {
                var pilos = getIDs();
                $(".text").prop('disabled', true);
                deleteNoPilos(pilos);
                bloquearTotales();
                if ($('.gradingerror').length != 0) {
                    //Redirect to a new page given the course id 
                    new_page = location.origin + "/moodle/grade/report/grader/index.php?id=" + getCourseid();
                    swal({
                        title: "Redireccionando página.",
                        text: "Debido al proceso de actualización del campus virtual se debe realizar este paso.\nSOLO EL DOCENTE ENCARGADO DEL CURSO PUEDE \n Una vez realizado por favor cerrar la ventana y volver a seleccionar su curso en el listado",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Continuar",
                        closeOnConfirm: false
                    },
                        function () {
                            location.href = new_page;
                        });
                }
            });

            $(document).on('focus', '.text', function () {
                grade = $(this).val();
                //console.log(grade);
            });

            $(document).on('click', '.reload', function () {

                location.reload();
            });
            
            /**
             * @method bloquearTotales
             * @desc Changes css style, font size and remove the href attribute (link)
             * @return {void}
             */
            function bloquearTotales() {
                $('.cat').each(function () {
                    var input = $(this).children().next('.text');
                    //input.attr('disabled', true);
                    input.css('font-weight', 'bold');
                });
                $("[name^='grade']").prop('disabled', true);
                $("a").removeAttr("href");

                $('.course').each(function () {
                    var input = $(this).children().next('.text');
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
             * @param {array} pilos students with cohort 'pilo'
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
             * @desc Verifies if an id is in a given array
             * @param {id} id id to verify in an array
             * @param {array} pilos students with category 'pilo'
             * @return {boolean} true if the given id is in the array, false otherwise
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
             * @desc gets all students id of element with attribute students-pilos from DOM
             * @return {array} students 'pilos' ids
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
             * @desc Obtains the course id present on the url
             * @return {}
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


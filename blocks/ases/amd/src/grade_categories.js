 /**
 * Grade categories management
 * @module amd/src/grade_categories
 * @author Camilo José Cruz rivera
 * @copyright 2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co> 
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define(['jquery', 'block_ases/bootstrap', 'block_ases/jquery.dataTables', 'block_ases/sweetalert', 'block_ases/jqueryui'], function ($, bootstrap, datatablesnet) {

    return {

        init: function () {

            $(document).ready(function () {
                $("#teachers").DataTable(); //Obtains teachers table 
            });

            $(document).on('click', '.desplegate', function () {
                var parent = $(this).parent().parent();
                var id = parent.attr('id').split("_")[1];
                var curso = '#curso_' + id;
                var profe = '#profe_' + id;

                //Changes the arrow symbol when something is deploying
                if (parent.hasClass('cerrado')) {
                    $(this).children().removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-down');
                    $(curso).appendTo(profe);
                }
                else {
                    $(this).children().removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-left');
                    $(curso).appendTo('#courses_info');
                }
                parent.toggleClass('cerrado');

            });

            //Redirects to a global_grade_book new tab when 'ir_curso' is clicked
            $(document).on('click', '.ir_curso', function () {
                var id_curso = $(this).attr('id');
                var url = 'global_grade_book.php' + location.search + '&id_course=' + id_curso;
                window.open(url, '_blank');
            });


        }
    }
});

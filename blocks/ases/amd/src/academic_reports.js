/**
 * Academic report management
 * @module amd/src/academic_reports
 * @author Camilo José Cruz rivera
 * @copyright 2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/jquery.dataTables', 'block_ases/sweetalert2'], function($, bootstrap, datatables, swal){

  return{
    /**
     *
     */
    init: function() {

      $(document).ready(function(){
        //Gets students and courses data tables
        $("#students").DataTable();
        $("#courses").DataTable();
      });

      $(document).on('click', '#students tbody tr td', function(){
        // Assign to variables a web page, table (students), column index selected,
        // student code (obtained by table), username (student id)
        // var pagina = "student_profile.php";
        var table = $("#students").DataTable();
        var colIndex = table.cell(this).index().column;
        // var student_code = table.cell(table.row(this).index(), 0).data();
        var username = $(this).attr('id');
        // if (colIndex <= 2) {
        //     $("#formulario").each(function() {
        //         this.reset;
        //     });
        //     document.getElementById("formulario").reset();
        //     location.href = pagina + location.search + "&student_code=" + student_code;
        // }COMENTADO MIENTRAS ESTA LA FICHA ACADEMICA

        if(colIndex == 3){

          $.ajax({
            type: "POST",
            data: {
              student: username,
              type: "load_loses"
            },
            //Calls processing academic_reports_processing to execute ajax
            url: "../managers/academic_reports/academic_reports_processing.php",
            success: function(msg){
              //Display all lose subjects
              swal({
                title: "Notas Perdidas",
                type: "info",
                text: msg,
                showCancelButton: false,
                customClass: 'notas_perdidas',
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Cerrar",
                closeOnConfirm: true });
            },
            dataType: "text",
            cache: "false",
            error: function(msg){
              console.log(msg);
            },
          });
        }
      });

      //Opens a new tab to display a course report, given the course id and the location
      $(document).on('click', '.curso_reporte', function() {
        var course_id = $(this).attr('id');
        var url = 'report_grade_book.php' + location.search + '&id_course=' + course_id;
        window.open(url, '_blank');
      });
    }
  };
});
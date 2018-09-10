 /**
 * Management - View reports
 * @module amd/src/icetex_reports_main
 * @author Juan Pablo Moreno Muñoz
 * @copyright 2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'block_ases/jszip',
        'block_ases/pdfmake',
        'block_ases/jquery.dataTables',
        'block_ases/dataTables.autoFill',
        'block_ases/dataTables.buttons',
        'block_ases/buttons.html5',
        'block_ases/buttons.flash',
        'block_ases/buttons.print',
        'block_ases/bootstrap',
        'block_ases/sweetalert'
        ],
        function($, jszip, pdfmake, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert) {

	return {

		init: function() {
			
			window.JSZip = jszip;

			$("#list-resolution-students-panel").on('click', function(){
				load_report_students_resolution();
				/*				
				setTimeout(function(){
					var table = $("#tableResStudents").DataTable();
					var col_array = table.columns(7).data().eq(0);;
					string_to_integer(col_array);
					var total = col_array.reduce(numSum);
					$("#table_foot").append(total);				
				}, 500);
				*/
			});

			$("#list-resolutions-panel").on('click', function(){
				load_resolutions();								
			});

			$("#report_button").on('click', function() {
				var cohort = $("#cohort_select select").val();
				load_summary_report(cohort);				
			});

			//Controles para la tabla de los estudianes con resolución
			$(document).on('change', '#tableResStudents thead tr th select', function () {
				var table = $("#tableResStudents").DataTable();
		
				var colIndex = $(this).parent().index()+1;
				var selectedText=$(this).parent().find(":selected").text();
				table.columns( colIndex-1 ).search( this.value ).draw();		
			});

			//Controles para la tabla de resoluciones
			$(document).on('change', '#tableResolutions thead tr th select', function () {
				var table = $("#tableResolutions").DataTable();
		
				var colIndex = $(this).parent().index()+1;
				var selectedText=$(this).parent().find(":selected").text();
				table.columns( colIndex-1 ).search( this.value ).draw();		
			});

	/**
	 * @method load_report_students_resolution
	 * @desc Loads the report of a student with resolution on a table. Current processing on icetex_reports_processing.php
	 * @return {void}
	 */
	function load_report_students_resolution(){
		$.ajax({
			type: "POST",
			data: {loadR: 'loadReport'},
			url: "../managers/historic_icetex_reports/icetex_reports_processing.php",
			success: function(msg){
				$("#div_res_students").empty();
				$("#div_res_students").append('<table id="tableResStudents" class="display" cellspacing="0" width="100%"><thead><thead></table>');
				var table = $("#tableResStudents").DataTable(msg);
				$('#div_res_students').css('cursor', 'pointer');
			},
			dataType: "json",
			cache: false,
			error: function(msg){
				swal("Error", "Error al cargar el reporte", "error");
			}
		});
	}

	/**
	 * @method load_resolutions
	 * @desc Loads the report of all resolutions on a table. Current processing on resolution_reports_processing.php
	 * @return {void}
	 */
	function load_resolutions(){
		$.ajax({
			type: "POST",
			data: {resR: 'resReport'},
			url: "../managers/historic_icetex_reports/resolution_reports_processing.php",
			success: function(msg){
				$("#div_resolutions").empty();
				$("#div_resolutions").append('<table id="tableResolutions" class="display" cellspacing="0" width="100%"><thead><thead></table>');
				var table = $("#tableResolutions").DataTable(msg);
				$('#div_resolutions').css('cursor', 'pointer');			
			},
			dataType: "json",
			cache: false,
			error: function(msg){
				swal("Error", "Error al cargar el reporte", "error");
			}
		});

	}

	function load_summary_report(cohort_name){
		$.ajax({
			type: "POST",
			data: {summ: 'summaryR', cohor: cohort_name},
			url: "../managers/historic_icetex_reports/summary_report_processing.php",
			success: function(msg){
				$("#div_report_summary").empty();
				$("#div_report_summary").append('<table id="tableSummary" class="display" cellspacing="0" width="100%"><thead><thead></table>');
				var table = $("#tableSummary").DataTable(msg);
				$('#div_report_summary').css('cursor', 'pointer');			
			},
			dataType: "json",
			cache: false,
			error: function(msg){
				swal("Error", "Error al cargar el reporte", "error");
			}
		});
		
	}
	
}

};

});
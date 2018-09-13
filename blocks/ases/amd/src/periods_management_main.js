 /**
 * Management - Create, update and load periods
 * @module amd/src/periods_management_main
 * @author Juan Pablo Moreno Muñoz
 * @copyright 2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/jquery.dataTables','block_ases/sweetalert','block_ases/select2', 'block_ases/jqueryui'], function($, bootstrap, datatables, sweetalert, select2, jqueryui) {

	return {

		init: function() {

			$("#periods").select2({
			language: {

				noResults: function(){
				return 'No se encontraron resultados';
				},

				searching: function(){
				return 'Buscando...';
				}
			},
			dropDownAutoWidth: true,

			});


			$(document).ready(function() {
				$(".assignment_li").css({ display: 'none' });
				$(".period_date").datepicker({dateFormat: "yy-mm-dd"});

				$("#search_button").on('click', function() {
				$("#search_button").prop("disabled", true);
				searchPeriod();
				$('#periods').prop('disabled', true);
				$(".assignment_li").slideToggle("fast");
			});

			$("#ok-button").on('click', function(){		
				updatePeriod();
				$(".assignment_li").addClass('hidden');
				$("#periods").val('').trigger('change.select2');
				$("#periods").prop("disabled", false);
				$("#search_button").prop("disabled", false);
				$("#semester_name").val(" ");
				$("#beginning_date").val(" ");
				$("#ending_date").val(" ");		

			});

			$("#cancel-button").on('click', function() {		
				$(".assignment_li").addClass('hidden');
				$("#periods").val('').trigger('change.select2');
				$("#periods").prop("disabled", false);
				$("#search_button").prop("disabled", false);
				$("#semester_name").val(" ");
				$("#beginning_date").val(" ");
				$("#ending_date").val(" ");		
			});

			$("#save-button").on('click', function() {
				createPeriod();		
				$("#new_semester_name").val(" ");
				$("#new_beginning_date").val(" ");
				$("#new_ending_date").val(" ");
			});

			$("#cancel-save-button").on('click', function(){				
				$("#new_semester_name").val(" ");
				$("#new_beginning_date").val(" ");
				$("#new_ending_date").val(" ");
			});

			$("#list-periods-panel").on('click', function(){
				loadPeriods();
			});


			});


	/**
	 * @method searchPeriod
	 * @desc Search a period information given a semester. Current processing on search_period_processing.php
	 * @param {object} semester We'd like to get certain information
	 * @param {function} callback 
	 * @return {void}
	 */
	function searchPeriod(semester, callback){
		var dataString = semester;
		if(!dataString){
			dataString = $("#periods").val();
		}

		$.ajax({
			type: "POST",
			data: {dat: dataString},
			url: "../managers/periods_management/search_period_processing.php",
			success: function(msg){

				if(callback){
					callback(msg);
				}else{
					if(!msg.error){

						if(msg.nombre == ""){
							swal("Error", "El semestre no existe en la base de datos", "error");
							$('#periods').prop('disabled', false);
						}else{
							$('.assignment_li').removeClass('hidden');
							$('#semester_name').val(msg.nombre);
							$('#beginning_date').val(msg.fecha_inicio);
							$('#ending_date').val(msg.fecha_fin);
						}
					}else{
						swal("Error", msg.error, "error");
						$(".assignment_li").addClass('hidden');
						$("#periods").val('').trigger('change.select2');
						$("#periods").prop('disabled', false);
						$("#semester_name").val("");
						$("#search_button").prop("disabled", false);
					}
				}
			},

			dataType: "json",
			error: function(msg){
				swal("Error", "Hay un error con la conexión", "error");
				$(".assignment_li").addClass('hidden');
				$("#periods").val('').trigger('change.select2');
				$("#periods").prop('disabled', false);
				$("#semester_name").val(" ");
				$("#search_button").prop("disabled", false);
			}
		});

	}

	/**
	 * @method updatePeriod
	 * @desc Updates a period values (id, name, beginning date and ending date). Current processing on update_period_processing.php
	 * @return {void}
	 */
	function updatePeriod(){

		var semesterId = $("#periods").val();
		var semesterName = $("#semester_name").val();
		var beginningDate = $("#beginning_date").val();
		var endingDate = $("#ending_date").val();

		var result_validation = validateFieldsUpdate(semesterId, semesterName, beginningDate, endingDate);

		if(result_validation != "success"){
			swal({
				title: "Advertencia",
				text: result_validation,
				type: "warning",
				html: true
			});
		}else{		

			$.ajax({
				type: "POST",
				data: {id: semesterId, name: semesterName, beginning: beginningDate, ending: endingDate},
				url: "../managers/periods_management/update_period_processing.php",
				success: function(msg){

					swal({
						title: "Información",
						text: msg,
						type: "info",
						html: true,
						confirmButtonColor: "#d51b23",
						confirmButtonText: "OK",
						closeOnConfirm: true
					});			
				},
				dataType: "text",
				cache: "false",
				error: function(msg){
					swal("Error", "Ha ocurrido un error", "error")
				}
			});

		}

	}

	/**
	 * @method loadPeriods
	 * @desc Loads a period values on a table. Current processing on load_periods_processing.php
	 * @return {void}
	 */
	function loadPeriods(){
		$.ajax({
			type: "POST",
			data: {load: 'loadSemester'},
			url: "../managers/periods_management/load_periods_processing.php",
			success: function(msg){
				$("#div_periods").empty();
				$("#div_periods").append('<table id="tablePeriods" class="display" cellspacing="0" width="100%"><thead><thead></table>');
				var table = $("#tablePeriods").DataTable(msg);
				$('#div_periods').css('cursor', 'pointer');
			},
			dataType: "json",
			cache: false,
			error: function(msg){
				alert("Error al cargar períodos")
			}
		});

	}


	/**
	 * @method createPeriod
	 * @desc Creates a period obtaining values from html. Current processing on load_periods_processing.php
	 * @return {void}
	 */
	function createPeriod(){
		var newSemesterName = $("#new_semester_name").val();
		var newBeginningDate = $("#new_beginning_date").val();
		var newEndingDate = $("#new_ending_date").val();

		//Validates if every field is correct
		var result_validation = validateFieldsCreate(newSemesterName, newBeginningDate, newEndingDate);

		//If error
		if(result_validation != "success"){
			swal({
				title: "Advertencia",
				text: result_validation,
				type: "warning",
				html: true
			});

		//Refresh with ajax 
		}else{
			$.ajax({
				type: "POST",
				data: {op: 'createSemester' , name: newSemesterName, beginning: newBeginningDate, ending: newEndingDate},
				url: "../managers/periods_management/create_period_processing.php",
				success: function(msg){

					swal({
						title: "Información",
						text: msg,
						type: "info",
						html: true,
						confirmButtonColor: "#d51b23",
						confirmButtonText: "OK",
						closeOnConfirm: true
					});			
				},
				dataType: "text",
				cache: "false",
				error: function(msg){
					swal("Error", "Ha ocurrido un error", "error")
				}			

			});
		}
	}


	/**
	 * @method validateFieldsUpdate
	 * @desc Validates if each field to update is correct (not empty field, date format correct)
	 * @param {id} semesterId semester id
	 * @param {string} semesterName semester name
	 * @param {date} beginningDate Beginning period date
	 * @param {date} endingDate Ending period date
	 */
	function validateFieldsUpdate(semesterId, semesterName, beginningDate, endingDate){

		var regexp = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;

		var validate_begin_date = regexp.exec(beginningDate);
		var validate_end_date = regexp.exec(endingDate);

		if(semesterName == "" && beginningDate == "" && endingDate == ""){
			return "Debe llenar todos los campos";
		}
		else if(beginningDate == "" || endingDate == ""){
			return "Debe introducir la fecha de inicio y fin del período";
		}
		else if(validate_begin_date === null){
			return "La fecha de inicio no sigue el patrón yyyy-mm-dd. Ejemplo: 2017-10-20";
		}
		else if(validate_end_date === null){
			return "La fecha de fin no sigue el patrón yyyy-mm-dd. Ejemplo: 2017-10-20";
		}
		else if(beginningDate == endingDate){
			return "La fecha de inicio y de fin deben ser diferentes";
		}
		else if(semesterId == ""){
			return "Se encontró un problema con el identificador del semestre, vuelva a seleccionar el semestre";
		}
		else if(semesterName == ""){
			return "Debe ingresar el nombre del semestre";
		}
		else{
			return "success";
		}

	}
	
	/**
	 * @method validateFieldsCreate
	 * @desc Validates if each field to create is correct (not empty field, date format correct)
	 * @param {string} semesterName Semester name
	 * @param {date} beginningDate Beginning period date
	 * @param {date} endingDate Ending period date
	 */
	function validateFieldsCreate(semesterName, beginningDate, endingDate){

		var regexp = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;

		var validate_begin_date = regexp.exec(beginningDate);
		var validate_end_date = regexp.exec(endingDate);

		if(semesterName == "" && beginningDate == "" && endingDate == ""){
			return "Debe llenar todos los campos";
		}
		else if(beginningDate == "" || endingDate == ""){
			return "Debe introducir la fecha de inicio y fin del período";
		}
		else if(validate_begin_date === null){
			return "La fecha de inicio no sigue el patrón yyyy-mm-dd. Ejemplo: 2017-10-20";
		}
		else if(validate_end_date === null){
			return "La fecha de fin no sigue el patrón yyyy-mm-dd. Ejemplo: 2017-10-20";
		}
		else if(beginningDate == endingDate){
			return "La fecha de inicio y de fin deben ser diferentes";
		}
		else if(semesterName == ""){
			return "Debe ingresar el nombre del semestre";
		}
		else{
			return "success";
		}

	}

}

};

});
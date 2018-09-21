<?php
	/**
	 *
	 * @author Hernán Darío Arango C. <hernan.arango@yahoo.com>
	 **/
	require('../../config.php');

	global $USER;

	require_login();
	//creamos los encabezados de la tabla
    $table = new html_table();
    $table->classes = array('logtable','generaltable');
    $table->head = array(
        "Codigo",
        "Nombre Curso",
        "Cedula",
        "Nombre",
        "Fecha de Creación",
        //"Tamaño",
        "Opción"

    );
    $table->data = array();
	$anio=date('Y');
	//$anio='2020';
	$mesydia=date('m-d');
	//mostramos los cursos menores a un año del año actual
	$anio=$anio-1;
	$fecha=$anio."-".$mesydia;
	$aniotimestamp=strtotime($fecha);
	$sql="SELECT
		  mdl_course.id,
		  mdl_course.shortname as codigocurso,
		  mdl_course.fullname,
		  mdl_user.username as cedula,
		  (mdl_user.firstname || ' ' || mdl_user.lastname) as nombreprofesor,
		  to_timestamp(mdl_course.timecreated) as fechacreacion
		FROM
		  public.mdl_user,
		  public.mdl_role_assignments,
		  public.mdl_context,
		  public.mdl_course
		WHERE
		  mdl_role_assignments.userid = mdl_user.id AND
		  mdl_role_assignments.contextid = mdl_context.id AND
		  mdl_context.instanceid = mdl_course.id And mdl_user.id='$USER->id' AND mdl_role_assignments.roleid='3' and mdl_course.timecreated<='$aniotimestamp' and mdl_course.id!='1'";

	/*$sql="select mdl_course.id,
		 mdl_course.shortname as codigocurso,
		 mdl_course.fullname,
		 mdl_user.username as cedula,
		 (mdl_user.firstname || ' ' || mdl_user.lastname) as nombreprofesor,
		 to_timestamp(mdl_course.timecreated) as fechacreacion,
		  total.tamaniocurso
		FROM
		  public.mdl_user
		INNER JOIN public.mdl_role_assignments
		ON mdl_user.id = mdl_role_assignments.userid

		INNER JOIN public.mdl_context
		ON mdl_role_assignments.contextid = mdl_context.id

		INNER JOIN public.mdl_course
		ON mdl_course.id = mdl_context.instanceid


		INNER JOIN (SELECT
			  mdl_course.id,
			  sum(mdl_files.filesize) as tamaniocurso

			FROM
			  public.mdl_course,
			  public.mdl_files,
			  public.mdl_context,
			  public.mdl_course_modules
			WHERE
			  mdl_course.id = mdl_course_modules.course AND
			  mdl_context.id = mdl_files.contextid AND
			  mdl_context.instanceid = mdl_course_modules.id group by mdl_course.id
			  ) as total ON mdl_course.id = total.id
		 AND mdl_user.id='$USER->id' And mdl_role_assignments.roleid='3' and mdl_course.timecreated<='$aniotimestamp' and mdl_course.id!='1'";*/

	$result=$DB->get_records_sql($sql);

	//global $CFG;
	foreach ($result as $key => $obj) {
		$row=array();
		$row[]=$obj->codigocurso;
		$row[]="<a href='$CFG->wwwroot/course/view.php?id=$obj->id'>".$obj->fullname."</a>";
		$row[]=$obj->cedula;
		$row[]=$obj->nombreprofesor;
		$row[]=$obj->fechacreacion;
		//$row[]=format_tamanio($obj->tamaniocurso)."MB";
		//$row[]="<a class='btn btn-default' href='#' onclick='eliminar($obj->id)'>Eliminar</a>";
		
		$result=$DB->get_record_sql("select id from mdl_list_courses_delete where idcourse='$obj->id'");
		
		if($result){
			$row[]="<a class='btn btn-default eliminar_modal' idcourse='$obj->id' href='#' action='delete_list'><i class='fa fa-check' aria-hidden='true'></i></a>";	
		}
		else{
			$row[]="<a class='btn btn-default eliminar_modal' idcourse='$obj->id' href='#' action='delete'><i class='fa fa-trash' aria-hidden='true'></i></a>";		
		}

		
		$table->data[] = $row;
	}

	$PAGE->set_title('Cursos Antiguos');
	$PAGE->set_heading(" ");
	$PAGE->navbar->add("Cursos Antiguos");
	echo $OUTPUT->header();
	//$PAGE->requires->js('/course/delete_course_old/css/style.css');

	echo "<div id='myModal' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	  <div class='modal-header'>
		<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
		<h3 id='myModalLabel'>¡Atención!</h3>
	  </div>
	  <div class='modal-body'>
		<div id='info-teacher-course'></div>

	  </div>
	  <div class='modal-footer'>
	<button id='eliminar' class='btn btn-primary'>Añadir</button>
	<button class='btn btn-default' data-dismiss='modal' aria-hidden='true'>Cancelar</button>

	  </div>
	</div>";
	$message_time="<div class='alert alert-warning'>
  		<h3><span style='color:#D51B23;'>Atención</span>, Solo podrá eliminar aquellos cursos que tengan mas de un año de creación.<br>
  		Si desea eliminar cursos creados hace menos de un año, por favor diligenciar el siguiente <a style='color:#D51B23;' target='blank' href='https://docs.google.com/forms/d/e/1FAIpQLScUqytuNLtZQQTYGY9KnXOzGnYFQ-gJasl1om1SbHTDJ6LQJg/viewform'>formulario</a>.
		</div>";
	echo $message_time;


	echo html_writer::table($table);

		//$PAGE->requires->js_call_amd('theme_crisp/modal','init');
		
		$PAGE->requires->js_amd_inline("
				require(['jquery'], function($) {
							


							$('.eliminar_modal').click(function(){
									id = $(this).attr('idcourse');
									boton = $(this);
									
									if($(this).attr('action') == 'delete'){



										$.get( 'count_teacher.php', { id: id }, function( data ) {
											if (data.total==1) {
												contenido='El curso será puesto en lista para ser eliminado (los cursos serán eliminados a la 01:00 am de cada día) </br>¿Esta Seguro de añadir el curso a la lista de cursos a eliminar?';
											$('#info-teacher-course').html(contenido);
											$('#myModal').modal('show');
											}
											else{
												contenido='<div class=alert alert-error>Cuidado este curso tiene otros profesores</div>';
												$('#info-teacher-course').html(contenido);
												for (var i =0; i <=data.profesores.length-1; i++) {
													$('#info-teacher-course').append('<strong>'+data.profesores[i]+'</strong><br>');

												}
												$('#info-teacher-course').append('<br>¿Esta Seguro de añadir el curso a la lista de cursos a eliminar?. Recuerde que los cursos serán eliminados a la 01:00 am de cada día.');
												$('#myModal').modal('show');

											}


										}, 'json');

										$('#eliminar').unbind().click(function () {
												
												$.post( 'add_list_course_delete.php', { id: id }, function( data ) {
													if(data='paso'){
														
														//cambiar icono
														$(boton).children('i').removeClass('fa fa-trash');
														$(boton).children('i').addClass( 'fa fa-check');
														boton.attr('action','delete_list');
													}
													else{
														alert('error');
													}

												});
												$('#myModal').modal('hide');
												
												
										})

									}
									else{
											$.post( 'add_list_course_delete.php', { id: id }, function( data ) {
												
												if(data='paso'){
													
													$('#myModal').modal('hide');
													//cambiar icono
													$(boton).children('i').removeClass('fa fa-check');
													$(boton).children('i').addClass('fa fa-trash');
													
													
													//cambiar funcion a llamar
													boton.attr('action','delete');

												}
												else{
													alert('error');
												}

											});

									}
							});


				});
		");

				


	echo $OUTPUT->footer();

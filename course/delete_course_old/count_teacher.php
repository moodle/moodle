<?php
	/**
	 * 
	 * @author Hernán Darío Arango C. <hernan.arango@yahoo.com>
	 **/
	require('../../config.php');

	$idCurso   = optional_param('id', 0, PARAM_INT);
	$sql="select 	
	public.mdl_user.firstname,
	public.mdl_user.lastname
	FROM
	public.mdl_user
	INNER JOIN public.mdl_role_assignments ON public.mdl_role_assignments.userid = public.mdl_user.id
	INNER JOIN public.mdl_context ON public.mdl_context.id = public.mdl_role_assignments.contextid
	INNER JOIN public.mdl_course ON public.mdl_context.instanceid = public.mdl_course.id
	WHERE
	public.mdl_course.id='$idCurso' and
	public.mdl_role_assignments.roleid = '3'";

	$result=$DB->get_records_sql($sql);

	$datos=array();
	$datos['total']=count($result);
	$profesores=array();
		
	foreach ($result as $key => $obj) {
		$profesores[]=$obj->firstname." ".$obj->lastname;
	}
	$datos['profesores']=$profesores;
	
	echo json_encode($datos);

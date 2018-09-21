<?php
/**
 * 
 * @author Hernán Darío Arango C. <hernan.arango@yahoo.com>
 **/
require("../../config.php");

$idCurso   = optional_param('id', 0, PARAM_INT);

$course = $DB->get_record('list_courses_delete', array('idcourse' => $idCurso), 'id');

if($course){

	$sql="DELETE FROM mdl_list_courses_delete where idcourse=?";
	$params=array($idCurso);

	if($DB->execute($sql,$params)){
		return "paso";
	}
	else{
		return "fallo";
	}
}
else{

	$sql="INSERT INTO mdl_list_courses_delete(idcourse) VALUES (?)";
	$params=array($idCurso);

	if($DB->execute($sql,$params)){
		return "paso";
	}
	else{
		return "fallo";
	}

}






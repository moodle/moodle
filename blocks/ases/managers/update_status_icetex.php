<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
global $DB;

date_default_timezone_set('America/Bogota');

$today_timestamp = time();	

$sql_query = "SELECT id FROM {talentospilos_estados_icetex} WHERE nombre = 'ACTIVO'";
$id_status = $DB->get_record_sql($sql_query)->id;

$sql_query = "SELECT id FROM {talentospilos_usuario}";
$id_students = $DB->get_records_sql($sql_query);

foreach($id_students as $student){

	$object_insert = new stdClass();
	$object_insert->id_estudiante = $student->id;
	$object_insert->id_estado_icetex = $id_status;
	$object_insert->fecha = $today_timestamp;

	$DB->insert_record('talentospilos_est_est_icetex', $object_insert);

}
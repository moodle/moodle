<?php
/**
 * Carga las funcionalidades y permisos en la base de datos en un array
 * que sera usado posteriormente para crear una interfaz con la 
 * siguiente estructura;
 * 
 * funcionalidad 1
 *      permiso 1
 *      permiso 2
 *      .
 *      .
 *      permiso n
 * 
 * funcionalidad 2
 *      permiso 1
 *      permiso 2
 *      .
 *      .
 *      permiso n
 * .
 * .
 * .
 * * funcionalidad n
 *      permiso 1
 *      permiso 2
 *      .
 *      .
 *      permiso n
 * 
 * @author Edgar Mauricio Ceron Florez
 * @return array con los los permisos y las funcionalidades
 */ 
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "SELECT id, nombre_func FROM {talentospilos_funcionalidad}";
$funcionalidades = $DB->get_records_sql($sql_query);



$sql_query = "SELECT id, permiso FROM {talentospilos_permisos}";
$permisos = $DB->get_records_sql($sql_query);
$permisos_funcionalidad = array("permisos" => $permisos, "funcionalidades" => $funcionalidades);
echo json_encode($permisos_funcionalidad);
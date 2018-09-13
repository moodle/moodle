<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol = 'sistemas'";
$id_admin = $DB->get_record_sql($sql_query)->id;

print_r($id_admin);

$sql_query = "SELECT id FROM {talentospilos_accion} WHERE nombre_accion = 'manage_action_ca'";
$id_accion = $DB->get_record_sql($sql_query)->id;

print_r($id_accion);

$sql_query = "SELECT id FROM {talentospilos_permisos_rol} WHERE id_rol = $id_admin AND id_accion = $id_accion";
$id_register = $DB->get_record_sql($sql_query)->id;

print_r($id_register);

if(!$id_register){

    $object = new stdClass();
    $object->id_rol = $id_admin;
    $object->id_accion = $id_accion;
        
    $result_insert = $DB->insert_record('talentospilos_permisos_rol', $object);

}else{
    print_r("El usuario sistemas ya tiene relacionada la acción 'manage_actions'");
}

$sql_query = "SELECT * FROM {talentospilos_accion}";
$acciones = $DB->get_records_sql($sql_query);

print_r($acciones);



<?php
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "TRUNCATE TABLE {talentospilos_funcionalidad}";
$result_truncate = $DB->execute($sql_query);
if($result_truncate){
    print_r("Truncate");
    print_r('\n');
}else{
    print_r("truncate failed");
    print_r('\n');
}

$sql_query = "TRUNCATE TABLE {talentospilos_accion}";
$result_truncate = $DB->execute($sql_query);
if($result_truncate){
    print_r("Truncate");
    print_r('\n');
}else{
    print_r("truncate failed");
    print_r('\n');
}

$sql_query = "TRUNCATE TABLE {talentospilos_permisos_rol}";
$result_truncate = $DB->execute($sql_query);
if($result_truncate){
    print_r("Truncate");
    print_r('\n');
}else{
    print_r("truncate failed");
    print_r('\n');
}

if($result_truncate){
    print_r("Truncate");
}else{
    print_r("truncate failed");
}

$object_insert = new stdClass();
$object_insert->nombre_func = "upload_files_form";
$object_insert->descripcion = "Funcionalidad que permite la carga de datos masivamente, para cada tabla del módulo";

$result_insert_func = $DB->insert_record('talentospilos_funcionalidad', $object_insert, true);

print_r("resultado func: ");
print_r($result_insert_func);
print_r('\n');

$object_insert = new stdClass();
$object_insert->nombre_accion = "upload_files_uf";
$object_insert->descripcion = "Permite cargar archivos utilizando la funcionalidad upload_files";
$object_insert->estado = 1;
$object_insert->id_funcionalidad = $result_insert_func;

$result_insert_action = $DB->insert_record('talentospilos_accion', $object_insert, true);

print_r("resultado action: ");
print_r($result_insert_action);
print_r('\n');

$sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol = 'sistemas'";
$id_sistemas = $DB->get_record_sql($sql_query)->id;

$sql_query = "SELECT id FROM {talentospilos_accion} WHERE nombre_accion = 'upload_files_uf'";
$id_accion = $DB->get_record_sql($sql_query)->id;

$object_insert = new stdClass();
$object_insert->id_rol = $id_sistemas;
$object_insert->id_accion = $id_accion;

$result_insert_permisos_rol = $DB->insert_record("talentospilos_permisos_rol", $object_insert, true);
print_r($result_insert_permisos_rol);





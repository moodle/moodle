<?php

require_once(dirname(__FILE__). '/../../../config.php');
global $DB;

//Get user with username
$sql_query = "SELECT * FROM {user} where username='sistemas1008'";
$user = $DB->get_record_sql($sql_query);

//Get instances
$sql_query = "SELECT * FROM {talentospilos_instancia}";
$instances = $DB->get_records_sql($sql_query);

//Get id from system role
$sql_query = "SELECT * FROM {talentospilos_rol} where nombre_rol='sistemas'";
$role = $DB->get_record_sql($sql_query);

//Get current semester

$sql_query = "SELECT id FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
$current_semester = $DB->get_record_sql($sql_query);

$record = new stdClass();

foreach ($instances as $instance) {
    $sql_query = "SELECT * from {talentospilos_user_rol} where id_rol ='$role->id' AND id_usuario='$user->id' AND id_semestre='$current_semester->id' AND id_instancia=$instance->id";
    
    $exist = $DB->get_record_sql($sql_query);

    if(!$exist){
    $record->id_rol = $role->id;
    $record->id_usuario = $user->id;
    $record->estado = 1;
    $record->id_semestre= $current_semester->id;
    $record->id_instancia = $instance->id_instancia;
    $DB->insert_record('talentospilos_user_rol', $record, false);
    }
}

echo "Éxito";

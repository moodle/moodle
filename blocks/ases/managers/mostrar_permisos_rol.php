<?php
/**
 * Recibe por post el identificador de un rol y devuel los permisos asociados 
 * a este en un array
 */
 
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;
if(isset($_POST['rol'])){
    $rol = $_POST['rol'];
    $sql_query = "SELECT id_permiso, id_funcionalidad FROM {talentospilos_permisos_rol} where id_rol =".$rol;
    $permisos_rol = $DB->get_records_sql($sql_query);
    echo json_encode($permisos_rol);
}

<?php
require_once dirname(__FILE__) . '/../../../config.php';

global $DB;

/* Script para cambiar el estado a inactivo de los roles de usuario cuando 
el periodo no es el actual*/

try{

$sql_query = "SELECT id , nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";

$current_semester = $DB->get_record_sql($sql_query);

$semester_id =$current_semester->id;
$sql_query = "UPDATE {talentospilos_user_rol} SET estado = 0 WHERE id_semestre<$semester_id";

$success = $DB->execute($sql_query);


if($success==1){

echo "Actualización exitosa";
}else{

echo "Error";
}

}catch(Exception $ex){
    echo $ex;
}
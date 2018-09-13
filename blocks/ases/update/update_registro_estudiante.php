<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_user_extended}
cuando tracking status este en 0 lo modifica a 1
 */

global $DB;

$object_to_delete = array();
$object_to_delete['id'] = 6784;
$DB->delete_records('talentospilos_usuario',$object_to_delete);


$registro_modificar = new StdClass;
$registro_modificar->id = 409;
$registro_modificar->num_doc = '1144104255';

$DB->update_record('talentospilos_usuario', $registro_modificar);

$registro_modificar2 = new StdClass;
$registro_modificar2->id = 34;
$registro_modificar2->id_estudiante = 409;

$DB->update_record('talentospilos_res_estudiante', $registro_modificar2);
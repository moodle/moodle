<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_user_extended}
 */

global $DB;


$object_to_delete1 = array();
$object_to_delete1['id'] = 1042;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete1);
echo '<br>';

$registro_modificar1 = new StdClass;
$registro_modificar1->id = 675;
$registro_modificar1->tracking_status = 0;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar1);

echo '<br>';


$registro_modificar2 = new StdClass;
$registro_modificar2->id = 679;
$registro_modificar2->tracking_status = 0;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar2);

echo '<br>';


$registro_modificar3 = new StdClass;
$registro_modificar3->id = 5036;
$registro_modificar3->tracking_status = 1;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar3);

echo '<br>';


$registro_modificar4 = new StdClass;
$registro_modificar4->id = 1042;
$registro_modificar4->program_status = 7;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar4);

echo '<br>';


$registro_modificar5 = new StdClass;
$registro_modificar5->id = 4922;
$registro_modificar5->tracking_status = 1;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar5);

echo '<br>';

$registro_modificar6 = new StdClass;
$registro_modificar6->id = 653;
$registro_modificar6->tracking_status = 0;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar6);

echo '<br>';
<?php

require_once(dirname(__FILE__). '/../../../config.php');

// Rrgistra el programa 3490 - LICENCIATURA EN EDUCACIÓN FÍSICA Y DEPORTE

global $DB;

$new_program = new stdClass();
$new_program->codigosnies = 106564;
$new_program->cod_univalle = 3490;
$new_program->nombre = "LICENCIATURA EN EDUCACIÓN FÍSICA Y DEPORTE";
$new_program->id_sede = 1;
$new_program->jornada = "DIURNA";
$new_program->id_facultad = 4;

if($DB->insert_record("talentospilos_programa", $new_program, true)){
    echo "Éxito!";
};


?>
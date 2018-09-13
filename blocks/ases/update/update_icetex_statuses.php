<?php

require_once(dirname(__FILE__).'/../../../config.php');

global $DB;
$dbman = $DB->get_manager();

$sql_query = "SELECT * FROM {talentospilos_estados_icetex}";
$icetex_statuses = $DB->get_records_sql($sql_query);

foreach($icetex_statuses as $status){

    $record = new stdClass();
    $record->id = $status->id;

    if($status->nombre == "1. Estudiante desistió del programa Ser Pilo Paga"
       || $status->nombre == "2. Estudiante no actualizó datos"
       || $status->nombre == "3. Estudiante actualizó datos y aplazó"
       || $status->nombre == "4. Estudiante actualizó datos, IES no renovó"
       || $status->nombre == "7. IES renovó, ICETEX desembolsó matrícula, IES reembolsó el giro"){

        $record->descripcion = "INACTIVO";
    }else{
        $record->descripcion = "ACTIVO";
    }

    $DB->update_record('talentospilos_estados_icetex', $record);    

}


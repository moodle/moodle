<?php
require_once dirname(__FILE__) . '/../../../config.php';
global $DB;

$sql_query = "TRUNCATE TABLE {talentospilos_est_estadoases} RESTART IDENTITY";
$result_truncate_est_estado = $DB->execute($sql_query);

print_r($result_truncate_est_estado);
print_r("<br>");
if($result_truncate_est_estado){
    $sql_query = "TRUNCATE TABLE {talentospilos_estados_ases} RESTART IDENTITY";
    $result_truncate_estados_ases = $DB->execute($sql_query);
    print_r($result_truncate_estados_ases);
    print_r("<br>");
}

// Carga de tabla de estados ASES
$status_array = ["seguimiento", "sinseguimiento"];
$description_status_array = ["Se le realiza seguimiento en la estrategia ASES", "No se le realiza seguimiento en la estrategia ASES"];

$object_record = new stdClass();
for($i = 0; $i < count($status_array); $i++){
    $object_record->nombre = $status_array[$i];
    $object_record->descripcion = $description_status_array[$i];

    $DB->insert_record('talentospilos_estados_ases', $object_record);
}

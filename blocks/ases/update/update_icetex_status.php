<?php

require_once(dirname(__FILE__).'/../../../config.php');

global $DB;
$dbman = $DB->get_manager();

$sql_query = "SELECT nombre 
              FROM {talentospilos_estados_icetex}";

$result_query = $DB->get_records_sql($sql_query);
print_r($result_query);

if(array_key_exists('ACTIVO', $result_query)){

    $sql_query = "TRUNCATE {talentospilos_estados_icetex} RESTART IDENTITY";
    $result_query = $DB->execute($sql_query);
    echo "Tabla truncada exitosamente \n";

    // Changing precision of field nombre on table talentospilos_estados_icetex to 200.
    $table = new xmldb_table('talentospilos_estados_icetex');
    $field_name = new xmldb_field('nombre', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null, 'id');
    $field_description = new xmldb_field('descripcion', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null, 'id');


    // Launch change of precision for field nombre.
    $result_precision_name = $dbman->change_field_precision($table, $field_name);
    $result_precision_description = $dbman->change_field_precision($table, $field_description);

    $array_statuses = array(
        "1. Estudiante desistió del programa Ser Pilo Paga",
        "2. Estudiante no actualizó datos",
        "3. Estudiante actualizó datos y aplazó",
        "4. Estudiante actualizó datos, IES no renovó",
        "5. IES renovó, ICETEX pendiente de giro",
        "6. IES renovó, ICETEX desembolsó matrícula",
        "7. IES renovó, ICETEX desembolsó matrícula, IES reembolsó el giro"
    );

    $array_description = array(
        "Estudiante desistió del programa Ser Pilo Paga",
        "Estudiante no actualizó datos",
        "Estudiante actualizó datos y aplazó",
        "Estudiante actualizó datos para renovar, IES no renovó por condiciones académicas o disciplinarias",
        "Institución de Educación Superior renovó, ICETEX pendiente de giro",
        "Institución de Educación Superior renovó, ICETEX desembolsó matrícula",
        "Institución de Educación Superior renovó, ICETEX desembolsó matrícula, IES reembolsó el giro"
    );

    for($i = 0; $i < count($array_statuses); $i++){
        $record = new stdClass();
        $record->nombre = $array_statuses[$i];
        $record->descripcion = $array_description[$i];

        $result = $DB->insert_record('talentospilos_estados_icetex', $record);

        if($result){
            echo "Inserción exitosa: \"$array_statuses[$i]\" \n";
        }
    }

}else{
    echo "Clave no encontrada \n";
}


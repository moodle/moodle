<?php 

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Dynamic PHP Forms
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

    require_once(dirname(__FILE__). '/../../../../../config.php');
    require_once(dirname(__FILE__). '/../../lib/student_lib.php');

    global $DB;

    // Records to update
    $records_to_update = "SELECT id AS id_formulario_respuestas
    FROM {talentospilos_df_form_resp} 
    WHERE estado = 1 AND id_formulario = (
            SELECT id FROM {talentospilos_df_formularios} WHERE alias = 'seguimiento_pares' AND estado = 1
        ) 

    EXCEPT 
    
    SELECT FS.id_formulario_respuestas 
    FROM {talentospilos_df_form_solu} AS FS 
    INNER JOIN {talentospilos_df_respuestas} AS R 
    ON FS.id_respuesta = R.id WHERE R.id_pregunta = 54";

    $records = $DB->get_records_sql( $records_to_update );

    echo "<hr>Cantidad de registros a los cuales se les va a registrar el campo de instancia: <strong>" . count( $records ) . "</strong><br><hr>";

    $faltantes = 0;

    foreach( $records as &$record ){

        $record_id = $record->id_formulario_respuestas;

        //Buscamos el campo con el identificador del estudiante [ 25 ].
        $sql_  ="SELECT * FROM mdl_talentospilos_df_respuestas AS R INNER JOIN 
        (SELECT * FROM mdl_talentospilos_df_form_solu WHERE id_formulario_respuestas = $record_id) AS FS
        ON R.id = FS.id_respuesta
        WHERE id_pregunta = 25";

        $result = $DB->get_record_sql( $sql_ );

        $ases_id = $result->respuesta;

        if($ases_id){

            //Obtenemos la instancia de un estudiante ases.
            $sql_ = "SELECT * FROM mdl_talentospilos_est_estadoases WHERE id_estudiante = $ases_id ORDER BY fecha DESC LIMIT 1";

            $result_2 = $DB->get_record_sql( $sql_ );
            
            $instance_id = -1;
            if($result_2){
                $instance_id = $result_2->id_instancia;
            }

            if( $instance_id != 450299 ){
                continue;
            }

            echo "rid: "  . $record_id . " ases_id: " . $ases_id . " instancia: " . $instance_id;

            // Registrar respuesta[ instancia ]

            $new_respuesta = new stdClass();
            $new_respuesta->id_pregunta = 54;
            $new_respuesta->respuesta = $instance_id;

            $id_record_respuesta = $DB->insert_record( 'talentospilos_df_respuestas', $new_respuesta, $returnid=true, $bulk=false );

            // Registrar en talentospilos_df_form_solu

            $new_form_solu = new stdClass();
            $new_form_solu->id_formulario_respuestas = $record_id;
            $new_form_solu->id_respuesta = $id_record_respuesta;

            $id_registrado = $DB->insert_record( 'talentospilos_df_form_solu', $new_form_solu, $returnid=true, $bulk=false );
            
            echo " Registrado con el id: " . $id_registrado . "<br>";

        }else{
            $faltantes++;
        }
        
    }

    echo "<hr>Estudiantes sin instancia: <strong>" . $faltantes . "</strong><br><hr>";
    
    die();

?>
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

    require_once(dirname(__FILE__). '/../../../../config.php');
    
    if( isset($_GET['form_id']) && isset($_GET['pregunta_id']) && isset($_GET['criterio']) && isset($_GET['order'])){
        header('Content-Type: application/json');

        if( !empty( $_GET[ 'super_su' ] ) ){
            echo dphpforms_find_records($_GET['form_id'], $_GET['pregunta_id'], $_GET['criterio'], $_GET['order'], $_GET[ 'using_like' ], $_GET[ 'super_su' ] );
            die();
        }

        if( (empty( $_GET[ 'using_like' ] ))&& ( empty( $_GET[ 'super_su' ] ) ) ){
            echo dphpforms_find_records($_GET['form_id'], $_GET['pregunta_id'], $_GET['criterio'], $_GET['order']);
        }else{
            echo dphpforms_find_records($_GET['form_id'], $_GET['pregunta_id'], $_GET['criterio'], $_GET['order'], $_GET[ 'using_like' ] );
        }  
        
        
    }
    
    //Se busca por if_from_preg (info en dphpforms_get_record)
    function dphpforms_find_records($id_form, $id_pregunta, $criterio, $order = 'DESC', $using_like = 'false', $super_su = 'false'){

        global $DB;

        $FORM_ID = $id_form;
        $PREGUNTA_ID = $id_pregunta;

        if(!is_numeric($id_form)){
            $sql_alias = "SELECT id FROM {talentospilos_df_formularios} WHERE alias = '$id_form' AND estado = 1";
            $form_record = $DB->get_record_sql($sql_alias);
            if($form_record != null){
                $FORM_ID = (int) $form_record->id;
            }
        }

        if(!is_numeric($FORM_ID)){
            return json_encode(
                array(
                    'results' => array()
                )
            );
        };

        if(!is_numeric($id_pregunta)){
            $sql_alias = "SELECT id_pregunta FROM {talentospilos_df_alias} WHERE alias = '$id_pregunta'";
            $preg_record = $DB->get_record_sql($sql_alias);
            if($preg_record != null){
                $PREGUNTA_ID = (int) $preg_record->id_pregunta;
            }
        }

        if(!is_numeric($PREGUNTA_ID)){
            return json_encode(
                array(
                    'results' => array()
                )
            );
        };

        $sql = "";

        $estado = "AND FR.estado = 1";

        if( $super_su == 'true' ){
            $estado = "";
        }

        if( $using_like == 'true' ){

            $sql = "SELECT FRS.id_formulario_respuestas AS id_registro, FRS.fecha_hora_registro_respuesta AS fecha_hora_registro
            FROM {talentospilos_df_respuestas} AS R 
            INNER JOIN 
                (
                    SELECT FR.id_formulario, FR.fecha_hora_registro AS fecha_hora_registro_respuesta, FS.id_formulario_respuestas, FS.id_respuesta
                    FROM {talentospilos_df_form_resp} AS FR 
                    INNER JOIN {talentospilos_df_form_solu} AS FS 
                    ON FR.id = FS.id_formulario_respuestas 
            WHERE FR.id_formulario = '" . $FORM_ID . "' ".$estado."
                ) AS FRS 
            ON FRS.id_respuesta = R.id
            WHERE R.respuesta LIKE '%" . $criterio . "%' AND R.id_pregunta = '" . $PREGUNTA_ID . "'
            ORDER BY FRS.fecha_hora_registro_respuesta " . $order;

        }else if( $using_like == 'false' ){

            $sql = "SELECT FRS.id_formulario_respuestas AS id_registro, FRS.fecha_hora_registro_respuesta AS fecha_hora_registro
            FROM {talentospilos_df_respuestas} AS R 
            INNER JOIN 
                (
                    SELECT FR.id_formulario, FR.fecha_hora_registro AS fecha_hora_registro_respuesta, FS.id_formulario_respuestas, FS.id_respuesta
                    FROM {talentospilos_df_form_resp} AS FR 
                    INNER JOIN {talentospilos_df_form_solu} AS FS 
                    ON FR.id = FS.id_formulario_respuestas 
            WHERE FR.id_formulario = '" . $FORM_ID . "' ".$estado."
                ) AS FRS 
            ON FRS.id_respuesta = R.id
            WHERE R.respuesta = '" . $criterio . "' AND R.id_pregunta = '" . $PREGUNTA_ID . "'
            ORDER BY FRS.fecha_hora_registro_respuesta " . $order;

        }else{
            return json_encode(
                array(
                    'results' => array()
                )
            );
        }

        $resultados = $DB->get_records_sql($sql);
        return json_encode(
            array(
                'results' => array_values($resultados)
            )
        );
    }

?>
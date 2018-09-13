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
    
    //$_GET['source'] prevents collisions with other API calls.

    if( isset( $_GET['record_id'] ) && ( !isset( $_GET['source'] ) ) ){
        header('Content-Type: application/json');
        $a_key = null;
        if(isset($_GET['alias_key'])){
            $a_key = $_GET['alias_key'];
        };
        if( isset( $_GET['ksuper_su'] ) ){
            $ksuper_su = false;
            if( $_GET['ksuper_su'] == 'true' ){
                $ksuper_su = true;
            };
            echo dphpforms_get_record( $_GET['record_id'], $a_key, $ksuper_su );
        }else{
            echo dphpforms_get_record( $_GET['record_id'], $a_key );
        };
    };
    
    function dphpforms_get_record($record_id, $alias_key, $super_su = false){

        global $DB;

        $state = 'AND FR.estado = 1';
        if( $super_su ){
            $state = '';
        };

        $sql = "SELECT * FROM {talentospilos_df_preguntas} P 
                INNER JOIN (
                SELECT * FROM (
                    SELECT id AS id_form_preg, id_pregunta AS id_tabla_preguntas FROM {talentospilos_df_form_preg}
                    ) FP INNER JOIN (SELECT * 
                                FROM {talentospilos_df_respuestas} AS R 
                                INNER JOIN 
                                    (
                                        SELECT * 
                                        FROM {talentospilos_df_form_resp} AS FR 
                                        INNER JOIN {talentospilos_df_form_solu} AS FS 
                                        ON FR.id = FS.id_formulario_respuestas 
                                        WHERE FR.id = '".$record_id."' $state
                                    ) AS FRS 
                                ON FRS.id_respuesta = R.id) RF
                            ON RF.id_pregunta = FP.id_form_preg) TT
                ON id_tabla_preguntas = P.id";

        $list_respuestas = $DB->get_records_sql($sql);
        $list_respuestas = array_values($list_respuestas);

        $sql_record = "SELECT * FROM {talentospilos_df_form_resp} WHERE id = '"  . $record_id . "'";
        $record_info = $DB->get_record_sql($sql_record);

        $respuestas = array();
        $key = null;
        if(count($list_respuestas) > 0){
            foreach($list_respuestas as &$respuesta){
                $sql_field_type = "SELECT * FROM {talentospilos_df_tipo_campo} WHERE id = '$respuesta->tipo_campo'";
                $field_type = $DB->get_record_sql($sql_field_type);
                $tmp_respuesta = array(
                    'enunciado' => $respuesta->enunciado,
                    'respuesta' => $respuesta->respuesta,
                    'opciones' => $respuesta->opciones_campo,
                    'tipo_campo' => $field_type->campo,
                    'id_pregunta' => $respuesta->id_tabla_preguntas,
                    'id_relacion_form_pregunta' => $respuesta->id_form_preg,
                    'local_alias' => json_decode($respuesta->atributos_campo)->{'local_alias'},
                );
                if(($alias_key)&&(json_decode($respuesta->atributos_campo)->{'local_alias'} == $alias_key)){
                    $key = $tmp_respuesta;
                };
                array_push($respuestas, $tmp_respuesta);
            };
        }else{
            return json_encode( array( 'record' => array() ) );
        };

        $form_alias = $DB->get_record_sql( "SELECT alias FROM {talentospilos_df_formularios} WHERE id = " . $list_respuestas[0]->id_formulario )->alias;

        return json_encode(
            array(
                'record' => array(
                    'id_formulario' => $list_respuestas[0]->id_formulario,
                    'alias' => $form_alias,
                    'id_registro' => $list_respuestas[0]->id_formulario_respuestas,
                    'fecha_hora_registro' => $record_info->fecha_hora_registro,
                    'campos' => $respuestas,
                    'alias_key' => $key
                )
            )
        );
    }

?>
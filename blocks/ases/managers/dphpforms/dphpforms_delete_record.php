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
    
    $_GET['source'] = 'dphpforms_delete_record'; // Prevents collisions with get_record API calls.

    require_once(dirname(__FILE__). '/../../../../config.php');
    require_once(dirname(__FILE__). '/dphpforms_get_record.php');
    require_once( $CFG->libdir . '/adminlib.php');

    if( isset( $_GET['record_id'] ) ){
        header('Content-Type: application/json');
        echo dphpforms_delete_record( $_GET['record_id'] );
    }

    function dphpforms_delete_record( $record_id ){

        global $DB;
        global $USER;

        if(!is_numeric( $record_id )){
            return json_encode(
                array(
                    'status' => '-1',
                    'message' => 'Invalid record id',
                    'data' => ''
                )
            );
        }

        $sql = "SELECT * FROM {talentospilos_df_form_resp} WHERE id = '$record_id' AND estado = '1'";
        $result = $DB->get_record_sql($sql);
        
        if($result){

            $previous_data = dphpforms_get_record($record_id, null);
            $current_data = null;
            
            $deleted_record = new stdClass();
            $deleted_record->id = $result->id;
            $deleted_record->id_formulario = $result->id_formulario; 
            $deleted_record->id_monitor = $result->id_monitor;
            $deleted_record->id_estudiante = $result->id_estudiante;
            $deleted_record->fecha_hora_registro = $result->fecha_hora_registro;
            $deleted_record->estado = '0';
            $DB->update_record('talentospilos_df_form_resp', $deleted_record, $bulk=false);

            $retorno = json_encode(
                array(
                    'status' => '0',
                    'message' => 'Deleted',
                    'data' => ''
                )
            );

            $stored_data = dphpforms_get_record($record_id, null, true);

            $to_warehouse = new stdClass();
            $to_warehouse->id_usuario_moodle = $USER->id;
            $to_warehouse->accion = "DELETE";
            $to_warehouse->id_registro_respuesta_form = $record_id;
            $to_warehouse->datos_previos = $previous_data;
            $to_warehouse->datos_enviados = $current_data;
            $to_warehouse->datos_almacenados = $stored_data;
            $to_warehouse->observaciones = "Eliminado lógico";
            $to_warehouse->cod_retorno = json_decode($retorno)->status;
            $to_warehouse->msg_retorno = json_decode($retorno)->message;
            $to_warehouse->dts_retorno = json_encode(json_decode($retorno)->data);
            $to_warehouse->navegador = $_SERVER['HTTP_USER_AGENT'];
            $to_warehouse->url_request = $_SERVER['HTTP_REFERER'];

            $DB->insert_record('talentospilos_df_dwarehouse', $to_warehouse, $returnid=false, $bulk=false);

            return $retorno;
            
        }else{
            return json_encode(
                array(
                    'status' => '-1',
                    'message' => 'Record does not exist',
                    'data' => ''
                )
            );
        }

    }


?>
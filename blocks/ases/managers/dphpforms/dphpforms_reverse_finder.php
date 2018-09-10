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

    if( isset( $_GET['respuesta_id'] ) ){
        header('Content-Type: application/json');
        echo dphpforms_reverse_finder( $_GET['respuesta_id'] );
    };
    
    function dphpforms_reverse_finder( $response_id, $super_su = false ){
        global $DB;
        $sql = "SELECT * FROM {talentospilos_df_respuestas} WHERE id = '" . $response_id . "'";
        $respuesta = $DB->get_record_sql( $sql );
        if( $respuesta ){
            //talentospilos_df_form_solu
            $sql = "SELECT * FROM {talentospilos_df_form_solu} WHERE id_respuesta = '" . $respuesta->id . "'";
            $form_solu = $DB->get_record_sql( $sql );
            if( $form_solu ){
                //talentospilos_df_form_resp
                $sql = "SELECT id AS id_registro, fecha_hora_registro FROM {talentospilos_df_form_resp} WHERE id = '" . $form_solu->id_formulario_respuestas . "'";
                $form_resp = $DB->get_record_sql( $sql );
                if( $form_solu ){
                    return json_encode(
                        array(
                            'result' => $form_resp
                        )
                    );
                }else{
                    return json_encode(
                        array(
                            'result' => null
                        )
                    );
                };
            }else{
                return json_encode(
                    array(
                        'result' => null
                    )
                );
            };
        }else{
            return json_encode(
                array(
                    'result' => null
                )
            );
        };
    };

?>
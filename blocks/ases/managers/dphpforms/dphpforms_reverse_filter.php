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
    
    // El filtro con respuesta casteada no es completo.
    //Criteria
    /*{
        "criteria":[
            {
                "operator":">",
                "value":"XYZ",
                "logic_operator":"AND"
            },
            {
                "operator":"<",
                "value":"XXY",
                "logic_operator":"OR"
            }
        ]
    }*/

    /*$test_criteria = json_decode( 
        '{
            "criteria":[
                {
                    "operator":">",
                    "value":"2018-02-01"
                },
                {
                    "operator":"<",
                    "value":"2018-06-01"
                }
            ]
        }' 
    );

    //Test
    echo dphpforms_reverse_filter( "953", "DATE", $test_criteria );*/
    // 

    if( isset( $_GET['id_pregunta'] ) && isset( $_GET['cast'] ) && isset( $_GET['criterio'] ) ){
        header('Content-Type: application/json');
        echo dphpforms_reverse_filter( $_GET['id_pregunta'], $_GET['cast'], json_decode( $_GET['criterio'] ) );
    };

    function dphpforms_reverse_filter($id_pregunta, $cast_to, $criteria){
        global $DB;
        
        $cast = "";
        $double_presicion_cast = "";
        $PREGUNTA_ID = -1;

        if(!is_numeric($id_pregunta)){
            $sql_alias = "SELECT id_pregunta FROM {talentospilos_df_alias} WHERE alias = '$id_pregunta'";
            $preg_record = $DB->get_record_sql($sql_alias);
            if($preg_record != null){
                $PREGUNTA_ID = (int) $preg_record->id_pregunta;
            }else{
                return json_encode(
                    array(
                        'results'=> null
                    )
                );
            };
        }else{
            $PREGUNTA_ID = $id_pregunta;
        };

        if( $cast_to ){
            $cast = ", NULLIF(respuesta,'')::$cast_to AS respuesta_casted";
            $cast_to = strtoupper( $cast_to );
            if( $cast_to == "DATE" ){
                $double_precision_cast = "AND respuesta ~ '[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}'";
            };
        }else{
            $cast = ", NULLIF(respuesta,'')::text AS respuesta_casted";
        };

        $sql_criteria = "";
        foreach( $criteria->criteria as $key => $criteria_element ){
            if( $key == 0 ){
                $sql_criteria .= "WHERE SC.respuesta_casted " . $criteria_element->operator . " '" . $criteria_element->value . "'";
            }else{
                $sql_criteria .= " AND SC.respuesta_casted " . $criteria_element->operator . " '" . $criteria_element->value . "'";
            };
        };
        $sql="SELECT * FROM ( SELECT *$cast FROM {talentospilos_df_respuestas} WHERE id_pregunta = '$PREGUNTA_ID' $double_precision_cast ) AS SC " . $sql_criteria;
        $records = $DB->get_records_sql( $sql );
        return json_encode(
            array(
                'results'=> array_values( $records )
            )
        );
    };

?>
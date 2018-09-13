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
    require_once(dirname(__FILE__).'/monitor_assignments_lib.php');

    header('Content-Type: application/json');

    global $USER;
    
    $raw_data = file_get_contents("php://input");
    
    // Validation if the user is logged. 
    if( $USER->id == 0 ){
        return_with_code( -1 );
    }

    $input = json_decode( $raw_data );

    /**
     * Api para el control de las asignaciones de monitor - practicante y monitor - estudiante.
     * @author Jeison Cardona Gomez
     * @see monitor_assignments_lib.php
     * @param json $input This input is a json with a function name and their respective parameters. The order of these parameters is very important. See every function to notice of their parameters order.
     * @return json The structure is {"status_code":int, "error_message":string, "data_response":string }
    */

    // Example of valid input. params = Parameters
    // { "function":"get_monitors_by_instance", "params":[ instance_id ] }

    if( isset($input->function) && isset($input->params) ){

        // Get practicant monitor relationship by instance
        // params[0] => instance_id
        if( $input->function == "get_practicant_monitor_relationship_by_instance" ){

            /* In this request is only valid pass like param(Parameters) the instance identificatior, 
             * for this reason, the input param only can be equal in quantity to one.
             * */
            
            if( count( $input->params ) == 1 ){

                // Order of params
                /**
                 * The instance value only can be a number.
                 */
                
                if( is_numeric( $input->params[0] ) ){
                    
                    echo json_encode( 
                        array(
                            "status_code" => 0,
                            "error_message" => "",
                            "data_response" => array_values( monitor_assignments_get_practicant_monitor_relationship_by_instance( $input->params[0] ) )
                        )
                    );
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else if( $input->function == "get_monitors_students_relationship_by_instance" ){

            if( count( $input->params ) == 1 ){

                // Order of params
                /**
                 * The instance value only can be a number.
                 */
                
                if( is_numeric( $input->params[0] ) ){
                    
                    echo json_encode( 
                        array(
                            "status_code" => 0,
                            "error_message" => "",
                            "data_response" => array_values( monitor_assignments_get_monitors_students_relationship_by_instance( $input->params[0] ) )
                        )
                    );
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else if( $input->function == "create_monitor_student_relationship" ){

            if( count( $input->params ) == 3 ){

                // Order of params
                /**
                 * The instance value only can be a number.
                 * The monitor value only can be a number.
                 * The student value only can be a number.
                 */
                
                if( is_numeric( $input->params[0] ) && is_numeric( $input->params[1] ) && is_numeric( $input->params[2] ) ){
                    
                    $return_value = monitor_assignments_create_monitor_student_relationship( $input->params[0], $input->params[1], $input->params[2] );
                    
                    if( $return_value ){

                        echo json_encode( 
                            array(
                                "status_code" => 0,
                                "error_message" => "",
                                "data_response" => "created"
                            )
                        );

                    }else{
                        return_with_code( -5 );
                    }
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else if( $input->function == "delete_monitor_student_relationship" ){

            if( count( $input->params ) == 3 ){

                // Order of params
                /**
                 * The instance value only can be a number.
                 * The monitor value only can be a number.
                 * The student value only can be a number.
                 */
                
                if( is_numeric( $input->params[0] ) && is_numeric( $input->params[1] ) && is_numeric( $input->params[2] ) ){
                    
                    $return_value = monitor_assignments_delete_monitor_student_relationship( $input->params[0], $input->params[1], $input->params[2] );
                    
                    if( $return_value ){

                        echo json_encode( 
                            array(
                                "status_code" => 0,
                                "error_message" => "",
                                "data_response" => "deleted"
                            )
                        );

                    }else{
                        echo json_encode( 
                            array(
                                "status_code" => 1,
                                "error_message" => "",
                                "data_response" => "the record does not exist"
                            )
                        );
                    }
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else if( $input->function == "create_practicant_monitor_relationship" ){

            if( count( $input->params ) == 3 ){

                // Order of params
                /**
                 * The instance value only can be a number.
                 * The practicant value only can be a number.
                 * The monitor calue only can be a number.
                 */
                
                if( is_numeric( $input->params[0] ) && is_numeric( $input->params[1] ) && is_numeric( $input->params[2] ) ){
                    
                    $return_value = monitor_assignments_create_practicant_monitor_relationship( $input->params[0], $input->params[1], $input->params[2] );
                    
                    if( $return_value ){

                        echo json_encode( 
                            array(
                                "status_code" => 0,
                                "error_message" => "",
                                "data_response" => "created"
                            )
                        );

                    }else{
                        return_with_code( -5 );
                    }
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else if( $input->function == "delete_practicant_monitor_relationship" ){

            if( count( $input->params ) == 3 ){

                // Order of params
                /**
                 * The instance value only can be a number.
                 * The practicant value only can be a number.
                 * The monitor value only can be a number.
                 */
                
                if( is_numeric( $input->params[0] ) && is_numeric( $input->params[1] ) && is_numeric( $input->params[2] ) ){
                    
                    $return_value = monitor_assignments_delete_practicant_monitor_relationship( $input->params[0], $input->params[1], $input->params[2] );
                    
                    if( $return_value ){

                        echo json_encode( 
                            array(
                                "status_code" => 0,
                                "error_message" => "",
                                "data_response" => "deleted"
                            )
                        );

                    }else{
                        echo json_encode( 
                            array(
                                "status_code" => 1,
                                "error_message" => "",
                                "data_response" => "the record does not exist"
                            )
                        );
                    }
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else if( $input->function == "transfer" ){

            if( count( $input->params ) == 3 ){

                // Order of params
                /**
                 * The instance value only can be a number.
                 * The old_monitor_id value only can be a number.
                 * The new_monitor_id value only can be a number.
                 */
                
                if( is_numeric( $input->params[0] ) && is_numeric( $input->params[1] ) && is_numeric( $input->params[2] ) ){
                    
                    $return_value = monitor_assignments_transfer( $input->params[0], $input->params[1], $input->params[2] );
                    
                    if( $return_value ){

                        echo json_encode( 
                            array(
                                "status_code" => 0,
                                "error_message" => "",
                                "data_response" => "transfered"
                            )
                        );

                    }else{
                        echo json_encode( 
                            array(
                                "status_code" => 1,
                                "error_message" => "",
                                "data_response" => "empty"
                            )
                        );
                    }
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else{
            // Function not defined
            return_with_code( -4 );
        }
        
    }else{
        return_with_code( -2 );
    }

    function return_with_code( $code ){
        
        switch( $code ){

            case -1:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "You are not allowed to access this resource.",
                        "data_response" => ""
                    )
                );
                break;
            case -2:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "Error in the scheme.",
                        "data_response" => ""
                    )
                );
                break;
            case -3:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "Invalid values in the parameters.",
                        "data_response" => ""
                    )
                );
                break;
            case -4:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "Function not defined.",
                        "data_response" => ""
                    )
                );
                break;
            
            case -5:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "Duplicate.",
                        "data_response" => ""
                    )
                );
                break;
            
            case -99:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "critical error.",
                        "data_response" => ""
                    )
                );
                break;

        }

        die();
    }

?>
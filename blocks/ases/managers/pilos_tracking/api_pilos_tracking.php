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
    require_once(dirname(__FILE__).'/pilos_tracking_lib.php');
    //rid = record_id
    //arg = student_code, i.e 142XXXX or undefined or 0

    

    if( isset($_GET['function']) && isset($_GET['arg']) && isset($_GET['rid']) ){

        if( $_GET['arg'] == 'undefined' ){
            $_GET['arg'] = '0';
        };

        if( ( $_GET['function'] == 'update_last_user_risk' ) && ( $_GET['arg'] == '0' ) ){

            header('Content-Type: application/json');
            update_last_user_risk( $_GET['arg'], $_GET['rid'] );
            echo json_encode( array( 'error' => '0', 'message' => "" ) );

        }else if( ( $_GET['function'] == 'update_last_user_risk' ) && ( $_GET['arg'] != '0' ) ){

            header('Content-Type: application/json');
            update_last_user_risk( $_GET['arg'], -1 );
            echo json_encode( array( 'error' => '0', 'message' => "" ) );

        }
    }else{
        echo json_encode( array( 'error' => '-1', 'message' => "arg, function, rid" ) );
    };

?>
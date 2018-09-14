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
 * Estrategia ASES
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once(dirname(__FILE__) . '/../lib/student_lib.php');


if( isset( $_GET['function'] ) && isset( $_GET['arg'] ) ){

    $function = $_GET['function'];

    if( $function == 'get_user_information' ){
        header('Content-Type: application/json');
        $user_id = $_GET['arg'];

        $ases_user = get_full_user($user_id);
        $user = new stdClass();
        $user->firstname = null;
        $user->lastname = null;
        //Security clean
        if($ases_user){
            $user->firstname = $ases_user->firstname;
            $user->lastname = $ases_user->lastname;
        }
        echo json_encode( $user );
    }

}

die();
    

?>
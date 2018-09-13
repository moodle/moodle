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
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Obtains a select given an array
 * @see get_roles_select($roles,$nombre_rol)
 * @param $roles --> array 
 * @param $nombre_rol --> name that will be assigned to the select
 * @return string html with the select obtained
 **/
function get_roles_select($roles,$nombre_rol){
    $table = "";
    $table.='<select class="form-pilos" id="'.$nombre_rol.'">';
    $table.='<option value="-1" > ------------------------------------- </option>';
    foreach($roles as $role){
            $table.='<option value="'.$role->id_usuario.'">'.$role->username." - ".$role->firstname." ".$role->lastname.'</option>';
     }
    $table.='</select>';
    return $table;

}

/**
 * Function that adds two select and one button to an array result.
 * @see get_assign($result,$practicants,$monitors)
 * @param $result --> array
 * @param $practicants --> array of practicants
 * @param $monitors --> array of monitors
 * @return array
 **/
function get_assign($result,$practicants,$monitors)
{
    global $DB;
    $array           = Array();

    $name_practicants=get_roles_select($practicants,"practicants");
    $name_monitors=get_roles_select($monitors,"monitors");

    
    foreach ($result as $r) {

        $r->monitor= $name_monitors;

        $r->practicante= $name_practicants;

        $r->assign= '<button type="button" id="student_assign"  class="red glyphicon glyphicon-ok"></button>';
  
        array_push($array, $r);
    }
    return $array;
}

/**
 * Function that creates options of a select of monitors by an array given
 * @see create_option_of_select($monitors)
 * @param $monitors --> array
 * @return String
 **/
function create_option_of_select($monitors)
{

    $table = "";
    $table.='<option value="-1" > ------------------------------------- </option>';
    foreach($monitors as $monitor){
            $table.='<option value="'.$monitor->id_usuario.'">'.$monitor->username." - ".$monitor->firstname." ".$monitor->lastname.'</option>';
     }
    return $table;
}




?>
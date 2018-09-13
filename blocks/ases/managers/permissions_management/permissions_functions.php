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
require_once('permissions_lib.php');


/**
 * Creates variables according to a speficif funcionality name
 *
 * @see create_variablebyname($validation)
 * @param $validation --> object with values to create
 * @return void
 */

function create_variablebyname($validation)
{

    $array_variable = array();

    foreach ($validation as $key => $value) {

        ${$value->nombre_accion} = true;

        $name        = $value->nombre_accion;
        $data->$name = $name;
        array_push($array_variable, $name);
        array_push($array_variable, $data->$name);
    }

    return $array_variable;

}


/**
 * Function that gets all functionalities and their actions
 * @see get_functions_actions()
 * @param $rol
 * @return string html with all functionalities obtained
 **/

function get_functions_actions()
{
    $table     = "";
    $functions = get_functions();


    foreach ($functions as $function) {

        $table .= ' <div class="col-lg-3 col-md-3"><fieldset id="' . $function->id . '"><legend>' . $function->nombre_func . '</legend>';
        $actions = get_actions_function($function->id);
        foreach ($actions as $action) {


            $table .= '<input type="checkbox" name="actions[]" "="" value="' . $action->id . '">' . $action->nombre_accion . '</br>';

        }


        $table .= '</div>';

    }
    return $table;
}




/**
 * Obtains a select given an array
 * @see get_roles_select($roles,$nombre_rol)
 * @param $roles --> array
 * @param $nombre_rol --> name that will be assigned to the select
 * @return string html with the select obtained
 **/
function get_roles_select($roles, $nombre_rol)
{
    $table = "";
    $table .= '<select class="form-pilos" id="' . $nombre_rol . '">';
    $table .= '<option></option>';
    foreach ($roles as $role) {
        $table .= '<option value="' . $role->id . '">' . $role->nombre_rol . '</option>';
    }
    $table .= '</select>';
    return $table;

}


/**
 * Gets a select given an array
 * @see get_functions_select($functions,$nombre_function)
 * @param $functions --> array containing function information
 * @param $nombre_function --> function name
 * @return string html with the select obtained
 **/
function get_functions_select($functions, $nombre_function)
{
    $table = "";
    $table .= '<select class="form-pilos" id="' . $nombre_function . '">';
    $table.='<option value="0" > ------------------------------------- </option>';

    foreach ($functions as $function) {
        $table .= '<option value="' . $function->id . '">' . $function->nombre_func . '</option>';
    }
    $table .= '</select>';
    return $table;

}


/**
 * Gets a select given an array
 * @see get_actions_select($actions)
 * @param $actions --> array containing actions information
 * @return string html with the select obtained
 **/
function get_actions_select($actions)
{
    $table = "";
    $table .= '<select class="form-pilos" id="actions">';
    foreach ($actions as $action) {
        $table .= '<option value="' . $action->id . '">' . $action->nombre_accion . '</option>';
    }
    $table .= '</select>';
    return $table;

}
?>

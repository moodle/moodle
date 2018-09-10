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
require_once(dirname(__FILE__) . '/../../../../config.php');


//Functionalities

/**
 * Gets all functionalities from {talentospilos_funcionalidades} table
 * @see get_functions()
 * @return array containing the information obtained from db
 **/

function get_functions()
{
    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_funcionalidad} ";
    return $DB->get_records_sql($sql_query);
}

/**
 * Returns all functionalities along the 'eliminar' field on system
 * @see get_functions_table()
 * @return array with strings HTML containing every functionality
 **/

function get_functions_table()
{
    global $DB;
    $array           = Array();
    $functions_array = get_functions();

    foreach ($functions_array as $function) {

        $function->edit= '   <button type="button" class="red glyphicon glyphicon-pencil"  id="'.  $function->id .'" data-toggle="modal" data-target="#edit"></button>';

        $function->delete= '   <button type="button" class="red glyphicon glyphicon-remove"  id="'.  $function->id .'"></button>';

        array_push($array, $function);
    }
    return $array;
}

/**
 * Gets all functionalities from {talentospilos_funcionalidades} table by name
 * @see get_functions_by_name($name)
 * @param $name --> Functionalityname
 * @return object With all functionalities
 **/

function get_functions_by_name($name)
{
    global $DB;


    $sql_query = "SELECT * FROM {talentospilos_funcionalidad} where  nombre_func='$name'";
    return $DB->get_record_sql($sql_query);
}



//Actions.

/**
 * Gets all actions from  {talentospilos_accion} table by name
 * @see  get_action_by_name($name)
 * @param $name ---> action name
 * @return object representing all actions obtained
 **/

function get_action_by_name($name)
{
    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_accion} WHERE nombre_accion ='$name'";
    return $DB->get_record_sql($sql_query);
}

/**
 * Returns true if an user can execute an specific action, false otherwise.
 * @see get_action_by_role($id_action,$id_role)
 * @param $id_action --> action id
 * @param $id_role --> role id
 * @return boolean
 */
function get_action_by_role($id_action,$id_role){
    global $DB;
    $sql_query = "SELECT id_accion,nombre_accion  FROM {talentospilos_permisos_rol}  permisos INNER JOIN {talentospilos_accion}   accion ON permisos.id_accion = accion.id where accion.id='$id_action' and id_rol='$id_role' and estado=1";
    $consulta = $DB->get_record_sql($sql_query);
    if($consulta){
        return true;
    }else{


        return false;
    }
}

/**
 * Modifies a record if it's an action, functionalitiy or role.
 * @see modify_record($id,$table,$nombre,$descripcion,$funcionalidad)
 * @param $id --> record id
 * @param $table --> current record
 * @param $nombre --> name to update
 * @param $descripcion --> description
 * @param $funcionalidad --> functionality id
 * @return object With success or error information
 **/

function modify_record($id,$table,$nombre,$descripcion,$funcionalidad)
{
    global $DB;
    $record = new stdClass();
    $record->id = $id;
    $record->descripcion =$descripcion;


    if($table=='accion'){
     $record->nombre_accion =$nombre;
     $record->id_funcionalidad=$funcionalidad;
    }else if($table =="funcionalidad"){
     $record->nombre_func =$nombre;
    }

    $tabla = "talentospilos_".$table;
    $modify = $DB->update_record($tabla, $record);

    if($modify){
         $msg        = new stdClass();
            $msg->title = "Éxito";
            $msg->text  = "Se modificó satisfactoriamente el registro";
            $msg->type  = "success";
        }else{
             $msg        = new stdClass();
            $msg->title = "Error";
            $msg->text  = "Se presento un problema";
            $msg->type  = "error";
        }

    return $msg;
}


/**
 * Gets all actions from  {talentospilos_accion} table by id
 *
 * @see get_action_by_id($id)
 * @param $id ---> action id
 * @return object Representing the action
 **/

function get_action_by_id($id)
{
    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_accion} WHERE id ='$id'";
    return $DB->get_record_sql($sql_query);
}

/**
 * Gets all actions from  {talentospilos_accion} table
 * @see get_actions()
 * @return array of actions
 **/

function get_actions()
{
    global $DB;

    $sql_query = "SELECT DISTINCT(accion.nombre_accion),accion.id,accion.descripcion,funcionalidad.nombre_func FROM {talentospilos_accion} accion
    INNER JOIN {talentospilos_funcionalidad} funcionalidad ON accion.id_funcionalidad = funcionalidad.id where estado=1";
    return $DB->get_records_sql($sql_query);

}

/**
 * Gets all actions related to a functionality from  {talentospilos_accion} table
 * @see  get_actions_function($funcionalidad)
 * @param $funcionalidad --> functionality id
 * @return array filled of actions
 **/

function get_actions_function($funcionalidad)
{
    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_accion} WHERE estado=1 and id_funcionalidad=" . $funcionalidad;
    return $DB->get_records_sql($sql_query);
}

/**
 * Returns true if a given action belongs to a given functionality, false otherwise.
 * @see is_action_in_functionality($id_action,$id_functionality)
 * @param $id_action --> action id
 * @param $id_functionality --> functionality id
 * @return boolean
 **/

function is_action_in_functionality($id_action,$id_functionality)
{
    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_accion} where id_funcionalidad='$id_functionality' and id='$id_action'";
    $exist = $DB->get_record_sql($sql_query);
    if($exist){
        return true;
    }else{
        return false;
    }
}

/**
 * Returns array with of actions with two parameters more (button and link)-
 * @see  get_actions_table()
 * @return array
 **/

function get_actions_table()
{
    global $DB;
    $array         = Array();
    $actions_array = get_actions();

    foreach ($actions_array as $action) {
        $action->edit= '   <button type="button" class="red glyphicon glyphicon-pencil"  id="'. $action->id .'" data-toggle="modal" data-target="#edit"></button>';

        $action->delete= "<a id = \"delete_action\"  ><span  id=\"" . $action->id . "\" class=\"red glyphicon glyphicon-remove\"></span></a>";
        array_push($array, $action);
    }
    return $array;
}



//Role.

/**
 * Gets all roles from {talentospilos_rol} table
 * @see get_roles()
 * @return array filled with roles
 **/

function get_roles()
{
    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_rol}";
    return $DB->get_records_sql($sql_query);
}

/**
 * Gets all records from {talentospilos_permisos_rol} table given a role
 * @see  get_functions_by_role($id_role)
 * @param $id_role --> role id
 * @return array of records
 **/

function get_functions_by_role($id_role)
{
    global $DB;

    $sql_query      = "SELECT * FROM {talentospilos_permisos_rol} where id_rol=" . $id_role;
    $consult        = $DB->get_records_sql($sql_query);
    $array_selected = array();
    foreach ($consult as $record) {
        array_push($array_selected, $record->id_accion);
    }

    return $array_selected;
}

/**
 * Returns all functionalities names given a role
 * @see get_actions_by_role_id($id_role)
 * @param $id_role --> role id
 * @return array with the name of every functionality
 **/

function get_functions_by_role_id($id_role){

    global $DB;

    $sql_query = "SELECT DISTINCT funcionalidad.nombre_func FROM mdl_talentospilos_permisos_rol AS permisos_rol
                    INNER JOIN mdl_talentospilos_accion AS accion ON  permisos_rol.id_accion = accion.id
                    INNER JOIN mdl_talentospilos_funcionalidad AS funcionalidad ON accion.id_funcionalidad = funcionalidad.id
                    WHERE id_rol =" . $id_role;
    $results = $DB->get_records_sql($sql_query);
    $array_functions = array();
    foreach ($results as $record){
        array_push($array_functions, $record->nombre_func);
    }

    return $array_functions;

}


/**
 * Gets all roles with their delete field on system
 *
 * @see get_roles_table()
 * @return array
 **/

function get_roles_table()
{
    global $DB;
    $array       = Array();
    $roles_array = get_roles();

    foreach ($roles_array as $role) {
        if($role->nombre_rol=='sistemas'){
         $role->edit= '   <span class="red glyphicon glyphicon-ban-circle"></span>';

        }else{
        $role->edit= '   <button type="button" class="red glyphicon glyphicon-pencil"  id="'.$role->id .'" data-toggle="modal" data-target="#edit"></button>';
    }
        array_push($array, $role);
    }
    return $array;
}



/**
 * Changes state of an action record depending of a source
 * @see delete_record($id,$source)
 * @param $id ---> record id
 * @param $source --> record table to delete
 * @return object with information of the executed change
 **/

function delete_record($id, $source)
{
    global $DB;
    $record     = new stdClass();
    $record->id = $id;
    $paso       = true;
    try {
        if ($source == 'accion' || $source == 'usuario_perfil') {
            $record->estado = 0;
            $nombre = $DB->get_record_sql("SELECT nombre_accion as nombre FROM {talentospilos_accion} WHERE id = $id")->nombre;
            $nombre_borrado = $nombre."_droped";
            $validate_drop = true;
            while ($validate_drop) {
                $validate_drop = $DB->get_record_sql("SELECT nombre_accion as nombre FROM {talentospilos_accion} WHERE nombre_accion = '$nombre_borrado' ");
                if($validate_drop){
                    $nombre_borrado .= '_droped';
                }else{
                    break;
                }
            }
            $record->nombre_accion = $nombre_borrado;
            
            $paso           = $DB->update_record('talentospilos_' . $source, $record);

        } else if ($source == 'perfil_accion') {
            $record->habilitado = 0;
            $paso               = $DB->update_record('talentospilos_' . $source, $record);
        }

        if ($paso) {
            $msg        = new stdClass();
            $msg->title = "Éxito";
            $msg->text  = "Se eliminó satisfactoriamente el registro";
            $msg->type  = "success";

        } else {

            $msg->title = "Error";
            $msg->text  = "No se pudo eliminar el registro seleccionado";
            $msg->type  = "error";

        }

        return $msg;
    }
    catch (Exception $ex) {

        $msg->title = "Inconveniente !";
        $msg->text  = $ex;
        $msg->type  = "error";
        return $msg;
    }
}



?>

<?php
require_once (dirname(__FILE__) . '/../../../config.php');

require_once ('permissions_management/permissions_lib.php');
require_once(dirname(__FILE__) . '/periods_management/periods_lib.php');

global $USER;
/*
* Función que evalua si el rol del usuario tiene permisos en una view especifica.
*
* @param $userid --> id de usuario
* @param $blockid --> instancia
* @return Object
*/

function authenticate_user_view($userid, $blockid,$vista=null)
{

    // Se obtiene la URL actual.

    $url = $_SERVER['REQUEST_URI'];
    $aux_function_name = explode("/", $url);

    // obtiene nombre de la vista actual.

    $function_name = explode(".php", $aux_function_name[5]) [0];
   
    if($vista){
        $function_name=$vista;
    }
    return get_actions_view($function_name,$userid,$blockid);
    
}

function get_actions_view($function_name,$userid,$blockid,$vista=null){
    $function = get_functions_by_name($function_name);
    $data = 'data';
    $data = new stdClass;
    try {
        if ($function) {
            $role = get_id_rol($userid, $blockid);
            if ($role) {
                $validation = get_actions_by_role($function->id, $role);
                $credentials = empty($validation);
                $message = "";
                $table_courseuseres = "";
                $view_users = "";
                $search_users = "";
                if ($credentials) {
                    $message = '<h3><strong><p class="text-danger">El usuario conectado no puede realizar dicha acción</p></strong></h3>';
                    $data->message = $message;
                    return $data;
                }
                else {
                    foreach($validation as $key => $value) {
                        $ {
                            $value->nombre_accion
                        } = true;
                        $name = $value->nombre_accion;

                        $data->$name = $name;
                    }

                    return $data;
                }
            }
            else {
                $message = '<h3><strong><p class="text-danger">El usuario conectado no se encuentra registrado en la instancia actual</p></strong></h3>';
                $data->message = $message;
                return $data;
            }
        }
        else {
            $message = '<h3><strong><p class="text-danger">La funcionalidad : ' . $function_name . ' no se encuentra registrada</p></strong></h3>';
            $data->message = $message;
            return $data;
        }
    }

    catch(Exception $ex) {
        $message = '<h3><strong><p class="text-danger">Se presentó un inconveniente : ' . $ex . '</p></strong></h3>';
        $data->message = $message;
        return $data;
    }
}

/**
 * Función que retorna el nombre del rol de un usuario con el fin de mostrar al correspondiente interfaz en seguimiento_pilos
 * Returns an user role to show the appropiate interface in 'seguimiento_pilos'
 *
 * @param $userid --> user id
 * @param $instanceid --> instance id
 * @return Array containing role name for the given user 
 */
function get_name_role($idrol)
{
    global $DB;
    $sql_query = "SELECT nombre_rol FROM {talentospilos_rol} WHERE id='$idrol'";
    $consulta=$DB->get_record_sql($sql_query);
    return $consulta->nombre_rol;
}


/*
* Función que retorna el rol de un usuario
*
* @param $userid
* @param $instanceid
* @return Array
*/

function get_id_rol($userid, $blockid)
{
    global $DB;

    $current_semester = get_current_semester();
    $sql_query = "SELECT id_rol FROM {talentospilos_user_rol} WHERE id_usuario=$userid AND id_instancia=$blockid AND id_semestre=$current_semester->max  and estado=1";
    $consulta = $DB->get_record_sql($sql_query);


    return $consulta->id_rol;
}

/* Función que retorna si un rol de usuario determinado puede hacer una acción
* @see role_is_able($role_id,$action_id)
* @param $role_id --> id del rol
* @param $action_id --> id de la acción
* @return boolean
*/

function role_is_able($role_id, $action_id)
{
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_permisos_rol} where id_rol='$role_id' and id_accion='$action_id'";
    $consulta = $DB->get_record_sql($sql_query);
    if ($consulta) {
        return true;
    }
    else {
        return false;
    }
}

/* Función que retorna arreglo con las acciones que puede realizar un rol cuya funcionalidad es una especifica
* @see get_actions_by_role($id_functionality,$id_role)
* @param $id_functionality --> id del rol
* @param $id_role --> id de la acción
* @return Array
*/

function get_actions_by_role($id_functionality, $id_role)
{
    global $DB;
    $sql_query = "SELECT id_accion,nombre_accion  FROM {talentospilos_permisos_rol}  permisos INNER JOIN {talentospilos_accion}   accion ON permisos.id_accion = accion.id where id_funcionalidad='$id_functionality' and id_rol='$id_role' and estado=1";
    $consulta = $DB->get_records_sql($sql_query);
    return $consulta;
}







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

require_once(dirname(__FILE__).'/../role_management/role_management_lib.php');
require_once(dirname(__FILE__).'/user_lib.php');

if(isset($_POST['role']) && isset($_POST['username'])){

    if(!isset($_POST['function'])){
        $_POST['function'] = "";
    }

    if($_POST['function'] == 'verify_assign'){
        echo verify_user_assign($_POST['username'], $_POST['idinstancia']);
    } else if($_POST['role'] == 'profesional_ps' && isset($_POST['professional']) && isset($_POST['idinstancia'])){
        //Assign or update the professional role, echoing success or fail
        $success =  manage_role_profesional_ps($_POST['username'], $_POST['role'], $_POST['professional'], $_POST['idinstancia']);
        switch($success){
            case 1:
            echo "Rol profesional psicoeducativo asignado con éxito";
            break;

            case 2:
            echo "No se ha podido asignar el rol profesional psicoeducativo";
            break;

            case 3:
            echo "Rol psicoeducativo actualizado con éxito.";
            break;

            case 4:
            echo "Actualización de rol psicoeducativo fallida.";
            break;
        }
    //Assign or update the monitor role, echoing success or fail
    }else if($_POST['role'] == 'monitor_ps'  && isset($_POST['idinstancia'])){
        $success =  update_role_monitor_ps($_POST['username'], $_POST['role'], $_POST['students'], $_POST['boss'], $_POST['idinstancia']);

        switch($success){
            case 1:
                echo "Rol asignado con éxito";
                break;

            case 2:
                echo "No se ha podido asignar el rol";
                break;

            case 3:
                echo "Rol actualizado con éxito.";
                break;

            case 4:
                echo "Actualización de rol fallida(monitor)";
                break;
            default:
                echo $success;
                break;
        }
    //Assign or update the 'practicante' role, echoing success or fail
    }else if($_POST['role'] == 'practicante_ps' && isset($_POST['idinstancia'])){
        $success =  actualiza_rol_practicante($_POST['username'], $_POST['role'],$_POST['idinstancia'], 1 , null , $_POST['boss']);
        switch($success){
            case 1:
                echo "Rol asignado con éxito";
                break;

            case 2:
                echo "No se ha podido asignar el rol";
                break;

            case 3:
                echo "Rol actualizado con éxito.";
                break;

            case 4:
                echo "Actualización de rol fallida.ultimo";
                break;

            case 5:
                echo "El usuario no puede ser su mismo jefe";
                break;

            default:
                echo $success;
                break;
            }
    //Assign or update the given role, echoing success or fail
    }else if($_POST['role'] == 'director_prog' && isset($_POST['idinstancia'])){

        $success =  update_program_director($_POST['username'], $_POST['role'],$_POST['idinstancia'], 1, $_POST['academic_program']);
        switch($success){
            case 1:
                echo "Rol asignado con éxito";
                break;
            case 2:
                echo "No se ha podido asignar el rol";
                break;
            case 3:
                echo "Rol actualizado con éxito.";
                break;
            case 4:
                echo "Actualización de rol fallida.ultimo";
                break;
            case 5:
                echo "El usuario no puede ser su mismo jefe";
                break;
            default:
                echo $success;
                break;
            }
    }else{
        $success =  update_role_user($_POST['username'], $_POST['role'],$_POST['idinstancia']);

        switch($success){
            case 1:
                echo "Rol asignado con éxito";
                break;

            case 2:
                echo "No se ha podido asignar el rol";
                break;

            case 3:
                echo "Rol actualizado con éxito.";
                break;

            case 4:
                echo "Actualización de rol fallida.ultimo";
                break;

            default:
                echo $success;
                break;
            }
    }
}else if(isset($_POST['deleteStudent']) && isset($_POST['student']) && isset($_POST['username'])){
    echo dropStudentofMonitor($_POST['username'], $_POST['student']);

}else if(isset($_POST['deleteMonitorWithoutStudents'])&&isset($_POST['oldUser']) && isset($_POST['idinstancia'])){
        //disable old user
        // old user informations is storaged
        $oldUserArray =  json_decode($_POST['oldUser']);
        $oldUser = new stdClass();
        $oldUser->id = $oldUserArray[0];
        $oldUser->username = $oldUserArray[1];

        update_role_monitor_ps($oldUser->username, 'monitor_ps', array(), null,$_POST['idinstancia'], 0);
        echo 3;
}
else if(isset($_POST['changeMonitor']) && isset($_POST['oldUser']) && isset($_POST['newUser']) && isset($_POST['idinstancia']) ){
    $user_rol = get_user_rol(json_decode($_POST['newUser'])[1]);
    $isdelete = $_POST['isdelete'];

    // old user informations is storaged
    $oldUserArray =  json_decode($_POST['oldUser']);
    $oldUser = new stdClass();
    $oldUser->id = $oldUserArray[0];
    $oldUser->username = $oldUserArray[1];

    // new user information is storaged
    $newUserArray = json_decode($_POST['newUser']);
    $newUser = new stdClass();
    $newUser->id = $newUserArray[0];
    $newUser->username = $newUserArray[1];
    if($user_rol->nombre_rol !='monitor_ps'){

        //updates the role of the monitor for the new user
        update_role_monitor_ps($newUser->username, 'monitor_ps', array(), null,$_POST['idinstancia'], 1);

        //list of students in charge is updated
        changeMonitor($oldUser->id,  $newUser->id );

        //disable old user
        update_role_monitor_ps($oldUser->username, 'monitor_ps', array(), null,$_POST['idinstancia'], 0);
        echo 1;

    }else{
        //list of students in charge is updated
        changeMonitor($oldUser->id,  $newUser->id );

        //disable old user
        update_role_monitor_ps($oldUser->username, 'monitor_ps', array(), null,$_POST['idinstancia'], 0);
        echo 1;
    }
}else if(isset($_POST['deleteProfesional']) && isset($_POST['user']) && isset($_POST['idinstancia'])){
    try{
        //Deletes the professional role of the given user
        $newUserArray = json_decode($_POST['user']);
        $user = new stdClass();
        $user->id = $newUserArray[0];
        $user->username = $newUserArray[1];
        manage_role_profesional_ps($user->username, 'profesional_ps', 'ninguno',$_POST['idinstancia'],0);
        echo 1;
    }catch(Exception $e){
        echo $e->getMessage();
    }
}else if(isset($_POST['deleteOtheruser']) && isset($_POST['user']) && isset($_POST['idinstancia'])){
    try{

    $newUserArray = json_decode($_POST['user']);
    $user = new stdClass();
    $user->id = $newUserArray[0];
    $user->username = $newUserArray[1];
    $user->rol =  $newUserArray[2];
    update_role_user($user->username, $user->rol,$_POST['idinstancia'], 0);
    echo 1;

    }catch(Exception $e){
        echo $e->getMessage();
    }
}else{
    echo "Actualización de rol fallida";
}
?>

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

    require_once(dirname(__FILE__).'/../../../../config.php');
    require_once('user_lib.php');

    //Verifies wether a given user is registered and bring information from database or returns an array of users bosses. 
    if(isset($_POST['dat']) && isset($_POST['idinstancia']))
    {
        global $DB;
        $info_boss= new stdClass();
        $info_boss->firstname="";
        $info_boss->lastname="";

        $sql_query = "SELECT id, firstname, lastname, username, email FROM {user} WHERE username = '".$_POST['dat']."';";
        $info_user = $DB->get_record_sql($sql_query);

        if($info_user){
            $sql_query = "SELECT nombre_rol,id_jefe FROM {talentospilos_user_rol} INNER JOIN {talentospilos_rol} ON {talentospilos_user_rol}.id_rol = {talentospilos_rol}.id   WHERE {talentospilos_user_rol}.id_usuario = '".$info_user->id."' AND  {talentospilos_user_rol}.id_instancia=".$_POST['idinstancia']." AND {talentospilos_user_rol}.estado = 1 AND {talentospilos_user_rol}.id_semestre = (SELECT MAX(id)  FROM {talentospilos_semestre});";
            $rol_user = $DB->get_record_sql($sql_query);

            if(isset($rol_user->id_jefe)){
                if($rol_user->id_jefe != ""){
                    $sql_query = "SELECT firstname, lastname FROM {user} WHERE id =$rol_user->id_jefe";
                    $info_boss = $DB->get_record_sql($sql_query);    
                }
            }           

            if(!$rol_user){
                $info_user->rol = "";
            }else {
                $info_user->rol = $rol_user->nombre_rol;

                if($info_user->rol == 'practicante_ps' || $info_user->rol == 'monitor_ps'){

                    $info_user->boss = $rol_user->id_jefe;
                    $info_user->boss_name = $info_boss->firstname." ".$info_boss->lastname;
                    
                }               

                if($info_user->rol == 'profesional_ps'){
                    $sql_query = "SELECT nombre_profesional FROM {talentospilos_profesional} prof INNER JOIN {talentospilos_usuario_prof} userprof ON prof.id = userprof.id_profesional  WHERE userprof.id_usuario = ".$info_user->id.";";
                    $profesion = $DB->get_record_sql($sql_query);
                    if($profesion) $info_user->profesion = $profesion->nombre_profesional;
                }
            }
            echo json_encode($info_user);
        }else{
            $object =  new stdClass();
            $object->error = "Error al consultar la base de datos. El usuario con codigo ".$_POST['dat']." no se encuentra en la base de datos.";
            echo json_encode($object);
        }
        
        
    }else if(isset($_POST['function']) && isset($_POST['idinstancia'])){
        $jefes =  get_boss_users($_POST['role'],$_POST['idinstancia']);
        echo json_encode($jefes);
        //echo json_encode(get_professionals(null, $_POST['idinstancia']));
    }
?>
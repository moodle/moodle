<?php
    require_once(dirname(__FILE__).'/../../../config.php');
    require('query.php');

    if(isset($_POST['dat']) && isset($_POST['idinstancia']))
    {
        global $DB;

        $sql_query = "SELECT id, firstname, lastname, username, email FROM {user} WHERE username = '".$_POST['dat']."';";
        $info_user = $DB->get_record_sql($sql_query);
    
        if($info_user){
            $sql_query = "SELECT nombre_rol,id_jefe FROM {talentospilos_user_rol} INNER JOIN {talentospilos_rol} ON {talentospilos_user_rol}.id_rol = {talentospilos_rol}.id   WHERE {talentospilos_user_rol}.id_usuario = '".$info_user->id."' AND  {talentospilos_user_rol}.id_instancia=".$_POST['idinstancia']." AND {talentospilos_user_rol}.estado = 1 AND {talentospilos_user_rol}.id_semestre = (SELECT MAX(id)  FROM {talentospilos_semestre});";
            $rol_user = $DB->get_record_sql($sql_query);
            
            if($rol_user->id_jefe != ""){
                $sql_query = "SELECT firstname, lastname FROM {user} WHERE id =$rol_user->id_jefe";
                $info_boss = $DB->get_record_sql($sql_query);    
            }

            if(!$rol_user){
                $info_user->rol = "ninguno";
            }
            else {
                $info_user->rol = $rol_user->nombre_rol;
                $info_user->boss = $rol_user->id_jefe;
                $info_user->boss_name = $info_boss->firstname." ".$info_boss->lastname;
                if($info_user->rol == 'profesional_ps'){
                    $sql_query = "SELECT nombre_profesional FROM {talentospilos_profesional} prof INNER JOIN {talentospilos_usuario_prof} userprof 
                                        ON prof.id = userprof.id_profesional  WHERE userprof.id_usuario = ".$info_user->id.";";
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
        echo json_encode(getProfessionals(null, $_POST['idinstancia']));
    }
?>
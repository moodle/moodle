<?php  
require('query.php');

global $COURSE, $USER;

if(isset($_POST['page']) && isset($_POST['block']) ){
    
    $page = $_POST['page'];
    $blockid = $_POST['block'];
    
    //se obtiene  el contexto del bloque concurrente
    $contextblock =  context_block::instance($blockid);

    // se verifica si el rol del usuario concurrente tiene permisos de adicionar una instancia del bloque
    //si tiene permisos se le concede los permisos del usuario sistemas
    if (has_capability('block/ases:configurateintance', $contextblock)) {
        $role = checking_role($USER->username, $blockid);
        if($role->estado != 1 || $role->nombre_rol != "sistemas"){ //el estado 1 es estado activo
            update_role_user($USER->username, "sistemas",$blockid, 1);
        }
    }

    if ($rol_object = get_role_user($USER->id, $blockid)){
        $idrol = $rol_object->rolid;
        //print_r($page);
        if($result = get_permisos_role($idrol, $page)){
            echo json_encode($result);
        }else{
            $object = new stdClass();
            $object->error = "Tienes asignado el rol ".$rol_object->nombre_rol." el cual no tiene permisos en esta sección Consejo: Dirígete al Área de Sistemas de la oficina de ASES para gestionar tus permisos.";
            echo json_encode($object);
        }
    }else{
        $object = new stdClass();
        $object->error = "En el presente semestre NO tienes permisos en esta sección";
        $object->msg = "<p ALIGN=left><b>Consejo:</b> Dirígete al Área de Sistemas de la oficina de ASES para gestionar tus permisos.</p>";
        echo json_encode($object);
    }
}elseif(isset($_POST['estudiante_monitor']) && isset($_POST['idinstancia'])){
    global $USER;
    
    // 
    $rol_object = get_role_user($USER->id, $_POST['idinstancia']);
    $idrol = $rol_object->rolid;
    $result = new stdClass();
    $result->idrol = $idrol;
    $result->recibe = $_POST['estudiante_monitor'];

    if($idrol == 4){ //4 es el id correspondiente al rol monitor: se valida si el usuario es monitor
        $students =  new stdClass();
        $students = getStudentsGrupal($USER->id, $_POST['idinstancia']); // se obtiene la lista de estudiante acargo del monitor
        $esmonitorde = false;  
        
        //se evalua si por lo menos hay un estudiante, de los que tiene a cargo, que tenga el id equivalente al recibido, el cual esta en $_POST['estudiante_monitor']
        
        foreach($students as $r){
            array_push($idtalentos_list,$r->idtalentos);
            if($_POST['estudiante_monitor'] == $r->idtalentos){
                $esmonitorde = true;
            }
        }

        if($esmonitorde){
            $result->result = true;
            echo json_encode($result);
        }else{
            $result->result = false;
            echo json_encode($result);
        }

    }else{
        $result->result = true;
        echo json_encode($result);
    }
}

//verificar usuario administrador
 // se verifica si el usuario actual es adminnistrador
    $admins = get_admins();
    $isadmin = false; 
    foreach ($admins as $admin) { 
        if ($USER->id == $admin->id) { 
            $isadmin = true; break; 
            
        } 
        
    }
    //si es administrador le asigno los permisos del rol sistemas, el cual tiene todos los permisos
    if($isadmin){
        update_role_user($USER->username, "sistemas");
    
    }
  
?>
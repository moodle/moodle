<?php
require_once('query.php');

if(isset($_POST['function'])){
    
    switch($_POST['function']){
        case "loadMotivos":
            loadMotivos();
            break;
        case "saveMotivoRetiro":
            saveMotivoRetiroStudent();
            break;
        case "loadMotivoRetirostudent":
            loadMotivoRetiroStudent();
            break;
        default:
            $msg =  new stdClass();
            $msg->error = "Error :(";
            $msg->msg = "Error al comunicarse con el servidor. Verificar la función";
            echo json_encode($msg);
            break;
    }
    
}else{
    $msg =  new stdClass();
    $msg->error = "Error :(";
    $msg->msg = "Error al comunicarse con el servidor. No se reconoció la funcion a ejecutar";
    echo json_encode($msg);
}

function saveMotivoRetiroStudent(){
    if(isset($_POST['talentosid']) && isset($_POST['motivoid']) && isset($_POST['detalle']))
    {
        echo json_encode(saveMotivoRetiro($_POST['talentosid'], $_POST['motivoid'],$_POST['detalle']));
        
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al comunicarse con el servidor. No se encuentran las variebles necesarias para guardar el motivo retiro";
        echo json_encode($msg);
    }
}

function loadMotivos(){
    $result = getMotivosRetiros();
    $msg = new stdClass();
    $msg->size = count($result);
    $msg->data = $result;
    echo json_encode($msg);
}

function loadMotivoRetiroStudent(){
    if(isset($_POST['talentosid']))
    {
        echo json_encode(getMotivoRetiroEstudiante($_POST['talentosid']));
        
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al comunicarse con el servidor. No se encuentran las variebles necesarias para cargar el motivo retiro";
        echo json_encode($msg);
    }
}


?>
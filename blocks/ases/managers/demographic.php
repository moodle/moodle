<?php

require_once('query.php');

if(isset($_POST['function'])){
    
    switch($_POST['function']){
        case "loadCoordinates":
            loadCoordinates();
            break;
        case "load_neighborhood":
            load_neighborhood();
            break;
        case "save_info_geographic":
            save_info_geographic();
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
    $msg->msg = "Error al comunicarse con el servidor. No se reconoce la funcion a ejecutar";
    echo json_encode($msg);
}

function loadCoordinates(){
    if(isset($_POST['talentosid'])){
        echo json_encode(getCoordinates($_POST['talentosid']));
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al comunicarse con el servidor. No se reconoció la funcion a la variable talentosid";
        echo json_encode($msg);
    }
}

function load_neighborhood(){
    echo json_encode(get_neighborhood());
}

function save_info_geographic(){
    
    $save_geographic_info = save_geographic_info($_POST['latitude'], $_POST['length'], $_POST['id_neighborhood'], $_POST['id_student']);
    $save_geographic_risk = save_geographic_risk($_POST['id_student'], $_POST['geographic_risk']);
    
    if($save_geographic_info == '1' && $save_geographic_risk == '1'){
        echo '1';
    }else{
        echo '0';
    }
}


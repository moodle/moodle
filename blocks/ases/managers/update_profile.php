<?php
require_once('query.php');

if(isset($_POST['codigo']) && isset($_POST['cedula']) && isset($_POST['tipo_doc']) && isset($_POST['dir1']) && isset($_POST['tel1']) && isset($_POST['tel2']) && isset($_POST['tel3']) && isset($_POST['email2']) && isset($_POST['nombre_acudiente']) && isset($_POST['tel4']) && isset($_POST['observaciones']) && isset($_POST['estado']) && isset($_POST['estadoAses']))
 {
    $arrayColumns = array("num_doc","tipo_doc","direccion_res", "tel_ini", "tel_res","celular","emailpilos","acudiente","tel_acudiente", "observacion", "estado","estado_ases");
    $arrrValues = array($_POST['cedula'], $_POST['tipo_doc'], $_POST['dir1'],$_POST['tel1'], $_POST['tel2'], $_POST['tel3'], $_POST['email2'], $_POST['nombre_acudiente'], $_POST['tel4'],$_POST['observaciones'], $_POST['estado'], $_POST['estadoAses']);
    $id =  $_POST['codigo'];
    
    if (update_talentosusuario($arrayColumns,$arrrValues,$id)){
        $msg =  new stdClass();
        $msg->exito = "Exito!!";
        $msg->msg = "Se ha almacenado la información con exito!!";
        echo json_encode($msg);
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al guardar la información. <br><b>Posibles Causas:</b><br><b>*</b> Si cambiaste el número de cedula es posible que el nuevo número ya exista en la base de datos.<br><b>*</b>  O es posible que haya un error con el servidor. Vuelve a intentarlo";
        echo json_encode($msg);
    }
    
 }else{
    $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al comunicarse con el servidor. Update profile ";
        echo json_encode($msg);
 }  
?>
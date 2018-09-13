<?php
require_once('../managers/seguimiento_pilos/seguimientopilos_lib.php');

global $USER;

if(isset($_POST['type'])&&$_POST['type']=="getid") 
 {
    echo($USER->id);
}

if(isset($_POST['type'])&&$_POST['type']=="getName") 
 {
    echo($USER->username);
}

if(isset($_POST['type'])&&$_POST['type']=="getEmail") 
 {
    echo($USER->email);
}

if(isset($_POST['type'])&&$_POST['type']=="getRol"&&isset($_POST['instance'])&&isset($_POST['id'])) 
 { 
  $retorno = get_name_rol($_POST['id'],$_POST['instance']);
   echo($retorno);
}

if(isset($_POST['type'])&&$_POST['type']=="info_monitor"&&isset($_POST['id'])&&isset($_POST['instance'])) 
 {
  
    $retorno = get_seguimientos_monitor($_POST['id'],$_POST['instance']);
    echo (json_encode($retorno));
}

if(isset($_POST['type'])&&$_POST['type']=="eliminar_registro"&&isset($_POST['id'])) 
 {
  
   $retorno = eliminar_registro($_POST['id']);
   echo $retorno;
   
}

if(isset($_POST['type'])&&$_POST['type']=="actualizar_registro") 
 {
  $objeto =(object)$_POST['seguimiento'];
  $retorno = updateSeguimiento_pares($objeto);
  echo $retorno;
  
 }

if(isset($_POST['type'])&&$_POST['type']=="number_seg_monitor"&&isset($_POST['id'])&&isset($_POST['instance'])) 
 {
    $retorno = get_cantidad_seguimientos_monitor($_POST['id'],$_POST['instance']);
    echo (json_encode($retorno));
}

if(isset($_POST['type'])&&$_POST['type']=="info_practicante"&&isset($_POST['id'])) 
 {
    $retorno = get_monitores_practicante($_POST['id']);
    echo (json_encode($retorno));
}

if(isset($_POST['type'])&&$_POST['type']=="info_profesional"&&isset($_POST['id'])&&isset($_POST['instance'])) 
 {
    $retorno = get_practicantes_profesional($_POST['id'],$_POST['instance']);
    echo (json_encode($retorno));
}

if(isset($_POST['type'])&&$_POST['type']=="getProfesional"&&isset($_POST['instance'])&&isset($_POST['id'])) 
 { 
   $retorno = get_profesional_practicante($_POST['id'],$_POST['instance']);
   echo($retorno);
}


if(isset($_POST['type'])&&$_POST['type']=="send_email_to_user"&&isset($_POST['message'])&&isset($_POST['tipoSeg'])&&isset($_POST['codigoEnviarN1'])&&isset($_POST['codigoEnviarN2'])&&isset($_POST['fecha'])&&isset($_POST['nombre'])) 
{
    //Pendiente código 3
 echo send_email_to_user($_POST['tipoSeg'],$_POST['codigoEnviarN1'],$_POST['codigoEnviarN2'], 0,$_POST['fecha'],$_POST['nombre'],$_POST['message'], "");
}




<?php
require_once('query.php');
require "dateValidator.php";

$id = $_POST['codigo'];
if(isset($_POST['codigo'])){
    if($result = get_ases_user_by_code($id)){
        //se consulta enfasis en caso de qeu exista
        $enfasis = getEnfasisFinal($result->id);
        if($enfasis) {
            $result->nom_enfasis = $enfasis->nom_enfasis;
            
            if($idprog = $enfasis -> final_programa){
                $prog = getPrograma($prog);
                $result->cod_programa = $prog->cod_univalle;
                $result->nom_programa = $prog->nom_programa;
            }else{
                $result->cod_programa = "NO";
                $result->nom_programa = "REGISTRA";
            }
            
        }else{
            $result->nom_enfasis = "NO REGISTRA";
            $result->cod_programa = "NO";
            $result->nom_programa = "REGISTRA";
        }
        
        $result->age = substr($result->age,0,2);
        
        //retorna el resultado
        echo json_encode($result);    
    }else{
        $object = new stdClass();
        $object->error = "No existe un estudiante de pilos con el código: ".$id." en la base de datos";
        echo json_encode($object);
    }
    
}else{
    $object = new stdClass();
        $object->error = "Erro al obtener el codigo";
        echo json_encode($object);
}
?>
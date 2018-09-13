<?php
require('query.php');

if( isset($_POST['function']) ){
    switch ($_POST['function']){
        case 'saveprimerAcerca':
            upgradeprimerAcerca(0);
            break;
        case 'load':
            load('primerAcerca');
            break;
        case 'newAcompaSocio':
            upgradeAcompaSocio(0);
            break;
        case 'load_AcompaSocio':
            load('AcompaSocio');
            break;
        case 'updateAcompaSocio':
            upgradeAcompaSocio(1);
            break;
        case 'updatePrimerAcerca':
            upgradeprimerAcerca(1);
            break;
        case 'saveSegSocio':
            upgradeSegSocio(0);
            break;
        case 'updateSegSocio':
            upgradeSegSocio(1);
            break;
        case 'loadSegSocio':
            load('loadSegSocio');
            break;
        case 'loadJustOneSegSocio':
            load('loadJustOneSegSocio');
            break;
        case 'deleteEconomica':
            dropEcono();
            break;
        case 'deleteFamilia':
            dropFami();
            break;
        
        
    }
}else{
    echo "mla funcion";
}

function upgradeprimerAcerca($fun){
    if(isset($_POST['comp_familiar']) && isset($_POST['freetime']) && isset($_POST['motivo']) && isset($_POST['optradio']) && isset($_POST['idtalentos']) ){
        global $USER;
        
        //se guarda fecha de creacion actual
        date_default_timezone_set("America/Bogota");
        $today = time();
        
        $record = new stdClass();
        
        $record->id_profesionalps = $USER->id;
        $record->id_estudiante = $_POST['idtalentos'];
        $record->comp_familiar = $_POST['comp_familiar'];
        $record->observaciones = $_POST['freetime'];
        $record->motivo = $_POST['motivo'];
        $record->status = $_POST['optradio'];
  
        try{
            if($fun == 0){
                
                if(!isset($_POST['idinstancia'])) throw new Exception('No se reconocio las variblaes necesarias: idinstancia.'); 
                //se almacena solo una vez la instancia de donde se generó el registro
                $record->id_instancia = $_POST['idinstancia'];
                //se adiciona la fecha de cracion
                date_default_timezone_set("America/Bogota");
                $today = time();
                $record->created = $today;
                
                insertPrimerAcerca($record);
            }else{
                $record->id = $_POST['idPA'];
                updatePrimerAcerca($record);
            }
            
            $msg =  new stdClass();
            $msg->exito = "exito";
            $msg->msg = "se ha almacenado la informacion con exito";
            echo json_encode($msg);
            
        }catch(Exception $e){
            $msg =  new stdClass();
            $msg->error = "Error :(";
            $msg->msg = "error al actualizar<br>".$e->getMessage()."<br>".$fun.$r."<br>".pg_last_error();
            echo json_encode($msg);
        }
        
        
    }else{
        $msg =  new stdClass();
            $msg->error = "Error :(";
            $error_debug="";
            if(!isset($_POST['comp_familiar'])) $error_debug.="El campo composición familiar es requerido<br>"; 
            if(!isset($_POST['freetime'])) $error_debug.="El campo composición familiar es requerido<br>"; 
            if(!isset($_POST['motivo'])) $error_debug.="El campo motivo es requerido<br>"; 
            if(!isset($_POST['optradio'])) $error_debug.="Por favor califica el asercamiento<br>"; 
            if(!isset($_POST['idtalentos'])) $error_debug.="error de programador. obtener idtealentos<br>"; 
            
            $msg->msg = $error_debug;
            echo json_encode($msg);
    }

}


function load($seccion){
    if(isset($_POST['idtalentos']) && isset($_POST['idinstancia'])){
        
        $result = new stdClass();
        switch($seccion){
            case 'primerAcerca':
                $result = getPrimerAcerca($_POST['idtalentos'], $_POST['idinstancia']);
                break;
            case 'AcompaSocio':
                $result =  getAcompaSocio($_POST['idtalentos'], $_POST['idinstancia']);
                break;
            case 'loadSegSocio':
                echo json_encode(getSegSocioOrderBySemester($_POST['idtalentos'], $_POST['idinstancia']));
                return 0;
                break;
            case 'loadJustOneSegSocio':
                $result =  getSegSocio($_POST['idtalentos'], $_POST['idinstancia'], $_POST['idSegSocio']);
                
        }
        
        //se formatean las fechas
        foreach($result as $r){
            if($seccion == 'primerAcerca'){
                $r->fecha = date('d-m-Y', $r->created);
                $r->created = date('d-m-Y', $r->created);
                $r->act_status = $r->status;
                //id_profesionalps
                $user = getUserMoodleByid($r->id_profesionalps);
                $r->infoProfesional = $user->firstname." ".$user->lastname;
                
            }else if($seccion == 'AcompaSocio'){
                $r->fecha = date('Y-m-d', $r->fecha);
                $r->created = date('d-m-Y', $r->created);
                $r->ingresos =  getEconomia($_POST['idtalentos'], 'INGRESO');
                $r->egresos = getEconomia($_POST['idtalentos'], 'EGRESO');
                $r->familia = getFamilia($_POST['idtalentos']);
                $r->act_status = $r->status;
                $user = getUserMoodleByid($r->id_profesionalps);
                $r->infoProfesional = $user->firstname." ".$user->lastname;
                
                
            }else if($seccion == 'loadSegSocio' || $seccion == 'loadJustOneSegSocio'){
                $r->fecha = date('Y-m-d', $r->fecha);
                $r->created = date('d-m-Y', $r->created);
                $r->act_status = $r->status;
                
                $user = getUserMoodleByid($r->id_profesionalps);
                $r->infoProfesional = $user->firstname." ".$user->lastname;
            }
            
            
    
        }
        
        $msg =  new stdClass();
        $msg->result = $result;
        $msg->rows = count($result);    
            
        echo json_encode($msg);
  
    }
    
}

function upgradeAcompaSocio($fun){
    global $USER;
    if(isset($_POST['date']) && isset($_POST['psicologia']) && isset($_POST['teo']) && isset($_POST['tsocial']) && isset($_POST['composicionFamiliar']) && isset($_POST['dinamicaFamiliar']) && isset($_POST['apoyoFamiliar']) && isset($_POST['apoyoEducativo']) && isset($_POST['apoyoSocial']) && isset($_POST['apoyoLaboral']) && isset($_POST['observacionGeneral']) && isset($_POST['acuerdos']) && isset($_POST['optradio']) && isset($_POST['descripIngresos']) && isset($_POST['valorIngresos']) && isset($_POST['idIngresos']) && isset($_POST['descripEgresos']) && isset($_POST['valorEgresos']) && isset($_POST['idEgresos']) && isset($_POST['idFamilia']) && isset($_POST['nombreFamilia']) && isset($_POST['parentescoFamilia']) && isset($_POST['edadFamilia']) && isset($_POST['estadoCivilfamilia']) && isset($_POST['ocupacionFamilia']) && isset($_POST['telefonoFamilia']) && isset($_POST['idtalentos']) && isset($_POST['descripSeg']) ){
        

        $record = new stdClass();
        
        $record->id_estudiante = $_POST['idtalentos'];
        $record->id_profesionalps = $USER->id;
        $record->fecha = strtotime($_POST['date']);
        
        if(isset($_POST['motivo'])){
            $record->descripcion_antecedente = $_POST['motivo'];
        }else{
            $record->descripcion_antecedente = "";
        }
        
       
        $record->antecedente_psicosocial = $_POST['psicologia'];
        $record->antecedente_tsocial = $_POST['tsocial'];
        $record->antecedente_terapiao = $_POST['teo'];
        $record->comp_familiar = $_POST['composicionFamiliar'];
        $record->dinamica_familiar = $_POST['dinamicaFamiliar'];
        $record->red_familiar = $_POST['apoyoFamiliar'];
        $record->red_edu = $_POST['apoyoEducativo'];
        $record->red_social = $_POST['apoyoSocial'];
        $record->red_laboral = $_POST['apoyoLaboral'];
        
        if (isset($_POST['riesgo1'])){
            $record->fr_spa = 1;
            $record->fr_spa_observaciones  = $_POST['riesgo1'];
        }else{
            $record->fr_spa = 0;
            $record->fr_spa_observaciones  = "";
        }
        
        if (isset($_POST['riesgo2'])){
            $record->fr_embarazo = 1;
            $record->fr_embarazo_observaciones = $_POST['riesgo2'];
        }else{
            $record->fr_embarazo = 0;
            $record->fr_embarazo_observaciones = "";
        }
        
        if (isset($_POST['riesgo3'])){
            $record->fr_maltrato = 1;
            $record->fr_maltrato_observaciones = $_POST['riesgo3'];
        }else{
            $record->fr_maltrato = 0;
            $record->fr_maltrato_observaciones = "";
        }
        
        if (isset($_POST['riesgo4'])){
            $record->fr_abusosexual = 1;
            $record->fr_abusosexual_observaciones = $_POST['riesgo4'];
        }else{
            $record->fr_abusosexual = 0;
            $record->fr_abusosexual_observaciones = "";
        }
        
        if (isset($_POST['riesgo5'])){
            $record->fr_otros = 1;
            $record->fr_otros_observaciones = $_POST['riesgo5'];
        }else{
            $record->fr_otros = 0;
            $record->fr_otros_observaciones = "";
        }
        
        $record->observaciones = $_POST['observacionGeneral'];
        $record->acuerdos = $_POST['acuerdos'];
        $record->seguimiento = $_POST['descripSeg'];
        $record->status = $_POST['optradio'];
        
        
        //se formatea la información económica para almacenar
        $valorIngresos = explode(",", $_POST['valorIngresos']);
        $descripIngresos = explode(",",$_POST['descripIngresos']);
        $idIngresos = explode(",",$_POST['idIngresos']);
        $descripEgresos = explode(",", $_POST['descripEgresos']);
        $valorEgresos = explode(",",$_POST['valorEgresos']);
        $idEgresos =  explode(",",$_POST['idEgresos']);
        
        $infoEconomica = array(); //donde se almacenarán los objetos qe contienen la info socioeconomica
        $str = "";
        
        for ($i = 0 ; $i < count($descripIngresos) ; $i++){
            $object = new stdClass();
            $object->id = $idIngresos[$i];
            $object->id_estudiante = intval($record->id_estudiante);
            $object->concepto = $descripIngresos[$i];
            $object->monto = intval($valorIngresos[$i]);
            $object->tipo = 'INGRESO';
            
            
            array_push($infoEconomica, $object);
        }
        
        for ($i = 0 ; $i < count($descripEgresos) ; $i++){
            $object = new stdClass();
            $object->id = $idEgresos[$i];
            $object->id_estudiante = intval($record->id_estudiante);
            $object->concepto = $descripEgresos[$i];
            $object->monto = intval($valorEgresos[$i]);
            $object->tipo = 'EGRESO';
            
            
            array_push($infoEconomica, $object);
        }
        
        
        //se formatea la información  familiar
        $nombreFamilia = explode(",",$_POST['nombreFamilia']);
        $parentescoFamilia = explode(",",$_POST['parentescoFamilia']);
        $edadFamilia = explode(",",$_POST['edadFamilia']);
        $estadoCivilfamilia = explode(",",$_POST['estadoCivilfamilia']);
        $ocupacionFamilia = explode(",",$_POST['ocupacionFamilia']);
        $telefonoFamilia = explode(",",$_POST['telefonoFamilia']);
        $idFamilia = explode(",",$_POST['idFamilia']);
        
        $infoFamilia = array(); //donde se almacena la info de los objetos que conteinen la info de familiares
        
        for ($i = 0 ; $i < count($parentescoFamilia) ; $i++){
            $object =  new stdClass();
            $object->id = $idFamilia[$i];
            $object->id_estudiante = $record->id_estudiante;
            $object->nombre_pariente = $nombreFamilia[$i];
            $object->parentesco = $parentescoFamilia[$i];
            $object->ocupacion = $ocupacionFamilia[$i];
            $object->telefono = intval($telefonoFamilia[$i]);
            
            array_push($infoFamilia, $object);
        }
        
        try{
            if($fun == 0){
                
                if(!isset($_POST['idinstancia'])) throw new Exception('No se reconocio las variblaes necesarias: idinstancia.'); 
                //se almacena solo una vez la instancia de donde se generó el registro
                $record->id_instancia = $_POST['idinstancia'];
                
                //se adiciona la fecha de cracion
                date_default_timezone_set("America/Bogota");
                $today = time();
                $record->created = $today;
                
                //se eliminan ela tributo id de cada objeto
                foreach ($infoEconomica as $object){
                    unset($object->id);
                }
                
                foreach ($infoFamilia as $object){
                    unset($object->id);
                }
                
                insertnewAcompaSocio($record);
                insertInfoEconomica($infoEconomica);
                insertInfoFamilia($infoFamilia);

            }else{
                $record->id = $_POST['idAcompaSocio'];
                updateAcompaSocio($record);
                
                
                foreach ($infoEconomica as $object){
                    if($object->id != 0){
                        
                        updateInfoEconomica($object); 
                    }else{
                        unset($object->id);
                        insertInfoEconomica(array($object));
                    }
                }
                
                foreach ($infoFamilia as $object){
                    if($object->id != 0){
                        updateInfoFamilia($object); 
                    }else{
                        unset($object->id);
                        insertInfoFamilia(array($object));
                    }
                }
            }
            
            $msg =  new stdClass();
            $msg->msg = "Se ha actualizado correctamente";
            echo json_encode($msg);
            
        }catch(Exception $e){
            $msg =  new stdClass();
            $msg->error = "Error :(";
            $msg->msg = pg_last_error()."<br>".$e->getMessage();
            
            echo json_encode($msg);
        }
        
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "error al obtener la variables de acompañamiento socioeducativo";
        echo json_encode($msg);
    }
}

function upgradeSegSocio($fun){
     if(isset($_POST['date']) && isset($_POST['motivo']) && isset($_POST['seg']) && isset($_POST['optradio']) && isset($_POST['idtalentos']) ){
        global $USER;
        
        
        $record = new stdClass();
        
        $record->id_profesionalps = $USER->id;
        $record->id_estudiante = $_POST['idtalentos'];
        $record->seguimiento = $_POST['seg'];
        $record->motivo = $_POST['motivo'];
        $record->status = $_POST['optradio'];
        $record->fecha = strtotime($_POST['date']);
        
        try{
            if($fun == 0){
                
                if(!isset($_POST['idinstancia'])) throw new Exception('No se reconocio las variblaes necesarias: idinstancia.'); 
                //se almacena solo una vez la instancia de donde se generó el registro
                $record->id_instancia = $_POST['idinstancia'];
                
                //se adiciona la fecha de cracion
                date_default_timezone_set("America/Bogota");
                $today = time();
                $record->created = $today;
                insertSegSocio($record);
            }else{
                $record->id = $_POST['idSegSocio'];
                updateSegSocio($record);
            }
            
            $msg =  new stdClass();
            $msg->exito = "exito";
            $msg->msg = "se ha almacenado la informacion con exito";
            echo json_encode($msg);
            
        }catch(Exception $e){
            $msg =  new stdClass();
            $msg->error = "Error :(";
            $msg->msg = "error al actualizar<br>".$e->getMessage()."<br>".$fun.$r."<br>".pg_last_error();
            echo json_encode($msg);
        }
        
        
    }else{
        $msg =  new stdClass();
            $msg->error = "Error :(";
            $msg->msg = "error al obtener la variables";
            echo json_encode($msg);
    }
}

function dropEcono(){
    if(isset($_POST['idEco'])){
        try{
            dropInfoEconomica($_POST['idEco']);
            $msg =  new stdClass();
            $msg->exito = "exito";
            $msg->msg = "se ha actualizado la informacion con exito";
            echo json_encode($msg);
        }catch(Exception $e){
            $msg =  new stdClass();
            $msg->error = "Error :(";
            $msg->msg = $e->getMessage();
            echo json_encode($msg);
        }
        
        
    }
    
}

function dropFami(){
    if(isset($_POST['idFamilia'])){
        try{
            dropFamilia($_POST['idFamilia']);
            $msg =  new stdClass();
            $msg->exito = "exito";
            $msg->msg = "se ha actualizado la informacion con exito";
            echo json_encode($msg);
        }catch(Exception $e){
            $msg =  new stdClass();
            $msg->error = "Error :(";
            $msg->msg = $e->getMessage();
            echo json_encode($msg);
        }
        
        
    }
}

?>
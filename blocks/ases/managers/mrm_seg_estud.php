<?php
require_once(dirname(__FILE__). '/../../../config.php');
require_once('MyException.php');
require_once('dateValidator.php');
require_once('query.php');

if( isset($_FILES['file']) || isset($_POST['idinstancia'])){
    try {
        
        $archivo = $_FILES['file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        
        $nombre = $archivo['name'];
        
        $rootFolder    = "../view/archivos_subidos/mrm/seguimientos_estud/files/";
        $zipFolfer = "../view/archivos_subidos/mrm/seguimientos_estud/comprimidos/";
        
        //se limpian las carpetas
        deleteFilesFromFolder($rootFolder);
        deleteFilesFromFolder($zipFolfer);
        
        
        if ($extension !== 'csv') throw new MyException("El archivo ".$archivo['name']." no corresponde al un archivo de tipo CSV. Por favor verifícalo"); 
        if (!move_uploaded_file($archivo['tmp_name'], $rootFolder.'Original_'.$nombre)) throw new MyException("Error al cargar el archivo.");
        ini_set('auto_detect_line_endings', true);
        if (!($handle = fopen($rootFolder.'Original_'.$nombre, 'r'))) throw new MyException("Error al cargar el archivo ".$archivo['name'].". Es posible que el archivo se encuentre dañado");
        

        global $DB;
        
        $record = new stdClass();
        $count = 0;
        $wrong_rows = array();
        $success_rows =  array();
        
        
        $detail_erros = array();
        array_push($detail_erros,['No. linea - archivo original','No. linea - archivo registros erroneos','No. columna','Nombre Columna' ,'detalle error']);
        $line_count =2;
        $lc_wrongFile =2;
        
        $titlesPos = fgetcsv($handle, 0, ",");
        array_push($wrong_rows,$titlesPos);
        array_push($success_rows,$titlesPos);
        
        
        
        $associativeTitles = getAssociativeTitles($titlesPos);
        
        
        
        while($data = fgetcsv($handle, 0, ",")){
            $isValidRow = true;
            
            if($associativeTitles['seguimientoid'] !== null){
                if($data[$associativeTitles['seguimientoid']] == "" || !is_numeric($data[$associativeTitles['seguimientoid']])){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['seguimientoid']+ 1),'seguimientoid','El campo debe seer un valor numérico no nulo.']);
                }else{
                    $sql_query = "SELECT id from {talentospilos_seguimiento} where id='".$data[$associativeTitles['seguimientoid']]."';";
                    $result = $DB->get_record_sql($sql_query);
                    
                    if(!$result){
                        $isValidRow = false;
                        array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['seguimientoid']+ 1),'seguimientoid','El campo no corresponde  aun id en la tabla seguimiento.']);
                    }
                }
                
                
                
            }else{
                throw new MyException('El campo seguimiento es obligatorio');
            }
            
            $studentid =0;
            
            if($associativeTitles['username'] !== null){
                
                $result = get_userById(array('idtalentos'),$data[$associativeTitles['username']]);
                
                if(!$result){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['username']+ 1),'username','El campo no correponde a un username en la tabla usarios Ases.']);
                }else{
                   $studentid = $result->idtalentos;
                }
                
            }else{
                throw new MyException('El campo "username" es obligatorio');
            }
            
            
            
            if(!$isValidRow){
                $lc_wrongFile++;
                $line_count++;
                array_push($wrong_rows, $data);
                continue;
            }else{
                
                $record =  new stdClass();
                $record->id_estudiante = $studentid;
                $record->id_seguimiento = $data[$associativeTitles['seguimientoid']];
                
                $sql_query = "select id from {talentospilos_seg_estudiante} where id_estudiante=".$record->id_estudiante." AND id_seguimiento=".$record->id_seguimiento.";";
                $result = $DB->get_record_sql($sql_query);
                
                
                if(!$result) $DB->insert_record('talentospilos_seg_estudiante', $record);
                array_push($success_rows, $data);
                $line_count++;
                
            }
        
        }
        
        
        if(count($wrong_rows) > 1){
           
            $filewrongname = $rootFolder.'RegistrosErroneos_'.$nombre.'.csv';
            
            $wrongfile = fopen($filewrongname, 'w');                              
            fprintf($wrongfile, chr(0xEF).chr(0xBB).chr(0xBF)); // darle formato unicode utf-8
            foreach ($wrong_rows as $row) {
                fputcsv($wrongfile, $row);              
            }
            fclose($wrongfile);
            
            //----
            $detailsFilename = $rootFolder.'DetalleErrores_'.$nombre;
            
            $detailsFileHandler = fopen($detailsFilename, 'w');                              
            foreach ($detail_erros as $row) {
                fputcsv($detailsFileHandler, $row);              
            }
            fclose($detailsFileHandler);
            
        }
        
        if(count($success_rows) > 1){ //porque la primera fila es correponde a los titulos no datos
            $arrayIdsFilename = $rootFolder.'RegistrosExitosos_'.$nombre;
            
            $arrayIdsFileHandler = fopen($arrayIdsFilename, 'w');  
            fprintf($arrayIdsFileHandler, chr(0xEF).chr(0xBB).chr(0xBF)); // darle formato unicode utf-8
            foreach ($success_rows as $row) {
                fputcsv($arrayIdsFileHandler, $row);              
            }
            fclose($arrayIdsFileHandler);
            
            $response = new stdClass();
            
            if(count($wrong_rows) > 0){
                $response->warning = 'Archivo cargado con inconsistencias<br> Para mayor informacion descargar la carpeta con los detalles de inconsitencias.'; 
            }else{
                $response->success = 'Archivo cargado satisfactoriamente';
            }
            
            $zipname = $zipFolfer."detalle.zip";
            createZip($rootFolder, $zipname);
            
            $response->urlzip = "<a href='$zipname'>Descargar detalles</a>";
            
            echo json_encode($response);
            
        }else{
            $response = new stdClass();
            $response->error = "No se cargo el archivo. Para mayor informacion descargar la carpeta con los detalles de inconsitencias.";
            
            $zipname = $zipFolfer."detalle.zip";
            createZip($rootFolder, $zipname);
            
            $response->urlzip = '<p>Carpeta de detalles:<a href="'.$zipname.'"><span class="glyphicon glyphicon-download-alt " style="color:red;"></span></a></p>';
            
            echo json_encode($response);
        }
        
        
        
    } 
    catch(MyException $e){
        $msj =  new stdClass();
        $msj->error = $e->getMessage();
        echo json_encode($msj);
    }
    catch (Exception $e) {
        $msj =  new stdClass();
        $msj->error = $e->getMessage();
        echo json_encode($msj);
    }
}else{
    echo json_encode('no entro');
}

function getAssociativeTitles ($titlesPos){
    
    $associativeTitles = array();
    $count = 0;
    
    foreach ($titlesPos as $t){
        
        switch($t){
            case 'seguimientoid':
                $associativeTitles['seguimientoid'] = $count;
                break;
            case 'username':
                $associativeTitles['username'] = $count;
                break;
            default:
                throw new MyException('Error al cargar el archivo. El titulo "'.$t.'" no correponde a alguna columna valida');
        }
        
        $count++;
    }
    
    return $associativeTitles;
}



?>
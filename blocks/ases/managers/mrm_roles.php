<?php
require_once(dirname(__FILE__). '/../../../config.php');
require_once('MyException.php');
require_once('mass_management/massmanagement_lib.php');
require_once('role_management/role_management_lib.php');

if( isset($_FILES['file']) || isset($_POST['idinstancia'])){
    try {


        $archivo = $_FILES['file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        date_default_timezone_set("America/Bogota");
        $nombre = $archivo['name'];
  
        $rootFolder    = "../view/archivos_subidos/mrm/roles/files/";
        $zipFolfer = "../view/archivos_subidos/mrm/roles/comprimidos/";

        
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
        array_push($detail_erros,$titlesPos);
        array_push($success_rows,$titlesPos);
        
        $associativeTitles = getAssociativeTitles($titlesPos);
        
        
        
        
        while($data = fgetcsv($handle, 0, ",")){
            $isValidRow = true;
            $seguimientoid = 0;
            
            //*** Incio validaciones campos requeridos
            
            $username=0;
            //validacion existencia del usuario
            if($associativeTitles['username'] !== null){
                $sql_query = "SELECT username from {user} where username like '".$data[$associativeTitles['username']]."%';";
                $result = $DB->get_record_sql($sql_query);
                
                
                if(!$result){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['username'] + 1),'username','No existe un usuario asociado al username'.$data[ $associativeTitles['username'] ] ]);
                }else{
                    $username = $result->username;
                }
            }else{
                throw new MyException('el campo "username"  es obligatorio');    
            }
            
            
            //validacion existencia del usuario
            if($associativeTitles['rol'] !== null){
                
                //valida si el rol es permitido
                
                if(validateRole($data[$associativeTitles['rol']])){
                    
                    $sql_query = "SELECT id from {talentospilos_rol} where nombre_rol = '".$data[$associativeTitles['rol']]."';";
                    $result = $DB->get_record_sql($sql_query);
                    
                    
                    if(!$result){
                        $isValidRow = false;
                        array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['rol'] + 1),'rol','No existe un rol con el nombre'.$data[ $associativeTitles['rol'] ] ]);
                    }
                    
                    
                }else{
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['rol'] + 1),'rol','El rol '.$data[ $associativeTitles['rol'] ].' no es permitido' ]);
                }
                
            }else{
                throw new MyException('el campo "rol"  es obligatorio');    
            }
            
            $nombre_rol = null;
            if($associativeTitles['rol'] !== null){
                
                //valida si el rol es permitido
                
                if(validateRole($data[$associativeTitles['rol']])){
                    
                    $sql_query = "SELECT nombre_rol from {talentospilos_rol} where nombre_rol = '".$data[$associativeTitles['rol']]."';";
                    $result = $DB->get_record_sql($sql_query);
                    
                    
                    if(!$result){
                        $isValidRow = false;
                        array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['rol'] + 1),'rol','No existe un rol con el nombre'.$data[ $associativeTitles['rol'] ] ]);
                    }else{
                        $nombre_rol = $result->nombre_rol;
                    }
                    
                    
                }else{
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['rol'] + 1),'rol','El rol '.$data[ $associativeTitles['rol'] ].' no está permitido' ]);
                }
                
            }else{
                throw new MyException('el campo "rol"  es obligatorio');    
            }
            
            
            $username_jefe=null;
            //validacion existencia del usuario
            if($associativeTitles['jefe'] !== null){
                $sql_query = "SELECT username from {user} where username like '".$data[$associativeTitles['jefe']]."%';";
                $result = $DB->get_record_sql($sql_query);
                
                
                if(!$result){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['jefe'] + 1),'jefe','No existe un usuario asociado al username'.$data[ $associativeTitles['jefe'] ] ]);
                }else{
                    $username_jefe = $result->username;
                }
            }


            
            //** Fin validaciones Campos requeridos
            
            if(!$isValidRow){
                $lc_wrongFile++;
                $line_count++;
                array_push($wrong_rows, $data);
                continue;
            }else{
                
                $result = update_role_user($username, $nombre_rol, $_POST['idinstancia'],  1, null, $username_jefe );
                
                
                if($result != 4 && $result != 2){
                    array_push($success_rows, $data);
                }else{
                    array_push($detail_erros,[$line_count,$lc_wrongFile,'Error asignando rol','Error asignando rol','Error del server asignando rol' ]);
                    array_push($wrong_rows, $data);
                    $lc_wrongFile++;
                }
                
                
                
                $line_count++;
            }


        }


        if(count($wrong_rows) > 1){
           
            $filewrongname = $rootFolder.'RegistrosErroneos_'.$nombre;
            
            $wrongfile = fopen($filewrongname, 'w');                              
            fprintf($wrongfile, chr(0xEF).chr(0xBB).chr(0xBF)); // darle formato unicode utf-8
            foreach ($wrong_rows as $row) {
                fputcsv($wrongfile, $row);              
            }
            fclose($wrongfile);
            
            //----
            $detailsFilename =  $rootFolder.'DetallesErrores_'.$nombre;
            
            $detailsFileHandler = fopen($detailsFilename, 'w');
            fprintf($detailsFileHandler, chr(0xEF).chr(0xBB).chr(0xBF)); // darle formato unicode utf-8
            foreach ($detail_erros as $row) {
                fputcsv($detailsFileHandler, $row);              
            }
            fclose($detailsFileHandler);
            
        }
        
        if(count($success_rows) > 1){ //porque la primera fila corresponde a los titulos no datos
            $arrayIdsFilename =  $rootFolder.'RegistrosExitosos_'.$nombre;
            
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
            
            $response->urlzip = "<a href='$zipname'>Descargar detalles</a>";
            
            echo json_encode($response);
        }
        
        
        
    } 
    catch(MyException $e){
        $msj =  new stdClass();
        $msj->error = $e->getMessage().pg_last_error();
        echo json_encode($msj);
    }
    catch (Exception $e) {
        $msj =  new stdClass();
        $msj->error = $e->getMessage().pg_last_error();
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
            case 'username':
                $associativeTitles['username'] = $count;
                break;
            case 'rol':
                $associativeTitles['rol'] = $count;
                break;
            case 'jefe':
                $associativeTitles['jefe'] = $count;
                break;
            default:
                throw new MyException('Error al cargar el archivo. El titulo "'.$t.'" no corresponde a alguna columna valida');
        }
        
        $count++;
    }
    
    return $associativeTitles;
}

function validateRole($role){
    if($role != 'sistemas'){
        return true;
    }else{
        return false;
    }
}

?>
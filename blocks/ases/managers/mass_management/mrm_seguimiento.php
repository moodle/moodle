<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Ases block
 *
 * @author     John Lourido
 * @package    block_ases
 * @copyright  2017 John Lourido <jhonkrave@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');
require_once('../MyException.php');
require_once('massmanagement_lib.php');


if( isset($_FILES['file']) || isset($_POST['idinstancia'])){
    try {
        
        $archivo = $_FILES['file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
       
     
        $nombre = $archivo['name'];
        
        $rootFolder    = "../../view/archivos_subidos/mrm/seguimientos/files/";
        $zipFolfer = "../view/archivos_subidos/mrm/seguimientos/comprimidos/";


        //deletes everything from folders
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
        
        $arrayids =  array();
        array_push($arrayids, ['seguimientoid','registroid']);
        
        $detail_erros = array();
        array_push($detail_erros,['No. linea - archivo original','No. linea - archivo registros erroneos','No. columna','Nombre Columna' ,'detalle error']);
        $line_count =2;
        $lc_wrongFile =2;
        
        $titlesPos = fgetcsv($handle, 0, ",");
        
        $associativeTitles = getAssociativeTitles($titlesPos);
        array_push($wrong_rows,$titlesPos);
        
        
        
        while($data = fgetcsv($handle, 0, ",")){
            $isValidRow = true;
            $exists = false;
            $seguimientoid = 0;
            
            //*** begin validations on required fields
            
            //validation id
            if($associativeTitles['registroid'] !== null){
                if($data[$associativeTitles['registroid']] == "" || $data[$associativeTitles['registroid']] == "undefined"){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['registroid'] + 1),'registroid','El campo No puede ser vacio']);
                }
                
                $sql_query = "SELECT id from {talentospilos_seguimiento} where registroid='".$data[$associativeTitles['registroid']]."';";
                $result = $DB->get_record_sql($sql_query);
                if($result){
                    $exists = true;
                    $seguimientoid = $result->id;
                }
                
                
            }else{
                //print_r($associativeTitles['registroid']);
                throw new MyException('El campo "registroid" es un campo obligatorio');
            }
            
            $id_monitor=0;
            //validating user exists
            if($associativeTitles['email_monitor'] !== null){
                $sql_query = "SELECT id from {user} where email='".$data[$associativeTitles['email_monitor']]."';";
                $result = $DB->get_record_sql($sql_query);
                
                
                if(!$result){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['email_monitor'] + 1),'email_monitor','No existe un usuario asociado al correo'.$data[ $associativeTitles['email_monitor'] ] ]);
                }else{
                    $id_monitor = $result->id;
                }
            }elseif($associativeTitles['username'] !== null){
                $sql_query = "SELECT id from {user} where username like '".$data[$associativeTitles['username']]."%';";
                $result = $DB->get_record_sql($sql_query);
                
                
                if(!$result){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['username'] + 1),'username','No existe un usuario asociado al username'.$data[ $associativeTitles['email_monitor'] ] ]);
                }else{
                    $id_monitor = $result->id;
                }
            }else{
                throw new MyException('Por lo menos un campos de los siguientes es requerido: "username" o "email_monitor"');    
            }
            
            
            
            
            
            // 'created' field validation
            
            if($associativeTitles['created']!== null ){
                if (!is_numeric($data[$associativeTitles['created']]) || $data[$associativeTitles['created']] == "" ||  $data[$associativeTitles['created']] == "undefined"){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['created'] + 1),'created','El campo debe ser un valor numerico no nulo']);
                }
            }else{
                throw new MyException('El campo "created" es obligatorio');
            }
            
            
            
            //'hora ini' field validation
            if($associativeTitles['hora_ini']!== null){
                if(!preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $data[$associativeTitles['hora_ini']])){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['hora_ini']+ 1),'hora_ini','El campo no debe ser nulo y debe tener el formato HH:MM']);
                }
            }else{
                throw new MyException('El campo "hora_ini" es obligatorio');
            }
             
            
            
            //'hora_fin'  field validation
            if($associativeTitles['hora_fin']!== null){
                if(!preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $data[$associativeTitles['hora_fin']])){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['hora_fin'] + 1),'hora_fin','El campo debe tener el formato HH:MM']);
                }
            }else{
                throw new MyException('El campo "hora_fin" es obligatorio');
            }
            
            if($associativeTitles['tema']!== null){
                if($data[$associativeTitles['tema']] == "" || $data[$associativeTitles['tema']] == "undefined"){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['tema']+ 1),'tema','El campo No puede ser vacio']);
                }elseif(strlen($data[$associativeTitles['tema']]) > 500){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['tema']+ 1),'tema','El campo No debe superar los 500 caracteres.']);
                }
            }else{
                throw new MyException('El campo "tema" es obligatorio');
            }
            
            if($associativeTitles['objetivos']!== null){
                if($data[$associativeTitles['objetivos']] == "" || $data[$associativeTitles['objetivos']] == "undefined"){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['objetivos']+ 1),'objetivos','El campo No puede ser vacio']);
                
                }elseif(strlen($data[$associativeTitles['objetivos']]) > 500){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['objetivos']+ 1),'objetivos','El campo No debe superar los 500 caracteres.']);
                }
            }else{
                throw new MyException('El campo objetivos es obligatorio');
            }
            
            
            // if($associativeTitles['actividades']!== null && strtoupper($data[$associativeTitles['tipo']]) == 'PARES'){
            //     if($data[$associativeTitles['actividades']] == ""){
            //         $isValidRow = false;
            //         array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['actividades']+ 1),'actividades','El campo No puede ser vacio']);
            //     }
            // }
            
            if($associativeTitles['fecha']!== null){
                if($data[$associativeTitles['fecha']] == "" || $data[$associativeTitles['fecha']] == 'undefined'){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['fecha']+ 1),'fecha','El campo No puede ser vacio']);
                }
            }
            
            
            // if($associativeTitles['observaciones']!== null){
            //     if($data[$associativeTitles['observaciones']] == ""){
            //         $isValidRow = false;
            //         array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['observaciones'] + 1),'observaciones','El campo No puede ser vacio']);
            //     }
            // }
            
            if($associativeTitles['tipo']!== null){
                if($data[$associativeTitles['tipo']] == "" || (strtoupper($data[$associativeTitles['tipo']]) != 'PARES'  &&  strtoupper($data[$associativeTitles['tipo']]) != 'GRUPAL')){
                    $isValidRow = false;
                    array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['tipo'] + 1),'tipo','El campo No puede ser vacio y debe corresponder a untipo válido (PARES, GRUPAL)' ]);
                }
            }else{
                throw new MyException('El campo tipo es obligatorio');
            }
            
            
            
            if(!is_numeric($data[$associativeTitles['familiar_riesgo']]) && $data[$associativeTitles['familiar_riesgo']] != ''){
                $isValidRow = false;
                array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['familiar_riesgo'] + 1),'familiar_riesgo','El campo debe ser ser un valor numérico' ]);
            }
            
            if(!is_numeric($data[$associativeTitles['academico_riesgo']]) && $data[$associativeTitles['academico_riesgo']] != ''){
                $isValidRow = false;
                array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['academico_riesgo'] + 1),'academico_riesgo','El campo debe ser ser un valor numérico']);
            }
            
            if(!is_numeric($data[$associativeTitles['economico_riesgo']]) && $data[$associativeTitles['economico_riesgo']] != ''){
                $isValidRow = false;
                array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['economico_riesgo'] + 1),'economico_riesgo','El campo debe ser ser un valor numérico']);
            }
            
            if(!is_numeric($data[$associativeTitles['vida_uni_riesgo']]) && $data[$associativeTitles['vida_uni_riesgo']] != ''){
                $isValidRow = false;
                array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['vida_uni_riesgo'] + 1),'vida_uni_riesgo','El campo debe ser ser un valor numérico']);
            }
            
            if(!is_numeric($data[$associativeTitles['individual_riesgo']]) && $data[$associativeTitles['individual_riesgo']] != ''){
                $isValidRow = false;
                array_push($detail_erros,[$line_count,$lc_wrongFile,($associativeTitles['familiar_riesgo'] + 1),'individual_riesgo','El campo debe ser ser un valor numérico']);
            }
            

            
            
            
            //** End validations on required fields
            
            
            if(!$isValidRow){
                $lc_wrongFile++;
                $line_count++;
                array_push($wrong_rows, $data);
                continue;
            }else{
                $record = new stdClass();
                $record->id_monitor = $id_monitor;
                $record->created = $data[$associativeTitles['created']];
                $record->fecha = strtotime($data[$associativeTitles['fecha']]);
                $record->hora_ini = $data[$associativeTitles['hora_ini']];
                $record->hora_fin = $data[$associativeTitles['hora_fin']];
                $record->lugar = $data[$associativeTitles['lugar']];
                $record->tema = $data[$associativeTitles['tema']];
                $record->objetivos = $data[$associativeTitles['objetivos']];
                $record->actividades = $data[$associativeTitles['actividades']];
                $record->observaciones = $data[$associativeTitles['observaciones']];
                $record->tipo = strtoupper($data[$associativeTitles['tipo']]);
                $record->familiar_desc = $data[$associativeTitles['familiar_desc']];
                $record->familiar_riesgo = $data[$associativeTitles['familiar_riesgo']];
                $record->academico = $data[$associativeTitles['academico']];
                $record->academico_riesgo = $data[$associativeTitles['academico_riesgo']];
                $record->economico = $data[$associativeTitles['economico']];
                $record->economico_riesgo = $data[$associativeTitles['economico_riesgo']];
                $record->vida_uni = $data[$associativeTitles['vida_uni']];
                $record->vida_uni_riesgo = $data[$associativeTitles['vida_uni_riesgo']];
                $record->individual = $data[$associativeTitles['individual']];
                $record->individual_riesgo = $data[$associativeTitles['individual_riesgo']];
                $record->id_instancia = $_POST['idinstancia'];
                $record->registroid = $data[$associativeTitles['registroid']];
                $record->status=1;
                
                $generatedId = null;
                
                if(!$exists){
                    $generatedId = $DB->insert_record('talentospilos_seguimiento', $record, true);
                }else{
                    $record->id = $seguimientoid;
                    $generatedId = $seguimientoid;
                    $DB->update_record('talentospilos_seguimiento',$record);
                }
                
                array_push($arrayids, [$data[$associativeTitles['registroid']],$generatedId]);
                
                $line_count++;
            }
        
        }
        
        if(count($wrong_rows) > 1){
           
            $filewrongname = $rootFolder.'RegistrosErroneos_'.$nombre;
            
            $wrongfile = fopen($filewrongname, 'w');                              
            fprintf($wrongfile, chr(0xEF).chr(0xBB).chr(0xBF)); // feed utf-8 unicode format on
            foreach ($wrong_rows as $row) {
                fputcsv($wrongfile, $row);              
            }
            fclose($wrongfile);
            
            //----
            $detailsFilename =  $rootFolder.'DetallesErrores_'.$nombre;
            
            $detailsFileHandler = fopen($detailsFilename, 'w');
            fprintf($detailsFileHandler, chr(0xEF).chr(0xBB).chr(0xBF)); // feed utf-8 unicode format on
            foreach ($detail_erros as $row) {
                fputcsv($detailsFileHandler, $row);              
            }
            fclose($detailsFileHandler);
            
        }
        
        if(count($arrayids) > 1){ //First row are titles
            $arrayIdsFilename =  $rootFolder.'IdentificadoresSeguimientos_'.$nombre;
            
            $arrayIdsFileHandler = fopen($arrayIdsFilename, 'w');
            fprintf($arrayIdsFileHandler, chr(0xEF).chr(0xBB).chr(0xBF)); // feed utf-8 unicode format on
            foreach ($arrayids as $row) {
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

/**
 * Creates an associative array given a header from a CSV file
 * 
 * @see getAssociativeTitles ($titlesPos)
 * @param $titlesPos --> header from CSV 
 * @return array with 'email_monitor', 'created', 'fecha', 'hora_ini', 'hora_fin', 'lugar', 'tema', 'objetivos', 'actividades', 'familiar_desc', 'familiar_riesgo', 'academico', 'academico_riesgo', 'economico', 'economico_riesgo', 'vida_uni', 'vida_uni_riesgo', 'individual', 'individual_riesgo', 'registroid' and 'username' fields on it
 * 
 */
function getAssociativeTitles ($titlesPos){
    
    $associativeTitles = array();
    $count = 0;
    
    foreach ($titlesPos as $t){
        
        switch($t){
            case 'email_monitor':
                $associativeTitles['email_monitor'] = $count;
                break;
            case 'created':
                $associativeTitles['created'] = $count;
                break;
            case 'fecha':
                $associativeTitles['fecha'] = $count;
                break;
            case 'hora_ini':
                $associativeTitles['hora_ini'] = $count;
                break;
            case 'hora_fin':
                $associativeTitles['hora_fin'] = $count;
                break;
            case 'lugar':
                $associativeTitles['lugar'] = $count;
                break;
            case 'tema':
                $associativeTitles['tema'] = $count;
                break;
            case 'objetivos':
                $associativeTitles['objetivos'] = $count;
                break;
            case 'actividades':
                $associativeTitles['actividades'] = $count;
                break;
            case 'observaciones':
                $associativeTitles['observaciones'] = $count;
                break;
            case 'tipo':
                $associativeTitles['tipo'] = $count;
                break;
            case 'familiar_desc':
                $associativeTitles['familiar_desc'] = $count;
                break;
            case 'familiar_riesgo':
                $associativeTitles['familiar_riesgo'] = $count;
                break;
            case 'academico':
                $associativeTitles['academico'] = $count;
                break;
            case 'academico_riesgo':
                $associativeTitles['academico_riesgo'] = $count;
                break;
            case 'economico':
                $associativeTitles['economico'] = $count;
                break;
            case 'economico_riesgo':
                $associativeTitles['economico_riesgo'] = $count;
                break;
            case 'vida_uni':
                $associativeTitles['vida_uni'] = $count;
                break;
            case 'vida_uni_riesgo':
                $associativeTitles['vida_uni_riesgo'] = $count;
                break;
            case 'individual':
                $associativeTitles['individual'] = $count;
                break;
            case 'individual_riesgo':
                $associativeTitles['individual_riesgo'] = $count;
                break;
            case 'registroid':
                $associativeTitles['registroid'] = $count;
                break;
            case 'username':
                $associativeTitles['username'] = $count;
                break;
            default:
                throw new MyException('Error al cargar el archivo. El titulo "'.$t.'" no corresponde a alguna columna de la tabla seguimiento');
        }
        
        $count++;
    }
    
    return $associativeTitles;
}



?>
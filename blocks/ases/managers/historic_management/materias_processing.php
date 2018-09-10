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
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once dirname(__FILE__) . '/../../../../config.php';
require_once '../MyException.php';
require_once '../mass_management/massmanagement_lib.php';
require_once '../historic_management/historic_academic_lib.php';
 
if (isset($_FILES['file'])) {

    try {
        global $DB;
        $response = new stdClass();

        $archivo = $_FILES['file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        date_default_timezone_set("America/Bogota");
        $nombre = $archivo['name'];

        $rootFolder = "../../view/archivos_subidos/historic/academic/files/";
        $zipFolder = "../../view/archivos_subidos/historic/academic/comprimidos/";

        //validate and create folders
        if (!file_exists($rootFolder)) {
            mkdir($rootFolder, 0777, true);
        }
        if (!file_exists($zipFolder)) {
            mkdir($zipFolder, 0777, true);
        }

        //deletes everything from folders
        deleteFilesFromFolder($rootFolder);
        deleteFilesFromFolder($zipFolder);

        //validate extension
        if ($extension !== 'csv') {
            throw new MyException("El archivo " . $archivo['name'] . " no corresponde al un archivo de tipo CSV. Por favor verifícalo");
        }

        //validate and move file
        if (!move_uploaded_file($archivo['tmp_name'], $rootFolder . 'Original_' . $nombre)) {
            throw new MyException("Error al cargar el archivo.");
        }

        //validate and open file
        ini_set('auto_detect_line_endings', true);
        if (!($handle = fopen($rootFolder . 'Original_' . $nombre, 'r'))) {
            throw new MyException("Error al cargar el archivo " . $archivo['name'] . ". Es posible que el archivo se encuentre dañado");
        }

        //Control variables
        $wrong_rows = array();
        $success_rows = array();
        $detail_errors = array();
        $registros = array();

        //headers of error file
        array_push($detail_errors, ['No. linea - archivo original', 'No. linea - archivo registros erroneos', 'No. columna', 'Nombre Columna', 'detalle error']);

        $line_count = 2;
        $lc_wrongFile = 2;

        //headers of succes files
        $titlesPos = fgetcsv($handle, 0, ",");
        array_push($wrong_rows, $titlesPos);
        array_push($success_rows, $titlesPos);

        $associativeTitles = getAssociativeArray($titlesPos);

        while ($data = fgetcsv($handle, 0, ",")) {
            $isValidRow = true;
            /* VALIDATIONS OF FIELDS */

            //validate codigo_estudiante
            if (!is_null($associativeTitles['codigo_estudiante'])) {

                $codigo_estudiante = $data[$associativeTitles['codigo_estudiante']];

                if ($codigo_estudiante != '') {

                    $id_estudiante = get_ases_id_by_code($codigo_estudiante);
                    if (!$id_estudiante) {
                        $isValidRow = false;
                        array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['codigo_estudiante'] + 1), 'codigo_estudiante', 'No existe un estudiante ases asociado al codigo' . $data[$associativeTitles['codigo_estudiante']]]);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['codigo_estudiante'] + 1), 'codigo_estudiante', 'El campo codigo_estudiante es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo codigo_estudiante es obligatoria');
            }

            //validate programa
            if ($associativeTitles['programa'] != null) {
                $codigo_programa = $data[$associativeTitles['programa']];
                if ($codigo_programa != '') {

                    $id_programa = get_id_program($codigo_programa);
                    if (!$id_programa) {
                        $isValidRow = false;
                        array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['programa'] + 1), 'programa', 'No existe un programa asociado al codigo ' . $codigo_programa]);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['programa'] + 1), 'programa', 'El campo programa es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo programa es obligatoria');
            }

            //validate semestre
            if ($associativeTitles['semestre'] != null) {
                $semestre = $data[$associativeTitles['semestre']];
                if ($semestre != '') {

                    $id_semestre = get_id_semester($semestre);
                    if (!$id_semestre) {
                        $isValidRow = false;
                        array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['semestre'] + 1), 'semestre', 'No existe ningun semestre registrado el nombre' . $semestre]);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['semestre'] + 1), 'semestre', 'El campo semestre es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo semestre es obligatoria');
            }

            //validate nombre_materia
            if ($associativeTitles['nombre_materia'] != null) {

                $nombre_materia = $data[$associativeTitles['nombre_materia']];
                if ($nombre_materia === '') {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['nombre_materia'] + 1), 'nombre_materia', 'El campo nombre_materia es obligatorio y se encuentra vacio']);
                }
            } else {
                throw new MyException('La columna con el campo nombre_materia es obligatoria');
            }

            //validate codigo_materia
            if ($associativeTitles['codigo_materia'] != null) {

                $codigo_materia = $data[$associativeTitles['codigo_materia']];
                if ($codigo_materia === '') {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['codigo_materia'] + 1), 'codigo_materia', 'El campo codigo_materia es obligatorio y se encuentra vacio']);
                }
            } else {
                throw new MyException('La columna con el campo codigo_materia es obligatoria');
            }
            //validate creditos

            if ($associativeTitles['creditos'] != null) {

                $creditos = $data[$associativeTitles['creditos']];
                if ($creditos != '') {

                    if (!is_numeric($creditos)) {
                        $isValidRow = false;
                        array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['creditos'] + 1), 'creditos', 'El campo creditos debe ser de tipo numerico']);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['creditos'] + 1), 'creditos', 'El campo creditos es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo creditos es obligatoria');
            }
            
            $hasCancel = false;
            
            //validate fecha_cancelacion_materia
            if ($associativeTitles['fecha_cancelacion_materia'] != null) {

                $fecha_cancelacion_materia = $data[$associativeTitles['fecha_cancelacion_materia']];
                if ($fecha_cancelacion_materia != '') {
                    $hasCancel = true;
                    $fecha_cancelacion_materia = strtotime($fecha_cancelacion_materia);

                    if($fecha_cancelacion_materia == null){
                        $isValidRow = false;
                        array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['fecha_cancelacion_materia'] + 1), 'fecha_cancelacion_materia', 'El formato de fecha_cancelacion_materia es incorrecto. Recuerde que el formato debe ser dd-mm-yyyy']);    
                    }        
                }

            } else {
                throw new MyException('La columna con el campo fecha_cancelacion_materia es obligatoria');
            }

            // validate nota
            
            if ($associativeTitles['nota'] != null) {

                $nota = $data[$associativeTitles['nota']];
                if(!$hasCancel){
                    if ($nota != '') {

                        // if (!is_numeric($nota)) {
                        //     $isValidRow = false;
                        //     array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['nota'] + 1), 'nota', 'El campo nota debe ser de tipo numerico']);
                        // }
    
                    } else {
                        $isValidRow = false;
                        array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['nota'] + 1), 'nota', 'El campo nota es obligatorio y se encuentra vacio']);
                    }
                }

            } else {
                throw new MyException('La columna con el campo nota es obligatoria');
            }


            //FINALIZACION DE VALIDACIONES. 
            if (!$isValidRow) {
                $lc_wrongFile++;
                array_push($wrong_rows, $data);
                continue;
            } else {
                $key = "$id_estudiante-$id_semestre-$id_programa";
                
                //validate register
                if(!array_key_exists($key, $registros)){
                    $registros[$key] = array();
                }

                $materia = new stdClass;
                $materia->nombre_materia = $nombre_materia;
                $materia->codigo_materia = $codigo_materia;
                $materia->creditos = $creditos;
                $materia->nota = $nota;
                $materia->fecha_cancelacion_materia = $fecha_cancelacion_materia;

                array_push($registros[$key],$materia);
                array_push($success_rows,$data);
            }

            $line_count++;
        }

        //RECORRER ARREGLO DE REGISTRO PARA GENERAR JSON DE MATERIAS

        foreach ($registros as $key => $array_materias) {

            $data = explode("-", $key);
            
            $id_estudiante = $data[0];
            $id_semestre = $data[1];
            $id_programa = $data[2];
            
            $json_materias = json_encode($array_materias);

            //Actualizar o crear un registro
            $result = update_historic_materias($id_estudiante, $id_programa, $id_semestre, $json_materias);

            if (!$result) {
                array_push($detail_erros, [$line_count, $lc_wrongFile, 'Error al registrar historico', 'Error Servidor', 'Error del server registrando el historico']);
                array_push($wrong_rows, $data);
                $lc_wrongFile++;
            }else{

            }

        }
        //RECORRER LOS REGISTROS ERRONEOS Y CREAR ARCHIVO DE registros_erroneos

        if (count($wrong_rows) > 1) {

            $filewrongname = $rootFolder . 'RegistrosErroneos_' . $nombre;

            $wrongfile = fopen($filewrongname, 'w');
            fprintf($wrongfile, chr(0xEF) . chr(0xBB) . chr(0xBF)); // darle formato unicode utf-8
            foreach ($wrong_rows as $row) {
                fputcsv($wrongfile, $row);
            }
            fclose($wrongfile);

            //----
            $detailsFilename = $rootFolder . 'DetallesErrores_' . $nombre;

            $detailsFileHandler = fopen($detailsFilename, 'w');
            fprintf($detailsFileHandler, chr(0xEF) . chr(0xBB) . chr(0xBF)); // darle formato unicode utf-8
            foreach ($detail_erros as $row) {
                fputcsv($detailsFileHandler, $row);
            }
            fclose($detailsFileHandler);

        }
        //RECORRER LOS REGISTROS EXITOSOS Y CREAR ARCHIVO DE registros_exitosos
        if (count($success_rows) > 1) { //porque la primera fila corresponde a los titulos no datos
            $arrayIdsFilename = $rootFolder . 'RegistrosExitosos_' . $nombre;

            $arrayIdsFileHandler = fopen($arrayIdsFilename, 'w');
            fprintf($arrayIdsFileHandler, chr(0xEF) . chr(0xBB) . chr(0xBF)); // darle formato unicode utf-8
            foreach ($success_rows as $row) {
                fputcsv($arrayIdsFileHandler, $row);
            }
            fclose($arrayIdsFileHandler);

            $response = new stdClass();

            if (count($wrong_rows) > 1) {
                $response->warning = 'Archivo cargado con inconsistencias<br> Para mayor informacion descargar la carpeta con los detalles de inconsitencias.';
            } else {
                $response->success = 'Archivo cargado satisfactoriamente';
            }

            $zipname = $zipFolder . "detalle.zip";
            createZip($rootFolder, $zipname);

            $response->urlzip = "<a href='ases/$zipname'>Descargar detalles</a>";

            echo json_encode($response);

        } else {
            $response = new stdClass();
            $response->error = "No se cargo el archivo. Para mayor informacion descargar la carpeta con los detalles de inconsitencias.";

            $zipname = $zipFolder . "detalle.zip";
            createZip($rootFolder, $zipname);

            $response->urlzip = "<a href='ases/$zipname'>Descargar detalles</a>";

            echo json_encode($response);
        }

    } catch (MyException $e) {
        $msj = new stdClass();
        $msj->error = $e->getMessage() . pg_last_error();
        echo json_encode($msj);
    } catch (Exception $e) {
        $msj = new stdClass();
        $msj->error = $e->getMessage() . pg_last_error();
        echo json_encode($msj);
    }

} else {
    $msj = new stdClass();
    $msj->error = "No se recibio ningun archivo";
    echo json_encode($msj);
}

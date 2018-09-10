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
 * Talentos Pilos
 *
 * @author     John Lourido
 * @author     Juan Pablo Moreno Muñoz
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 John Lourido <jhonkrave@gmail.com>
 * @copyright  2017 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once dirname(__FILE__) . '/../../../../config.php';

require_once $CFG->dirroot . '/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot . '/blocks/ases/managers/MyException.php';
require_once $CFG->dirroot . '/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot . '/blocks/ases/managers/dateValidator.php';

if (isset($_FILES['csv_file'])) {

    try {
        $archivo = $_FILES['csv_file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre = $archivo['name'];
        $varSelector = $_POST['typefile_select'];
        if ($extension !== 'csv') {
            throw new MyException("El archivo " . $archivo['name'] . " no corresponde al un archivo de tipo CSV. Por favor verifícalo");
        }

        if (!move_uploaded_file($archivo['tmp_name'], "../../view/archivos_subidos/$nombre")) {
            throw new MyException("Error al cargar el archivo.");
        }

        ini_set('auto_detect_line_endings', true);
        if (!($handle = fopen("../../view/archivos_subidos/$nombre", 'r'))) {
            throw new MyException("Error al cargar el archivo " . $archivo['name'] . ". Es posible que el archivo se encuentre dañado");
        }

        //DB transaction begins
        pg_query("BEGIN") or die("Could not start transaction\n");
        //$transaction = $DB->start_delegated_transaction();

        if ($varSelector == "Municipio") {

            global $DB;
            $record = new stdClass();
            $count = 0;
            $array_id = array();
            $array_data = array();
            $line_count = 1;

            while ($data = fgetcsv($handle, 100, ",")) {
                array_push($array_data, $data);

                $query = "SELECT id FROM {talentospilos_departamento} WHERE codigodivipola = " . intval($data[1]) . ";";

                $result = $DB->get_record_sql($query);

                if (!$result) {
                    throw new MyException("Por favor Revisa la línea " . $line_count . ".<br>El codigo de División Política del departamento " . $data[1] . " asociado al  municipio " . $data[2] . " no se encuentra en la base de datos");
                }
                array_push($array_id, $result->id);
                $line_count += 1;
            }

            foreach ($array_data as $dat) {
                $record->codigodivipola = $dat[0];
                $record->cod_depto = $array_id[$count];
                $record->nombre = $dat[2];
                $DB->insert_record('talentospilos_municipio', $record, false);
                $count += 1;
            }

            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "Facultad") {
            global $DB;
            //This table does not depend on another
            $record = new stdClass();
            $count = 0;

            while ($data = fgetcsv($handle, 50, ",")) {
                $record->cod_univalle = $data[0];
                $record->nombre = $data[1];
                $DB->insert_record('talentospilos_facultad', $record, false);
                $count += 1;
            }

            $respuesta = 1;
            echo $respuesta;

        } else if ($varSelector == "Departamento") {
            global $DB;
            // This table does not depend on another
            $record = new stdClass();
            $count = 0;

            while ($data = fgetcsv($handle, 1000, ",")) {
                $record->codigodivipola = $data[0];
                $record->nombre = $data[1];

                $DB->insert_record('talentospilos_departamento', $record, false);
                $count += 1;
            }
            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "Sede") {
            global $DB;
            $record = new stdClass();
            $count = 0;
            $array_id = array();
            $array_data = array();
            $line_count = 1;

            while ($data = fgetcsv($handle, 100, ",")) {
                array_push($array_data, $data);

                $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='" . intval($data[0]) . "';";
                $result = $DB->get_record_sql($query);
                if (!$result) {
                    throw new MyException("Por favor revisa la linea" . $line_count . ".<br>El codigo de División Política de la ciudad " . $data[0] . " asociado a la sede" . $data[2] . " no se encuentra en la base de datos");
                }
                array_push($array_id, $result->id);
                $line_count += 1;
            }

            foreach ($array_data as $data) {
                $record->id_ciudad = $array_id[$count];
                $record->cod_univalle = $data[1];
                $record->nombre = $data[2];

                $DB->insert_record('talentospilos_sede', $record, false);
                $count += 1;
            }
            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "Programa") {
            global $DB;
            $record = new stdClass();
            $count = 0;
            $array_id_sede = array();
            $array_id_fac = array();
            $array_data = array();
            $line_count = 1;

            while ($data = fgetcsv($handle, 1000, ",")) {

                array_push($array_data, $data);

                $query = "SELECT id FROM {talentospilos_sede} WHERE cod_univalle ='" . intval($data[3]) . "';";
                $result = $DB->get_record_sql($query);
                if (!$result) {
                    throw new MyException("Por favor revisa la linea " . $line_count . ".<br>El codigo Univalle de la sede " . $data[0] . " asociado al programa " . $data[2] . " no se encuentra en la base de datos");
                }
                array_push($array_id_sede, $result->id);

                //faculty code is verified
                $query = "SELECT id FROM {talentospilos_facultad} WHERE cod_univalle ='" . $data[4] . "';";
                $result = $DB->get_record_sql($query);
                if (!$result) {
                    throw new MyException("El codigo Univalle de la facultad " . intval($data[4]) . " asociado al programa " . $data[2] . " no se encuentra en la base de datos. linea " . $line_count . " " . $data[0] . "-" . $data[1] . "-" . $data[2] . "-" . $data[3] . "-" . $data[4] . "");
                }
                $line_count += 1;
                array_push($array_id_fac, $result->id);
            }

            foreach ($array_data as $data) {
                $record->codigosnies = $data[0];
                $record->cod_univalle = $data[1];
                $record->nombre = $data[2];
                $record->id_sede = $array_id_sede[$count];
                $record->id_facultad = $array_id_fac[$count];
                $record->jornada = $data[5];

                $DB->insert_record('talentospilos_programa', $record, false);
                $count += 1;
            }
            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "Discapacidad") {
            global $DB;
            // Does not depend on any table
            $record = new stdClass();
            $count = 0;

            while ($data = fgetcsv($handle, 100, ",")) {
                $record->codigo_men = $data[0];
                $record->nombre = $data[1];

                $DB->insert_record('talentospilos_discap_men', $record, false);
                $count += 1;
            }
            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "Usuario") {
            global $DB;
            $record = new stdClass();
            $dateValidator = new dateValidator();
            $count = 0;
            $array_id_ciudadini = array(); // initial city
            $array_id_ciudadres = array(); // City of residence
            $array_id_ciudadnac = array(); // Birthplace
            $array_id_discap = array(); //disability
            $array_id_talentos = array();
            $array_data = array();
            $line_count = 0;
            $exists = true; // Verifies any record existence

            while ($data = fgetcsv($handle, 10000, ",")) {
                // Verifies record existence to confirm wheter it's an insertion or an update
                $query = "SELECT id FROM {talentospilos_usuario} WHERE num_doc_ini ='" . intval($data[1]) . "';";
                $result = $DB->get_record_sql($query);

                if (!$result) {
                    $exists = false;
                } else {
                    $array_id_talentos[$line_count] = $result->id;
                }

                // Birthdate format is verified
                $dateValidator->validateDateStyle($data[16]);

                //line information is stored
                array_push($array_data, $data);

                // In case it doesn't exist, the requeried information is obtained to insert
                if (!$exists) {
                    // initial city information is verified
                    $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='" . intval($data[6]) . "';";
                    $result = $DB->get_record_sql($query);
                    if (!$result) {
                        throw new MyException("Por favor revisa la linea " . ($line_count + 1) . ".<br>El codigo de Divsion politica " . $data[6] . " asociado a la ciudad de procedencia del estudiante con número de  identificación: " . $data[3] . " no se encuentra en la base de datos." . $data[0] . "-" . $data[1] . "-" . $data[2] . "-" . $data[3] . "-" . $data[4] . "-" . $data[5] . "-" . $data[6] . "-");
                    }
                    $array_id_ciudadini[$line_count] = $result->id;

                    //City of residence is verified
                    $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='" . intval($data[10]) . "';";
                    $result = $DB->get_record_sql($query);
                    if (!$result) {
                        throw new MyException("Por favor revisa la linea " . ($line_count + 1) . ".<br>El codigo de division politica " . $data[10] . " asociado a la ciudad de recidencia del estudiante con número de identificación:" . $data[3] . " no se encuentra en la base de datos");
                    }
                    $array_id_ciudadres[$line_count] = $result->id;

                    //Birthplace is verified
                    $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='" . intval($data[17]) . "';";
                    $result = $DB->get_record_sql($query);
                    if (!$result) {
                        throw new MyException("Por favor revisa la linea " . ($line_count + 1) . ".<br>El codigo de division politica " . $data[17] . " asociado a la ciudad de nacimiento del estudiante con número de identificación:" . $data[3] . " no se encuentra en la base de datos");
                    }
                    $array_id_ciudadnac[$line_count] = $result->id;

                    //disability code is verified
                    $query = "SELECT id FROM {talentospilos_discap_men} WHERE codigo_men ='" . intval($data[24]) . "';";
                    $result = $DB->get_record_sql($query);
                    if (!$result) {
                        throw new MyException("Por favor revisa la linea " . ($line_count + 1) . ".<br>El codigo  " . $data[24] . " asociado a la discapacidad del estudiante con número de identificación:" . $data[3] . " no se encuentra en la base de datos");
                    }
                    $array_id_discap[$line_count] = $result->id;
                }

                $line_count += 1;
            }
            foreach ($array_data as $data) {
                $record->tipo_doc_ini = $data[0];
                $record->num_doc_ini = intval($data[1]);
                $record->tipo_doc = $data[2];
                $record->num_doc = intval($data[3]);
                $record->dir_ini = $data[4];
                $record->barrio_ini = $data[5];

                $record->tel_ini = $data[7];
                $record->direccion_res = $data[8];
                $record->barrio_res = $data[9];

                $record->tel_res = $data[11];
                $record->celular = $data[12];
                $record->emailpilos = $data[13];
                $record->acudiente = $data[14];
                $record->tel_acudiente = $data[15];
                $record->fecha_nac = $data[16];

                $record->sexo = $data[18];
                $record->colegio = $data[19];
                $record->estamento = $data[20];
                $record->observacion = $data[21];
                $record->estado = $data[22];
                $record->estado_icetex = 0;
                $record->grupo = $data[23];

                $record->ayuda_discap = $data[25];

                //Inserts or update

                if ($exists) {
                    $record->id_ciudad_ini = $data[6];
                    $record->id_ciudad_res = $data[10];
                    $record->id_ciudad_nac = $data[17];
                    $record->id_discapacidad = $data[24];
                    $record->id = $array_id_talentos[$count];
                    $DB->update_record('talentospilos_usuario', $record);
                } else {
                    $record->id_ciudad_ini = $array_id_ciudadini[$count];
                    $record->id_ciudad_res = $array_id_ciudadres[$count];
                    $record->id_ciudad_nac = $array_id_ciudadnac[$count];
                    $record->id_discapacidad = $array_id_discap[$count];
                    $id_register = $DB->insert_record('talentospilos_usuario', $record, true);

                    $state_ases = $DB->get_record_sql("SELECT id FROM {talentospilos_estados_ases} WHERE nombre = 'seguimiento'")->id;

                    $record = new stdClass;
                    $record->id_estudiante = $id_register;
                    $record->id_estado_ases = $state_ases;
                    $record->fecha = time();
                    $DB->insert_record('talentospilos_est_estadoases', $record, false);

                    $state_icetex = $DB->get_record_sql("SELECT id FROM {talentospilos_estados_icetex} WHERE nombre = '5. IES renovó, ICETEX pendiente de giro'")->id;

                    $record = new stdClass;
                    $record->id_estudiante = $id_register;
                    $record->id_estado_icetex = $state_icetex;
                    $record->fecha = time();
                    $DB->insert_record('talentospilos_est_est_icetex', $record, false);
                }

                $count += 1;
            }
            $respuesta = '1';
            echo $respuesta;

        } else if ($varSelector == "user") {

            global $DB;
            $count = 0;
            $array_username = array();
            $array_programa = array();
            $array_data = array();
            $line_count = 1;
            $act = "";
            $query = "";

            while ($data = fgetcsv($handle, 500, ",")) {
                $temp_array = array();
                //document number is verified
                $query = "SELECT id FROM {talentospilos_usuario} WHERE num_doc = '" . intval($data[0]) . "';";
                $result = $DB->get_record_sql($query);

                if (!$result) {
                    throw new MyException("Por favor revisa la línea " . $line_count . ".<br>El número de documento " . $data[0] . " no corresponde a un estudiante de pilos");
                }

                $id_talentos = $result->id;

                // talentospilos_usuario table id is added according to the document number
                array_push($temp_array, $id_talentos);

                // program is verified
                $query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle = " . intval($data[2]) . " AND  jornada='" . $data[3] . "' AND id_sede = (SELECT id from {talentospilos_sede} WHERE cod_univalle =" . intval($data[4]) . ");";
                $result = $DB->get_record_sql($query);

                if (!$result) {
                    throw new MyException("Por favor revisa la línea " . $line_count . ".<br>El programa con las siguientes características; codigo univalle: " . $data[2] . ", jornada: " . $data[3] . "  y sede: " . $data[4] . " no existe en la base de datos. ");
                }
                array_push($temp_array, $result->id);

                //username is verified
                $username = substr($data[1], -7) . "-" . $data[2];
                $query = "SELECT id FROM {user} WHERE username = '" . $username . "' ;";
                $result = $DB->get_record_sql($query);

                if (!$result) {
                    throw new MyException("Por favor revisa la linea " . $line_count . ".<br>No existe un ususario en moodle para el estudiante con codigo univalle: " . $data[1] . " y programa " . $data[2] . ". ");
                }
                array_push($temp_array, $result->id);

                // remainder information is added to a temporary array that contains (id_talentos,id_programa,id_user, ACTIVO)
                // Query to program_status id
                $program_status = $DB->get_record_sql("SELECT id FROM {talentospilos_estad_programa} WHERE nombre = 'ACTIVO' ")->id;
                array_push($temp_array, $program_status);

                //validate tracking_status

                $query = "SELECT * FROM {talentospilos_user_extended} WHERE id_ases_user = $id_talentos";
                $result = $DB->get_records_sql($query);

                if (!$result) {
                    $tracking_status = 1;
                } else {
                    $tracking_status = 0;
                }

                array_push($temp_array, $tracking_status);

                //Previous temporary array is added to the array that contains the general information
                array_push($array_data, $temp_array);

                $line_count += 1;
            }

            foreach ($array_data as $dat) {
                $record = new stdClass();
                // If the field is created it'd be an insert, update otherwise
                $query = "SELECT id FROM {talentospilos_user_extended} WHERE id_moodle_user = " . $dat[2];
                //$query="select  d.id, f.shortname  from {user_info_data} d inner join {user_info_field} f on d.fieldid= f.id  where (f.shortname='idtalentos' OR f.shortname='idprograma' OR f.shortname='estado') AND userid =".$dat[2]." order by shortname;";
                $result = $DB->get_records_sql($query);
                if (!$result) {
                    //dat[2] --> user id on moodle user table
                    $record->id_moodle_user = $dat[2];
                    //dat[0] --> user id on moodle talentos table
                    $record->id_ases_user = $dat[0];
                    //dat[1] --> program id
                    $record->id_academic_program = $dat[1];
                    //dat[3] --> status
                    $record->program_status = $dat[3];
                    //dat[4] --> tracking_status
                    $record->tracking_status = $dat[4];

                    $DB->insert_record('talentospilos_user_extended', $record);

                    //METODO USANDO MODELO ANTIGUO.(usando user_info_data)
                    //   $act .= "(" . $dat[2] . "," . $idestado_field . ")-";
                    //   //se inserta la info del id del usuario de la tabla talentos en el campo idtalentos asociado a la tabla user
                    //   $record->userid = $dat[2]; //id del usario en moodle
                    //   $record->fieldid = $idtalentos_field;
                    //   //data[0] es el id del usario de tabla talentos
                    //   $record->data = $dat[0];
                    //   $record->dataformat = 0;
                    //   $DB->insert_record('user_info_data', $record);

                    //   //se inserta la info del campo idprograma
                    //   $record->userid = $dat[2];
                    //   $record->fieldid = $idprograma_field;
                    //   //data[1] es el id del programa de la tabla talentospilosprograma
                    //   $record->data = $dat[1];
                    //   $record->dataformat = 0;
                    //   $DB->insert_record('user_info_data', $record);

                    //   //se inserta la info del campo estado
                    //   $record->userid = $dat[2];
                    //   $record->fieldid = $idestado_field;
                    //   //data[3] es el estado que por defeto es activo
                    //   $record->data = $dat[3];
                    //   $record->dataformat = 0; //campo necesario para guradar coherencia con la tabla user_info_data
                    //   $DB->insert_record('user_info_data', $record);

                } else {
                    $record->id = $value->id;
                    //dat[2] user id on moodle user table
                    $record->id_moodle_user = $dat[2];
                    //dat[0] user id on moodle talentos table
                    $record->id_ases_user = $dat[0];
                    //dat[1] program id
                    $record->id_academic_program = dat[1];
                    //dat[3] status
                    $record->id_academic_program = dat[3];
                    //dat[4] --> tracking_status
                    $record->tracking_status = $dat[4];

                    $DB->update_record('talentospilos_user_extended', $record);
                    // foreach ($result as $value) {

                    //     //METODO USANDO MODELO ANTIGUO.(usando user_info_data)

                    //     // $shortname = $value->shortname;

                    //     // if ($shortname == 'idtalentos') {

                    //     //     $record->id = $value->id; //se asigna el id que correponde a la informacion del campo a actualizar
                    //     //     $record->data = $dat[0]; //se actualiza la informacion con la info de la tabla

                    //     // } else if ($shortname == 'idprograma') {

                    //     //     $record->id = $value->id; //
                    //     //     $record->data = $dat[1];
                    //     // } else if ($shortname == 'estado') {

                    //     //     $record->id = $value->id;
                    //     //     $record->data = $dat[3];
                    //     // }
                    //     // $DB->update_record('user_info_data', $record);
                    // }
                    $count += 1;
                }
            }

            $respuesta = 1;
            echo $respuesta;
        }
        // loading roles
        else if ($varSelector == "Roles") {
            global $DB;
            $record = new stdClass();

            while ($data = fgetcsv($handle, 100, ",")) {
                $record->nombre_rol = $data[0];
                $record->descripcion = $data[1];

                $DB->insert_record('talentospilos_rol', $record, false);
            }
            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "Funcionalidad") {
            global $DB;
            $record = new stdClass();

            while ($data = fgetcsv($handle, 100, ",")) {
                $record->nombre_func = $data[0];
                $record->descripcion = $data[1];

                $DB->insert_record('talentospilos_funcionalidad', $record, false);
            }
            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "Accion") {
            global $DB;
            $record = new stdClass();

            while ($data = fgetcsv($handle, 100, ",")) {
                $record->nombre_accion = $data[0];
                $record->descripcion = $data[1];
                $record->estado = $data[2];

                $query = "SELECT id FROM {talentospilos_funcionalidad} WHERE nombre_func = '" . $data[3] . "';";

                $result = $DB->get_record_sql($query);

                $record->id_funcionalidad = $result->id;

                $DB->insert_record('talentospilos_accion', $record, false);
            }
            $respuesta = 1;
            echo $respuesta;

        } else if ($varSelector == "Permisos") {
            global $DB;
            $record = new stdClass();

            while ($data = fgetcsv($handle, 100, ",")) {
                $record->permiso = $data[0];
                $record->descripcion = $data[1];

                $DB->insert_record('talentospilos_permisos', $record, false);
            }
            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "Permisos-Rol") {
            global $DB;
            $record = new stdClass();

            while ($data = fgetcsv($handle, 100, ",")) {
                // Getting id from field: campos, permiso, rol, funcionalidad.
                $result = $DB->get_record_sql("SELECT id FROM {talentospilos_rol} WHERE  nombre_rol = '" . $data[0] . "'");
                $record->id_rol = $result->id;

                $result = $DB->get_record_sql("SELECT id FROM {talentospilos_accion} WHERE nombre_accion = '" . $data[1] . "'");
                $record->id_accion = $result->id;

                $DB->insert_record('talentospilos_permisos_rol', $record, false);
            }

            $respuesta = 1;
            echo $respuesta;

        } else if ($varSelector == "Enfasis") {
            global $DB;
            $record = new stdClass;
            $count = 0;

            while ($data = fgetcsv($handle, 100, ",")) {

                $record->nombre = $data[0];
                $record->descripcion = $data[1];
                $DB->insert_record('talentospilos_enfasis', $record);
                $count += 1;
            }
            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "profesional") {

            global $DB;
            $record = new stdClass();

            while ($data = fgetcsv($handle, 100, ",")) {
                $record->nombre_profesional = $data[0];

                $DB->insert_record('talentospilos_profesional', $record, false);
            }

            $respuesta = 1;
            echo $respuesta;

        } else if ($varSelector == "Semestre") {

            global $DB;
            $record = new stdClass();

            while ($data = fgetcsv($handle, 100, ",")) {
                $record->nombre = $data[0];
                $record->fecha_inicio = $data[1];
                $record->fecha_fin = $data[2];
                $DB->insert_record('talentospilos_semestre', $record);
            }
            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "Barrios") {

            global $DB;
            $record = new stdClass();

            while ($data = fgetcsv($handle, 100, ",")) {

                $sql_query = "SELECT id FROM {talentospilos_barrios} WHERE cod_barrio = $data[0]";
                $id_barrio = $DB->get_record_sql($sql_query)->id;

                if ($id_barrio) {
                    $record->id = $id_barrio;
                    $record->cod_barrio = (int) $data[0];
                    $record->cod_comuna = $data[1];
                    $record->nombre = $data[2];
                    $record->estrato = $data[3];
                    $DB->update_record('talentospilos_barrios', $record);
                } else {
                    $record->cod_barrio = (int) $data[0];
                    $record->cod_comuna = $data[1];
                    $record->nombre = $data[2];
                    $record->estrato = $data[3];
                    $DB->insert_record('talentospilos_barrios', $record);
                }

            }
            $respuesta = 1;
            echo $respuesta;
        } else if ($varSelector == "Geolocalizacion") {

            // CSV field: neighborhood id, student id, latitude, longitude, risk

            global $DB;
            $record = new stdClass();
            $count = 1;

            while ($data = fgetcsv($handle, 100, ",")) {
                $count++;
                $sql_query = "SELECT id FROM {talentospilos_barrios} WHERE cod_barrio = " . $data[0];
                $id_barrio = $DB->get_record_sql($sql_query);

                $query = "SELECT id FROM {user} WHERE username LIKE '" . substr($data[1], 2) . "%'";

                $id_user = $DB->get_record_sql($query);

                if (!$id_user) {
                    throw new MyException("El estudiante con código " . substr($data[1], 2) . " no se encuentra registrado en el campus virtual");
                }

                $additional_fields = get_adds_fields_mi((int) $id_user->id);

                $id_user_talentos = $additional_fields->idtalentos;

                if (!$id_user_talentos) {
                    throw new MyException("El estudiante con código " . substr($data[1], 2) . " no se encuentra enlazado a la tabla talentospilos_usuario");
                }

                $query = "SELECT id FROM {talentospilos_demografia} WHERE id_usuario = $id_user_talentos";
                $id_register = $DB->get_record_sql($query);

                if ($id_register) {

                    $record->id = $id_register->id;
                    $record->id_usuario = $id_user_talentos;
                    // print_r($data[2]);
                    $record->longitud = floatval($data[2]);
                    // print_r($record->longitud);
                    $record->latitud = (double) $data[3];
                    $record->barrio = (int) $id_barrio->id;

                    $DB->update_record('talentospilos_demografia', $record);

                } else {
                    $record->id_usuario = (int) $id_user_talentos;
                    $record->longitud = (float) $data[2];
                    $record->latitud = (float) $data[3];
                    $record->barrio = (int) $id_barrio->id;

                    $DB->insert_record('talentospilos_demografia', $record);
                }

                //consultar id riesgo geografico
                $id_geografic_risk = $DB->get_record_sql("SELECT id FROM {talentospilos_riesgos_ases} WHERE nombre = 'geografico' ")->id;
                $new_risk = new stdClass;
                $new_risk->id_usuario = (int) $id_user_talentos;
                $new_risk->id_riesgo = (int) $id_geografic_risk;
                $new_risk->calificacion_riesgo = (int) $data[4];
                //actualizar riesgo geografico o insertar nuevo registro
                
                $registro = $DB->get_record_sql("SELECT id FROM {talentospilos_riesg_usuario} WHERE id_usuario = $id_user_talentos AND id_riesgo = $id_geografic_risk");
                if( $registro ){
                    $new_risk->id = $registro->id;
                    $DB->update_record('talentospilos_riesg_usuario', $new_risk);                    
                }else{
                    $DB->insert_record('talentospilos_riesg_usuario', $new_risk);
                }
                
                $count++;
            }

            $respuesta = 1;
            echo $respuesta;

        } else if ($varSelector == "materias") {

            global $DB;
            $count = 0;
            while ($data = fgetcsv($handle, 100, ",")) {
                $record = new stdClass();
                $count++;
                $codigo_materia = $data[0];

                $query = "SELECT *
                FROM {talentospilos_materias_criti}
                WHERE codigo_materia = '$codigo_materia'";

                $result = $DB->get_record_sql($query);

                if ($result) {
                    throw new MyException("La materia con codigo $codigo_materia ya se encuentra registrada");
                }

                $record->codigo_materia = $codigo_materia;
                $DB->insert_record('talentospilos_materias_criti', $record);

            }

            $respuesta = 1;
            echo $respuesta;
        } else {
            throw new MyException("Lo sentimos la carga de archivos para la tabla " . $varSelector . " esta en desarrollo.");
        }
        //End of db transaction
        pg_query("COMMIT") or die("Transaction commit failed\n");
        //$transaction->allow_commit();
        fclose($handle);
    } catch (MyException $ex) {
        fclose($handle);
        if (file_exists("../../view/archivos_subidos/$nombre")) {
            unlink("../../view/archivos_subidos/$nombre");
        }
        echo $ex->getMessage();

    } catch (Exception $e) {
        $errorSqlServer = pg_last_error();
        fclose($handle);
        if (file_exists("../../view/archivos_subidos/$nombre")) {
            unlink("../../view/archivos_subidos/$nombre");
        }
        pg_query("ROLLBACK");
        // SQL error generated by serversql is captured. Why is not being captured during a transaction? Find how to do it always

        echo $e->getMessage() . "<br>" . $errorSqlServer . "<br>" . $query . "<b>Consejos:</b><br><b>*</b> Por favor verifica la linea: " . intval($count + 1) . " en el archivo: " . $archivo['name'] . ". Asegurate de que no haya duplicidad en la información<br><b>*</b>Asegurate de que el archivo cargado contenga a la información necesaria en el formato determinado para cargar la tabla " . $varSelector . ".";

    }
} else {
    echo "El envio no se realiza sactisfactoriamente.";
}

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
 * Dynamic PHP Forms
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

    require_once(dirname(__FILE__). '/../../../../../config.php');
    require_once(dirname(__FILE__). '/../../lib/student_lib.php');

    echo "Script no disponible";

    // global $DB;

    // $super_su = false;
    // $password = "";
    // if( hash('sha512', $_GET['password']) == $password  ){
    //     $super_su = true;
    // }else{
    //     echo "<strong> MODO SOLO LECTURA, ?password=s1... </strong><br>";
    // };

    // $alias = 'seguimiento_pares_id_estudiante';
    // $sql_find_preg = "SELECT id_pregunta FROM {talentospilos_df_alias} WHERE alias = '$alias'";
    // $preg_record = $DB->get_record_sql( $sql_find_preg );

    // $id_pregunta = $preg_record->id_pregunta;
    
    
    // $sql_find_records = "SELECT * FROM {talentospilos_df_respuestas} WHERE id_pregunta = '$id_pregunta'";
    // $records = $DB->get_records_sql( $sql_find_records );

    // $cambios = array();
    // $registros_validos = 0;

    // $flag_error = false;

    // foreach( $records as $key => $record ){
    //     print_r( $record );
    //     if( is_numeric( $record->respuesta ) && !empty( $record->respuesta ) ){
    //         $ases_user = get_ases_user_by_code( $record->respuesta );
    //         if( $ases_user ){
    //             $registros_validos++;
    //             $tmp = array(
    //                 'old_value' => $record->respuesta,
    //                 'new_value' => $ases_user->id,
    //                 'record_id' => $record->id
    //             );
    //             array_push( $cambios, $tmp );
    //             echo '<strong style="color:green;">VALIDO:</strong> ' . $record->respuesta . ' → ' . $ases_user->id . ' DOC: ' . $ases_user->num_doc . ' RID: ' . $record->id . '<br>';
    //             if( $super_su ){
    //                 $record->respuesta = $ases_user->id;
    //                 $status = $DB->update_record('talentospilos_df_respuestas', $record, $bulk=false);
    //                 echo "Estado de la actualizacion: " . $status . "<br>";
    //                 if( $status != 1 ){
    //                     $flag_error = true;
    //                     break;
    //                 };
    //             }
    //         }else{
    //             echo '<strong style="color:red;">NO VALIDO!:</strong> ' . $record->respuesta . '<br>';
    //         };
    //     }else{
    //         echo '<strong style="color:red;">NO VALIDO!:</strong> ' . $record->respuesta . '<br>';
    //     }
    // }

    // echo  '<br>Registros totales:' . count( $records ) . ' Registros válidos:' . $registros_validos . ' Registros actualizables: ' . count( $cambios ) . '<br><br>';

    // if( $super_su ){
    //     if( $flag_error ){
    //         echo "<strong>========> ERROR DETECTADO, REGRESANDO A ESTADO PREVIO</strong><br>";
    //         $flag_error_restaurando = false;
    //         foreach( $cambios as $cambio ){
    //             $registro = $DB->get_record_sql( "SELECT * FROM {talentospilos_df_respuestas} WHERE id = '" . $cambio['record_id'] . "'" );
    //             $registro->respuesta = $cambio['old_value'];
    //             if( $DB->update_record('talentospilos_df_respuestas', $registro, $bulk=false) != 1 ){
    //                 $flag_error_restaurando = true;
    //                 echo 'ERROR CRÍTICO: ' . $registro->respuesta . ' → ' . $cambio['old_value'] . ' RID: ' . $registro->id . '<br>';
    //             };
    //         };
    //         if( $flag_error_restaurando ){
    //             echo "ERROR CRÍTICO DURANTE EL REGRESO AL ESTADO PREVIO<br>";
    //         }
    //     }
    // }

    // print_r( json_encode( $cambios ) );
    die();

?>
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
 * ASES
 *
 * @author     Iader E. García G.
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once __DIR__ . '/../../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once("$CFG->libdir/formslib.php");

require_once '../managers/lib/student_lib.php';
require_once '../managers/lib/lib.php';
require_once '../managers/user_management/user_lib.php';
require_once '../managers/student_profile/geographic_lib.php';
require_once '../managers/student_profile/studentprofile_lib.php';
require_once '../managers/student_profile/academic_lib.php';
require_once '../managers/student_profile/student_graphic_dimension_risk.php';
require_once '../managers/instance_management/instance_lib.php';
require_once '../managers/dateValidator.php';
require_once '../managers/permissions_management/permissions_lib.php';
require_once '../managers/validate_profile_action.php';
require_once '../managers/menu_options.php';
require_once '../managers/dphpforms/dphpforms_forms_core.php';
require_once '../managers/dphpforms/dphpforms_records_finder.php';
require_once '../managers/dphpforms/dphpforms_get_record.php';
require_once '../managers/user_management/user_management_lib.php';
require_once '../managers/periods_management/periods_lib.php';
require_once '../classes/AsesUser.php';
require_once '../classes/mdl_forms/UserImageForm.php';
include '../lib.php';

global $PAGE;
global $USER;

include "../classes/output/student_profile_page.php";
include "../classes/output/renderer.php";


// Set up the page.
$title = "Ficha estudiante";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$student_code = (string)optional_param('student_code', 0, PARAM_TEXT);

require_login($courseid, false);

// Set up the page.
if (!consult_instance($blockid)) {
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock = context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/student_profile.php", array('courseid' => $courseid, 'instanceid' => $blockid, 'student_code' => $student_code));

// Nav configuration
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Reporte general', new moodle_url("/blocks/ases/view/ases_report.php", array('courseid' => $courseid, 'instanceid' => $blockid)), null, 'block', $blockid);
$coursenode->add_node($blocknode);
$node = $blocknode->add('Ficha estudiante', new moodle_url("/blocks/ases/view/student_profile.php", array('courseid' => $courseid, 'instanceid' => $blockid, 'student_code' => $student_code)));
$blocknode->make_active();
$node->make_active();

// Load information of student's file
// Initialize context variable
$record = new stdClass;
$actions = authenticate_user_view($USER->id, $blockid);
$record = $actions;
echo $actions->nombre_accion;

$data_init = array();

$rol = get_role_ases($USER->id);
$html_profile_image = "";
/**
 * @param int $mdl_user Moodle user ID
 * @return string  $html_profile_image Standard $OUPUT profile image html
 */
function getHtmlProfileImage($mdl_user_id): string {
    global $DB, $OUTPUT;
    $html_profile_image = "";
    $mdl_user =   user_management_get_full_moodle_user ($mdl_user_id);
    $html_profile_image = $OUTPUT->user_picture($mdl_user, array('size'=>200, 'link'=> false));
    return $html_profile_image;
}
$id_user_moodle_ = null;
$ases_student = null;
if ($student_code != 0) {
    
    $ases_student = get_ases_user_by_code($student_code);

    $student_id = $ases_student->id;
    //echo $student_id ;
    //die;
    // Student information to display on file header (ficha)
    $id_user_moodle = get_id_user_moodle($ases_student->id);
    $id_user_moodle_ = $id_user_moodle;

    $user_moodle = get_moodle_user($id_user_moodle);
    
    
    $html_profile_image = AsesUser::get_HTML_img_profile_image($contextblock->id, $ases_student->id);
    $academic_programs = get_status_program_for_profile($student_id);
    $student_cohorts = get_cohorts_by_student($id_user_moodle);
    $status_ases_array = get_ases_status($ases_student->id, $blockid);

    $document_type = get_document_types_for_profile($ases_student->id);

    $record->id_moodle = $id_user_moodle;
    $record->id_ases = $student_id;
    $record->email_moodle = $user_moodle->email_moodle;
    $record->age = substr($ases_student->age, 0, 2);
    $record->academic_programs = $academic_programs;
    $record->student_cohorts = $student_cohorts;
    $record->document_type = $document_type;

    array_push($data_init, $academic_programs);
    
    // General file (ficha general) information

    $record->res_address = $ases_student->direccion_res;
    $record->init_tel = $ases_student->tel_ini;
    $record->res_tel = $ases_student->tel_res;
    $record->cell_phone = $ases_student->celular;
    $record->emailpilos = $ases_student->emailpilos;
    $record->attendant = $ases_student->acudiente;
    $record->attendant_tel = $ases_student->tel_acudiente;
    $record->num_doc = $ases_student->num_doc;
    $record->observations = $ases_student->observacion;

    // Estado ASES
    if($status_ases_array){
        if($status_ases_array[$blockid]->nombre == "seguimiento"){
            $record->ases_status_t = "seguimiento";
            $record->ases_status_description = "Se realiza seguimiento en esta instancia";
        }else if($status_ases_array[$blockid]->nombre == "sinseguimiento"){

            $has_ases_status = verify_ases_status($ases_student->id);

            if($has_ases_status){
                $record->ases_status_f = "sinseguimiento";
                $record->ases_status_description = "Se realiza seguimiento en otra instancia";
            }else{
                $record->ases_status_n = "noasignado";
                $record->ases_status_description = "No se realiza seguimiento";
            }
        }else{
            $record->ases_status_n = "noasignado";
            $record->ases_status_description = "No se realiza seguimiento";
        }
    }else{
        $record->ases_status_n = "noasignado";
        $record->ases_status_description = "No se realiza seguimiento";
    }

    // Estado ICETEX
    $icetex_statuses = get_icetex_statuses();
    $options_status_icetex = '';

    $status_icetex_student = get_icetex_status_student($student_id);

    foreach($icetex_statuses as $status){
        if($status_icetex_student->id_estado_icetex == $status->id){
            $options_status_icetex .= "<option value='$status->id' selected='selected'>$status->nombre</option>";
        }else{
            $options_status_icetex .= "<option value='$status->id'>$status->nombre</option>";
        }
    }

    $record->options_status_icetex = $options_status_icetex;
    $record->icetex_status_description = $icetex_statuses[$status_icetex_student->id_estado_icetex]->descripcion;
    $record->icetex_status_name = substr($icetex_statuses[$status_icetex_student->id_estado_icetex]->nombre, 3);

    $monitor_object = new stdClass();
    $trainee_object = new stdClass();
    $professional_object = new stdClass();

    $record->id_dphpforms_creado_por = $USER->id;

    $monitor_object = get_assigned_monitor($student_id);
    $trainee_object = get_assigned_pract($student_id);
    $professional_object = get_assigned_professional($student_id);


    if ($monitor_object) {
        $record->monitor_fullname = "$monitor_object->firstname $monitor_object->lastname";
        $record->id_dphpforms_monitor = '-1';
    } else {
        $record->monitor_fullname = "NO REGISTRA";
    }

    if ($trainee_object) {
        $record->trainee_fullname = "$trainee_object->firstname $trainee_object->lastname";
    } else {
        $record->trainee_fullname = "NO REGISTRA";
    }
    
    if ($professional_object) {
        $record->professional_fullname = "$professional_object->firstname $professional_object->lastname";
    } else {
        $record->professional_fullname = "NO REGISTRA";
    }

    // Geographic information

    $geographic_object = load_geographic_info($student_id);

    $neighborhoods_array = get_neighborhoods();

    $neighborhoods = "<option>No registra</option>";

    for ($i = 1; $i <= count($neighborhoods_array); $i++) {
        if(isset($geographic_object->barrio)){
            if ($neighborhoods_array[$i]->id == (int) $geographic_object->barrio) {
                $neighborhoods .= "<option value='" . $neighborhoods_array[$i]->id . "' selected>" . $neighborhoods_array[$i]->nombre . "</option>";
            } else {
                $neighborhoods .= "<option value='" . $neighborhoods_array[$i]->id . "'>" . $neighborhoods_array[$i]->nombre . "</option>";
            }
        }else{
            $neighborhoods .= "<option value='" . $neighborhoods_array[$i]->id . "'>" . $neighborhoods_array[$i]->nombre . "</option>";
        }
        
    }

    $level_risk_array = array('Bajo', 'Medio', 'Alto');

    $select_geographic_risk = "<option>No registra</option>";
    for ($i = 0; $i < 3; $i++) {
        $value = $i + 1;
        if ($i + 1 == (int) $geographic_object->risk) {
            $select_geographic_risk .= "<option value='$value' selected>" . $level_risk_array[$i] . "</option>";
        } else {
            $select_geographic_risk .= "<option value='$value'>" . $level_risk_array[$i] . "</option>";
        }
    }

    $record->select_geographic_risk = $select_geographic_risk;

    $record->select_neighborhoods = $neighborhoods;

    $geographic_object = get_geographic_info($student_id);

    $record->latitude = $geographic_object->latitude;
    $record->longitude = $geographic_object->longitude;

    switch ($geographic_object->risk) {
        case 1:
            $record->geographic_class = 'div_low_risk';
            break;
        case 2:
            $record->geographic_class = 'div_medium_risk';
            break;
        case 3:
            $record->geographic_class = 'div_high_risk';
            break;
        default:
            $record->geographic_class = 'div_no_risk';
            break;
    }

    // Students risks

    $risk_object = get_risk_by_student($student_id);

    $record->individual_risk = $risk_object['individual']->calificacion_riesgo;
    $record->familiar_risk = $risk_object['familiar']->calificacion_riesgo;
    $record->academic_risk = $risk_object['academico']->calificacion_riesgo;
    $record->life_risk = $risk_object['vida_universitaria']->calificacion_riesgo;
    $record->economic_risk = $risk_object['economico']->calificacion_riesgo;

    switch ($risk_object['individual']->calificacion_riesgo) {
        case 1:
            $record->individual_class = 'div_low_risk';
            $record->level_individual = 1;
            break;
        case 2:
            $record->individual_class = 'div_medium_risk';
            $record->level_individual = 2;
            break;
        case 3:
            $record->individual_class = 'div_high_risk';
            $record->level_individual = 3;
            break;
        default:
            $record->individual_class = 'div_no_risk';
            $record->level_individual = 0;
            break;
    }

    switch ($risk_object['familiar']->calificacion_riesgo) {
        case 1:
            $record->familiar_class = 'div_low_risk';
            $record->level_familiar = 1;
            break;
        case 2:
            $record->familiar_class = 'div_medium_risk';
            $record->level_familiar = 2;
            break;
        case 3:
            $record->familiar_class = 'div_high_risk';
            $record->level_familiar = 3;
            break;
        default:
            $record->familiar_class = 'div_no_risk';
            $record->level_familiar = 0;
            break;
    }

    switch ($risk_object['economico']->calificacion_riesgo) {
        case 1:
            $record->economic_class = 'div_low_risk';
            $record->level_economic = 1;
            break;
        case 2:
            $record->economic_class = 'div_medium_risk';
            $record->level_economic = 2;
            break;
        case 3:
            $record->economic_class = 'div_high_risk';
            $record->level_economic = 3;
            break;
        default:
            $record->economic_class = 'div_no_risk';
            $record->level_economic = 0;
            break;
    }

    switch ($risk_object['vida_universitaria']->calificacion_riesgo) {
        case 1:
            $record->life_class = 'div_low_risk';
            $record->level_life = 1;
            break;
        case 2:
            $record->life_class = 'div_medium_risk';
            $record->level_life = 2;
            break;
        case 3:
            $record->life_class = 'div_high_risk';
            $record->level_life = 3;
            break;
        default:
            $record->life_class = 'div_no_risk';
            $record->level_life = 0;
            break;
    }

    switch ($risk_object['academico']->calificacion_riesgo) {
        case 1:
            $record->academic_class = 'div_low_risk';
            break;
        case 2:
            $record->academic_class = 'div_medium_risk';
            break;
        case 3:
            $record->academic_class = 'div_high_risk';
            break;
        default:
            $record->academic_class = 'div_no_risk';
            break;
    }

    $select = make_select_ficha($USER->id, $rol, $student_code, $blockid, $actions);
    $record->code = $select;

    // Loading academic information

    foreach ($academic_programs as $program) {
        if($program->tracking_status == 1){
            $academic_program_id = $program->academic_program_id;
            break;
        }
    }
    //Current data
    //weighted average
    $promedio = get_promedio_ponderado($student_id, $academic_program_id);
    $record->promedio = $promedio;

    // //num bajos
    $bajos = get_bajos_rendimientos($student_id, $academic_program_id);
    $record->bajos = $bajos;

    // // //num estimulos
    $estimulos = get_estimulos($student_id, $academic_program_id);
    $record->estimulos = $estimulos;

    // //Current semester
    $html_academic_table = get_grades_courses_student_last_semester($id_user_moodle);
    $record->academic_semester_act = $html_academic_table;

    // //historic academic
    $html_historic_academic = get_historic_academic_by_student($student_id);
    $record->historic_academic = $html_historic_academic;

    // Student trackings (Seguimientos)

    $dphpforms_ases_user = get_ases_user_by_code( $student_code )->id;

    // Pares
    $array_peer_trackings_dphpforms = dphpforms_find_records('seguimiento_pares', 'seguimiento_pares_id_estudiante', $dphpforms_ases_user, 'DESC');
    $array_peer_trackings_dphpforms = json_decode($array_peer_trackings_dphpforms);

    // Inasistencia
    $array_inasistencia_peer_trackings_dphpforms = dphpforms_find_records('inasistencia', 'inasistencia_id_estudiante', $dphpforms_ases_user, 'DESC');
    $array_inasistencia_peer_trackings_dphpforms = json_decode($array_inasistencia_peer_trackings_dphpforms);
    

    $array_detail_peer_trackings_dphpforms = array();
    $array_detail_inasistencia_peer_trackings_dphpforms = array();

    foreach ($array_peer_trackings_dphpforms->results as &$peer_trackings_dphpforms) {
        array_push($array_detail_peer_trackings_dphpforms, json_decode(dphpforms_get_record($peer_trackings_dphpforms->id_registro, 'fecha')));
    };

    foreach ($array_inasistencia_peer_trackings_dphpforms->results as &$inasistencia_peer_trackings_dphpforms) {
        array_push($array_detail_inasistencia_peer_trackings_dphpforms, json_decode(dphpforms_get_record($inasistencia_peer_trackings_dphpforms->id_registro, 'in_fecha')));
    };

    $array_tracking_date = array();

    foreach ($array_detail_peer_trackings_dphpforms as &$peer_tracking) {
        foreach ($peer_tracking->record->campos as &$tracking) {
            if ($tracking->local_alias == 'fecha') {
                array_push($array_tracking_date, strtotime($tracking->respuesta));
            };
        };
    };

    foreach ($array_detail_inasistencia_peer_trackings_dphpforms as &$inasistencia_peer_tracking) {
        foreach ($inasistencia_peer_tracking->record->campos as &$tracking) {
            if ($tracking->local_alias == 'in_fecha') {
                array_push($array_tracking_date, strtotime($tracking->respuesta));
            };
        };
    };

    rsort($array_tracking_date);

    $seguimientos_ordenados = new stdClass();
    $seguimientos_ordenados->index = array();
    //Inicio de ordenamiento
    $periodo_a = [1, 2, 3, 4, 5, 6, 7];
    //periodo_b es el resto de meses;
    for ($x = 0; $x < count($array_tracking_date); $x++) {

        $string_date = $array_tracking_date[$x];
        $array_tracking_date[$x] = getdate($array_tracking_date[$x]);
        $year = $array_tracking_date[$x]['year'];

        if (property_exists($seguimientos_ordenados, $year)) {
            
            if (in_array($array_tracking_date[$x]['mon'], $periodo_a)) {
                // Records where we expect to find
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_a, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                };
                            };
                        };
                    };
                };
                // Inasistencia
                for ($y = 0; $y < count($array_detail_inasistencia_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_inasistencia_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_inasistencia_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'in_fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_a, $array_detail_inasistencia_peer_trackings_dphpforms[$y]);
                                    $array_detail_inasistencia_peer_trackings_dphpforms[$y] = null;
                                    break;
                                };
                            };
                        };
                    };
                };
            } else {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_b, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                };
                            };
                        };
                    };
                };
                // Inasistencia
                for ($y = 0; $y < count($array_detail_inasistencia_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_inasistencia_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_inasistencia_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'in_fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_b, $array_detail_inasistencia_peer_trackings_dphpforms[$y]);
                                    $array_detail_inasistencia_peer_trackings_dphpforms[$y] = null;
                                    break;
                                };
                            };
                        };
                    };
                };
            };
        } else {
            
            array_push($seguimientos_ordenados->index, $year);

            $seguimientos_ordenados->$year->year = $year;
            $seguimientos_ordenados->$year->per_a = array();
            $seguimientos_ordenados->$year->per_b = array();

            $seguimientos_ordenados->$year->year = $year;
            if (in_array($array_tracking_date[$x]['mon'], $periodo_a)) {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_a, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                };
                            };
                        };
                    };
                };
                // Inasistencia
                for ($y = 0; $y < count($array_detail_inasistencia_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_inasistencia_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_inasistencia_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'in_fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_a, $array_detail_inasistencia_peer_trackings_dphpforms[$y]);
                                    $array_detail_inasistencia_peer_trackings_dphpforms[$y] = null;
                                    break;
                                };
                            };
                        };
                    };
                };
            } else {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_b, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                };
                            };
                        };
                    };
                };
                // Inasistencia
                for ($y = 0; $y < count($array_detail_inasistencia_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_inasistencia_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_inasistencia_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'in_fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_b, $array_detail_inasistencia_peer_trackings_dphpforms[$y]);
                                    $array_detail_inasistencia_peer_trackings_dphpforms[$y] = null;
                                    break;
                                };
                            };
                        };
                    };
                };
            };
        };
    };
    //Fin de ordenamiento

    $seguimientos_array = json_decode(json_encode($seguimientos_ordenados), true);

    $array_periodos = array();
    for ($x = 0; $x < count($seguimientos_array['index']); $x++) {
        array_push($array_periodos, $seguimientos_array[ $seguimientos_array['index'][$x] ]);
    }
    $record->peer_tracking_v2 = array(
        'index' => $seguimientos_array['index'],
        'periodos' => $array_periodos,
    );

    $enum_risk = array();
    array_push($enum_risk, "");
    array_push($enum_risk, "Bajo");
    array_push($enum_risk, "Medio");
    array_push($enum_risk, "Alto");

    //END V2

    //*************************************/
    // fx get_tracking_peer_student_current_semester('1522006', '23');
    //*************************************/

    $html_tracking_peer = "";
    $array_peer_trackings = get_tracking_group_by_semester($student_id, 'PARES', null, $blockid);

    if ($array_peer_trackings != null) {

        $panel = "<div class='panel-group' id='accordion_semesters'>";
        $number_semesters = count($array_semester);
        foreach ($array_peer_trackings->semesters_segumientos as $key_semester => $array_semester) {

            if (strpos($array_semester->name_semester, '2018') !== false) {
                continue;
            }
            ;

            $panel .= "<div class='panel panel-default'>";
            $panel .= "<a data-toggle='collapse' class='collapsed' data-parent='#accordion_semesters' style='text-decoration:none' href='#semester" . $array_semester->id_semester . "'>";
            $panel .= "<div class='panel-heading heading_semester_tracking'>";
            $panel .= "<h4 class='panel-title'>";
            $panel .= "$array_semester->name_semester";
            $panel .= "<span class='glyphicon glyphicon-chevron-left'></span>";
            $panel .= "</h4>"; //End panel-title
            $panel .= "</div>"; //End panel-heading
            $panel .= "</a>";

            $panel .= "<div id='semester$array_semester->id_semester' class='panel-collapse collapse in'>";
            $panel .= "<div class='panel-body'>";

            // $panel .= "<div class=\"container well col-md-12\">";
            // $panel .= "<div class=\"container-fluid col-md-10\" name=\"info\">";
            // $panel .= "<div class=\"row\">";

            $panel .= "<div class='panel-group' id='accordion_trackings_semester'>";

            foreach ($array_semester->result as $tracking) {

                $monitor_object = get_moodle_user($tracking->id_monitor);

                // Date format (Formato de fecha)
                $date = date_parse_from_format('d-m-Y', $tracking->fecha);
                $months = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-heading'>";
                $panel .= "<h4 class='panel-title'>";

                $panel .= "<a data-toggle='collapse' data-parent='#accordion_trackings_semester' href='#" . $tracking->id_seg . "'>";
                $panel .= " Registro " . $months[(int) $date["month"] - 1] . "-" . $date["day"] . "-" . $date["year"] . "</a>";

                $panel .= "</h4>"; // h4 div panel-title
                $panel .= "</div>"; // End div panel-heading

                $panel .= "<div id='$tracking->id_seg' class='panel-collapse collapse'>";
                $panel .= "<div class='panel-body'>";

                // Date, Place, time  (Fecha, lugar, hora)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-3'>";
                $panel .= "<b>Fecha:</b>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-6'>";
                $panel .= "<b>Lugar:</b>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-3'>";
                $panel .= "<b>Hora:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-3'>";
                $panel .= "<span class='date_tracking_peer'>" . $date["month"] . "-" . $date["day"] . "-" . $date["year"] . "</span>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-6'>";
                $panel .= "<span class='place_tracking_peer'>" . $tracking->lugar . "</span>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-3'>";
                $panel .= "<span class='init_time_tracking_peer'>" . $tracking->hora_ini . "</span> - <span class='ending_time_tracking_peer'>" . $tracking->hora_fin . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Created by (Creado por)

                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Creado por: </b>";
                $panel .= $monitor_object->firstname . " " . $monitor_object->lastname;
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Subject (Tema)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Tema:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<span class='topic_tracking_peer'>" . $tracking->tema . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Objectives (Objetivos)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Objetivos:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<span class='objectives_tracking_peer'>" . $tracking->objetivos . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                if ($tracking->individual != "") {

                    if ($tracking->individual_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->individual_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->individual_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Individual:</b><br>";
                    $panel .= "<span class='individual_tracking_peer'>$tracking->individual</span><br><br>";
                    $panel .= "<b>Riesgo individual: </b>";
                    $panel .= "<span class='ind_risk_tracking_peer'>" . $enum_risk[(int) $tracking->individual_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->familiar_desc != "") {

                    if ($tracking->familiar_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->familiar_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->familiar_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Familiar:</b><br>";
                    $panel .= "<span class='familiar_tracking_peer'>$tracking->familiar_desc</span><br><br>";
                    $panel .= "<b>Riesgo familiar: </b>";
                    $panel .= "<span class='fam_risk_tracking_peer'>" . $enum_risk[(int) $tracking->familiar_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->academico != "") {

                    if ($tracking->academico_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->academico_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->academico_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Académico:</b><br>";
                    $panel .= "<span class='academico_tracking_peer'>$tracking->academico</span><br><br>";
                    $panel .= "<b>Riesgo académico: </b>";
                    $panel .= "<span class='aca_risk_tracking_peer'>" . $enum_risk[(int) $tracking->academico_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->economico != "") {

                    if ($tracking->economico_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->economico_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->economico_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Económico:</b><br>";
                    $panel .= "<span class='economico_tracking_peer'>$tracking->economico</span><br><br>";
                    $panel .= "<b>Riesgo económico: </b>";
                    $panel .= "<span class='econ_risk_tracking_peer'>" . $enum_risk[(int) $tracking->economico_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->vida_uni != "") {

                    if ($tracking->vida_uni_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->vida_uni_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->vida_uni_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Vida universitaria:</b><br>";
                    $panel .= "<span class='lifeu_tracking_peer'>$tracking->vida_uni</span><br><br>";
                    $panel .= "<b>Riesgo vida universitaria: </b>";
                    $panel .= "<span class='lifeu_risk_tracking_peer'>" . $enum_risk[(int) $tracking->vida_uni_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                // Observations (observaciones)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Observaciones:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<span class='observations_tracking_peer'>" . $tracking->observaciones . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Edit and delete buttons
                $panel .= "<div class='row'>";
                $panel .= "<div class='col-sm-4 row-buttons-tracking'>";
                $panel .= "<button type='button' class='btn-primary edit_peer_tracking' id='edit_tracking_" . $tracking->id_seg . "'>Editar seguimiento</button>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-3 col-sm-offset-5 row-buttons-tracking'>";
                $panel .= "<button type='button' class='btn-danger delete_peer_tracking col-sm-10' id='delete_tracking_peer_" . $tracking->id_seg . "'>";
                $panel .= "Borrar <span class='glyphicon glyphicon-trash'></span>";
                $panel .= "</button>";
                $panel .= "</div>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body tracking
                $panel .= "</div>"; // End div panel-collapse tracking
                $panel .= "</div>"; // End div panel-default
            }

            $panel .= "</div>"; // End panel accordion_trackings_semester

            $panel .= "</div>"; // End panel-body
            $panel .= "</div>"; // End panel-collapse

            $panel .= "</div>"; //End panel panel-default
        }

        $panel .= "</div>"; //End panel group accordion_semesters

        $html_tracking_peer .= $panel;

    } else {
        $html_tracking_peer .= "<div class='col-sm-12'><center><h4>No registra seguimientos</h4></center></div>";
    }

    $record->peer_tracking = $html_tracking_peer;

    // Loading desertion reasons or studies postponement

    $reasons_dropout = get_reasons_dropout();

    $html_select_reasons = "<option value='' id='no_reason_option'>Seleccione el motivo</option>";

    foreach ($reasons_dropout as $reason) {
        $html_select_reasons .= "<option value=" . $reason->id . ">";
        $html_select_reasons .= $reason->descripcion;
        $html_select_reasons .= "</option>";
    }

    $record->reasons_options = $html_select_reasons;

    // Getting data for risks graphs

    $periodoactual = getPeriodoActual();
    $idEstudiante = $student_id;

    //$seguimientos_array
    //
    echo '<br>';
    $current_year = date('Y', strtotime($periodoactual['fecha_inicio']));
    $initial_month = date('m', strtotime($periodoactual['fecha_inicio']));
    $final_month = date('m', strtotime($periodoactual['fecha_fin']));
    
    $isPeriodA = false;
    if ( in_array( $initial_month, $periodo_a ) ){
        $isPeriodA = true;
    }

    $datos_seguimientos_periodo_actual = array();

    if( $isPeriodA ){
        $datos_seguimientos_periodo_actual = $seguimientos_array[ $current_year ][ 'per_a' ];
    }else{
        $datos_seguimientos_periodo_actual = $seguimientos_array[ $current_year ][ 'per_b' ];
    };

    /*
        In this block, we use the local_alias defined with the field in the dynamic form
        to filter the fields
    */
    
    $risks = array();

    for( $x = 0; $x < count( $datos_seguimientos_periodo_actual ); $x++){

        $risk_date = null;

        $individual_dimension_risk_lvl = null;
        $academic_dimension_risk_lvl = null;
        $economic_dimension_risk_lvl = null;
        $familiar_dimension_risk_lvl = null;
        $universitary_life_risk_lvl = null;

        $tmp_track = $datos_seguimientos_periodo_actual[ $x ][ 'record' ][ 'campos' ];
        
        for( $y = 0; $y < count( $tmp_track ); $y++ ){
            
            if( $tmp_track[ $y ]['local_alias'] == 'fecha' ){
                $risk_date = date('Y-M-d', strtotime($tmp_track[ $y ]['respuesta'] ));
            }
            if( $tmp_track[ $y ]['local_alias'] == 'puntuacion_riesgo_individual' ){
                if( ($tmp_track[ $y ]['respuesta'] != '-#$%-')&&($tmp_track[ $y ]['respuesta'] != '0') ){
                    $individual_dimension_risk_lvl = $tmp_track[ $y ]['respuesta'] ;
                }
            }
            if( $tmp_track[ $y ]['local_alias'] == 'puntuacion_riesgo_academico' ){
                if( ($tmp_track[ $y ]['respuesta'] != '-#$%-')&&($tmp_track[ $y ]['respuesta'] != '0') ){
                    $academic_dimension_risk_lvl = $tmp_track[ $y ]['respuesta'] ;
                }
            }
            if( $tmp_track[ $y ]['local_alias'] == 'puntuacion_riesgo_economico' ){
                if( ($tmp_track[ $y ]['respuesta'] != '-#$%-')&&($tmp_track[ $y ]['respuesta'] != '0') ){
                    $economic_dimension_risk_lvl = $tmp_track[ $y ]['respuesta'] ;
                }
            }
            if( $tmp_track[ $y ]['local_alias'] == 'puntuacion_riesgo_familiar' ){
                if( ($tmp_track[ $y ]['respuesta'] != '-#$%-')&&($tmp_track[ $y ]['respuesta'] != '0') ){
                    $familiar_dimension_risk_lvl = $tmp_track[ $y ]['respuesta'] ;
                }
            }
            if( $tmp_track[ $y ]['local_alias'] == 'puntuacion_vida_uni' ){
                if( ($tmp_track[ $y ]['respuesta'] != '-#$%-')&&($tmp_track[ $y ]['respuesta'] != '0') ){
                    $universitary_life_risk_lvl = $tmp_track[ $y ]['respuesta'] ;
                }
            }
        }

        $risk_by_dimensions = array();

        if( $individual_dimension_risk_lvl ){
            array_push(
                $risk_by_dimensions,
                array(
                    'dimension' => 'individual',
                    'risk_lvl' => $individual_dimension_risk_lvl
                )
            );
        }
        if( $academic_dimension_risk_lvl ){
            array_push(
                $risk_by_dimensions,
                array(
                    'dimension' => 'academica',
                    'risk_lvl' => $academic_dimension_risk_lvl
                )
            );
        }
        if( $economic_dimension_risk_lvl ){
            array_push(
                $risk_by_dimensions,
                array(
                    'dimension' => 'economica',
                    'risk_lvl' => $economic_dimension_risk_lvl
                )
            );
        }
        if( $familiar_dimension_risk_lvl ){
            array_push(
                $risk_by_dimensions,
                array(
                    'dimension' => 'familiar',
                    'risk_lvl' => $familiar_dimension_risk_lvl
                )
            );
        }
        if( $universitary_life_risk_lvl ){
            array_push(
                $risk_by_dimensions,
                array(
                    'dimension' => 'vida_universitaria',
                    'risk_lvl' => $universitary_life_risk_lvl
                )
            );
        }
        

        $risk_data = array(
            'date' => $risk_date,
            'information' => $risk_by_dimensions,
            'record_id' => $datos_seguimientos_periodo_actual[ $x ]['record']['id_registro']
        );

        array_push( $risks, $risk_data );
    }
    
    $risk_individual_dimension = array();
    $risk_academic_dimension = array();
    $risk_economic_dimension = array();
    $risk_familiar_dimension = array();
    $risk_uni_life_dimension = array();
    
    $risks = array_reverse( $risks );
    
    for( $p = 0; $p < count( $risks ); $p++ ){

        for( $q = 0; $q < count( $risks[ $p ][ 'information' ] ); $q++  ){

            $isIndividual = false;
            $isAcademic = false;
            $isEconomic = false;
            $isFamiliar = false;
            $isUniLife = false;
            
            if( $risks[ $p ][ 'information' ][ $q ][ 'dimension' ] == 'individual' ){
                $isIndividual = true;
                $color = null;
                if( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '1'){
                    $color = 'green';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '2') {
                    $color = 'orange';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '3') {
                    $color = 'red';
                }
            }

            if( $risks[ $p ][ 'information' ][ $q ][ 'dimension' ] == 'academica' ){
                $isAcademic = true;
                $color = null;
                if( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '1'){
                    $color = 'green';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '2') {
                    $color = 'orange';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '3') {
                    $color = 'red';
                }
            }

            if( $risks[ $p ][ 'information' ][ $q ][ 'dimension' ] == 'economica' ){
                $isEconomic = true;
                $color = null;
                if( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '1'){
                    $color = 'green';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '2') {
                    $color = 'orange';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '3') {
                    $color = 'red';
                }
            }

            if( $risks[ $p ][ 'information' ][ $q ][ 'dimension' ] == 'familiar' ){
                $isFamiliar = true;
                $color = null;
                if( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '1'){
                    $color = 'green';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '2') {
                    $color = 'orange';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '3') {
                    $color = 'red';
                }
            }

            if( $risks[ $p ][ 'information' ][ $q ][ 'dimension' ] == 'vida_universitaria' ){
                $isUniLife = true;
                $color = null;
                if( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '1'){
                    $color = 'green';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '2') {
                    $color = 'orange';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '3') {
                    $color = 'red';
                }
            }
            
            $data = array(
                'fecha' => $risks[ $p ][ 'date' ],
                'color' => $color,
                'riesgo' => $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ],
                'end' => 'false'
            );

            $tmp_risk = array(
                'id_seguimiento' => $risks[ $p ][ 'record_id' ],
                'datos' => $data
            );

            if( $isIndividual ){
                array_push( $risk_individual_dimension, $tmp_risk );
            }else if( $isAcademic ){
                array_push( $risk_academic_dimension, $tmp_risk );
            }else if( $isEconomic ){
                array_push( $risk_economic_dimension, $tmp_risk );
            }else if( $isFamiliar ){
                array_push( $risk_familiar_dimension, $tmp_risk );
            }else if( $isUniLife ){
                array_push( $risk_uni_life_dimension, $tmp_risk );
            }
        };

    }

    array_push( $risk_individual_dimension, array( 'end' => 'true' ) );
    array_push( $risk_academic_dimension, array( 'end' => 'true' ) );
    array_push( $risk_economic_dimension, array( 'end' => 'true' ) );
    array_push( $risk_familiar_dimension, array( 'end' => 'true' ) );
    array_push( $risk_uni_life_dimension, array( 'end' => 'true' ) );

    //die();

    // Mustache doesn't allow advanced conditional control, information detachment occurs here
    /*$seguimientosEstudianteIndividual = obtenerDatosSeguimientoFormateados($idEstudiante, 'individual', $periodoactual);
    $seguimientosEstudianteFamiliar = obtenerDatosSeguimientoFormateados($idEstudiante, 'familiar', $periodoactual);
    $seguimientosEstudianteAcademico = obtenerDatosSeguimientoFormateados($idEstudiante, 'academico', $periodoactual);
    $seguimientosEstudianteEconomicor = obtenerDatosSeguimientoFormateados($idEstudiante, 'economico', $periodoactual);
    $seguimientosVidaUniversitaria = obtenerDatosSeguimientoFormateados($idEstudiante, 'vida_universitaria', $periodoactual);*/

    $seguimientosEstudianteIndividual = $risk_individual_dimension;
    $seguimientosEstudianteFamiliar = $risk_familiar_dimension;
    $seguimientosEstudianteAcademico = $risk_academic_dimension;
    $seguimientosEstudianteEconomicor = $risk_economic_dimension;
    $seguimientosVidaUniversitaria = $risk_uni_life_dimension;

    $record->nombrePeriodoSeguimiento = $periodoactual['nombre_periodo'];
    $record->datosSeguimientoEstudianteIndividual = $seguimientosEstudianteIndividual;
    $record->datosSeguimientoEstudianteFamiliar = $seguimientosEstudianteFamiliar;
    $record->datosSeguimientoEstudianteAcademico = $seguimientosEstudianteAcademico;
    $record->datosSeguimientoEstudianteEconomico = $seguimientosEstudianteEconomicor;
    $record->datosSeguimientoEstudianteVidaUniversitaria = $seguimientosVidaUniversitaria;

    // End of data obtaining for risks graphs

    $record->form_seguimientos = null;
    $record->primer_acercamiento = null;
    $record->form_seguimientos = dphpforms_render_recorder('seguimiento_pares', $rol);
    $record->form_inasistencia = dphpforms_render_recorder('inasistencia', $rol);
    
    if ($record->form_seguimientos == '') {
        $record->form_seguimientos = "<strong><h3>Oops!: No se ha encontrado un formulario con el alias: <code>seguimiento_pares</code>.</h3></strong>";
    }
    
    $record->primer_acercamiento = dphpforms_render_recorder('primer_acercamiento', $rol);
    if ($record->primer_acercamiento == '') {
        $record->primer_acercamiento = "<strong><h3>Oops!: No se ha encontrado un formulario con el alias: <code>primer_acercamiento</code>.</h3></strong>";
    }

    $record->registro_primer_acercamient = null;
    $record->editor_registro_primer_acercamiento = null;
    $primer_acercamiento = json_decode( dphpforms_find_records('primer_acercamiento', 'primer_acercamiento_id_estudiante', $dphpforms_ases_user, 'DESC') )->results;
    
    if($primer_acercamiento){
        $record->actualizar_primer_acercamiento = true;
        $record->id_primer_acercamiento = array_values( $primer_acercamiento )[0]->id_registro;
        $record->editor_registro_primer_acercamiento = dphpforms_render_updater('primer_acercamiento', $rol, array_values( $primer_acercamiento )[0]->id_registro);
    }else{
        $record->registro_primer_acercamiento = true;
    }

    $record->form_seguimientos_geograficos = dphpforms_render_recorder('seguimiento_geografico', $rol);
    if ($record->form_seguimientos_geograficos == '') {
        $record->form_seguimientos_geograficos = "<strong><h3>Oops!: No se ha encontrado un formulario con el alias: <code>seguimientos_geograficos</code>.</h3></strong>";
    }
    $seguimiento_geografico = json_decode( dphpforms_find_records('seguimiento_geografico', 'seg_geo_id_estudiante', $dphpforms_ases_user, 'DESC') )->results;
    if($seguimiento_geografico){
        $record->actualizar_seguimiento_geografico = true;
        $record->id_seguimiento_geografico = array_values( $seguimiento_geografico )[0]->id_registro;
    }else{
        $record->registro_seguimiento_geografico = true;
    }

} else {

    $student_id = -1;
    $select = make_select_ficha($USER->id, $rol, null, $blockid, $actions);
    $record->code = $select;

}

if ($rol == 'sistemas') {
    $record->add_peer_tracking_lts = true;
    $record->sistemas = true;
}

if ($rol == 'dir_socioeducativo') {
    $record->dir_socioeducativo = true;
}

if ($rol == 'monitor_ps') {
    $record->monitor_ps = true;
}
/** Update user image */
$show_html_elements_update_user_profile_image = false;
if (isset($actions->update_user_profile_image)) {
    $show_html_elements_update_user_profile_image = true;
}
$record->show_html_elements_update_user_profile_image = $show_html_elements_update_user_profile_image;


$url_user_edit_image_form_manager        = new moodle_url("/blocks/ases/view/edit_user_image.php", array(
    'courseid' => $courseid,
    'instanceid' => $blockid,
    'ases_user_id' => $ases_student->id,
    'url_return' => $url
));
$_user_image_edit_form = new user_image_edit_form($url_user_edit_image_form_manager,null,'post',null,array('id'=>'update_user_profile_image'));
$_user_image_edit_form->set_data($toform);
$record->update_profile_image_form = $_user_image_edit_form->render(null);
/** End of Update user image  */
$record->ases_student_code = $dphpforms_ases_user;
$record->instance = $blockid;
$record->html_profile_image = $html_profile_image;

// Url for update user image profile
$url_update_user_image           = new moodle_url("/blocks/ases/view/edit_user_image.php", array(
    'courseid' => $courseid,
    'instanceid' => $blockid,
    'userid' => $id_user_moodle_,
    'url_return' => $url
));
$record->update_profile_image_url = $url_update_user_image; 

// periods_lib.php contains get_current_semester()
$record->current_semester = get_current_semester()->max;

$stud_mon_prac_prof = user_management_get_stud_mon_prac_prof( $record->ases_student_code, $record->instance, $record->current_semester );
$record->monitor_id = $stud_mon_prac_prof->monitor->id;
$record->practicing_id = $stud_mon_prac_prof->practicing->id;
$record->professional_id = $stud_mon_prac_prof->professional->id;


//Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);

$record->menu = $menu_option;

$PAGE->set_context($contextcourse);
$PAGE->set_context($contextblock);
$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->requires->css('/blocks/ases/style/jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/sugerenciaspilos.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/c3.css', true);
$PAGE->requires->css('/blocks/ases/style/student_profile_risk_graph.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/student_profile.css', true);
$PAGE->requires->css('/blocks/ases/style/switch.css', true);
//Pendiente para cambiar el idioma del nombre del archivo junto con la estructura de
//su nombramiento.
$PAGE->requires->css('/blocks/ases/style/creadorFormulario.css', true);

$PAGE->requires->js_call_amd('block_ases/student_profile_main', 'init', $data_init);
$PAGE->requires->js_call_amd('block_ases/student_profile_main', 'equalize');

$PAGE->requires->js_call_amd('block_ases/geographic_main', 'init');
$PAGE->requires->js_call_amd('block_ases/dphpforms_form_renderer', 'init');
$PAGE->requires->js_call_amd('block_ases/academic_profile_main', 'init');
$output = $PAGE->get_renderer('block_ases');

echo $output->header();
$student_profile_page = new \block_ases\output\student_profile_page($record);
echo $output->render($student_profile_page);
echo $output->footer();


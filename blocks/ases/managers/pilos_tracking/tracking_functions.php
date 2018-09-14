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
 * Estrategia ASES
 *
 * @author     Isabella Serna Ramirez
 * @package    block_ases
 * @copyright  2017 Isabella Serna RamĆ­rez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once ('pilos_tracking_lib.php');
require_once (dirname(__FILE__) . '/../lib/student_lib.php');
require_once (dirname(__FILE__) . '/../dphpforms/dphpforms_get_record.php');
require_once (dirname(__FILE__) . '/../student_profile/studentprofile_lib.php');
require_once (dirname(__FILE__) . '/../seguimiento_grupal/seguimientogrupal_lib.php');

/**
 * Get the toggle of the monitor with the follow-ups of each student with the implementation of the new form
 *
 * @see render_monitor_new_form($students_by_monitor)
 * @param $student_by_monitor --> students assigned to a monitor
 * @return String
 *
 */

function render_monitor_new_form($students_by_monitor, $period = null)
{
    $panel = "";
    foreach($students_by_monitor as $student) {
        $student_code = get_user_moodle($student->id_estudiante);
        //$student = explode("-", $student_code->username);
        //$student = explode("-", $student->id_estudiante + "-" );
        $ases_student_code = $student->id_estudiante;
        $current_semester = get_current_semester();
        if ($period == null) {
            $monitor_trackings = get_tracking_current_semester('student', $ases_student_code, $current_semester->max);
        }
        else {
            $monitor_trackings = get_tracking_current_semester('student', $ases_student_code, $period);
        }

        $monitor_counting = filter_trackings_by_review($monitor_trackings);

        $panel.= "<a data-toggle='collapse' class='student collapsed btn btn-danger btn-univalle btn-card collapsed' data-parent='#accordion_students' style='text-decoration:none' href='#student" . $ases_student_code . "'>";
        $panel.= "<div class='panel-heading heading_students_tracking'>";
        $panel.= "<h4 class='panel-title'>";
        $panel.= "$student_code->firstname $student_code->lastname";
        $panel.= "</h4>"; //End panel-title
        $panel.= "<div class='row'>
              <div class='col-sm-11'><h6><p class='text-right'><strong>RP :</strong><label class='review_prof'>" . $monitor_counting[0] . "</label> - <strong> N RP: </strong><label class='not_review_prof'>" . $monitor_counting[1] . "</label> - <strong>TOTAL:</strong><label class='total_prof'>" . ($monitor_counting[0] + $monitor_counting[1]) . "</label></p><p class='text-right'><strong>Rp :</strong><label class='review_pract'>" . $monitor_counting[2] . "</label> - <strong> N Rp: </strong><label class='not_review_pract'>" . $monitor_counting[3] . "</label> - <strong>TOTAL:</strong><label class='total_pract'>" . ($monitor_counting[2] + $monitor_counting[3]) . "</label></p></h6></div>
             <div class='col-sm-1'><span class='glyphicon glyphicon-chevron-left'></span></div>
             </div>";
        $panel.= "</div>"; //End panel-heading
        $panel.= "</a>";
        $panel.= "<div id='student$ases_student_code'  class='show collapse_v2 collapse border_rt' role='tabpanel' aria-labelledby='headingstudent$ases_student_code' aria-expanded='true'>";
        $panel.= "<div class='panel-body'>";
        $panel.= "</div>"; // End panel-body
        $panel.= "</div>"; // End collapse
    }

    return $panel;
}


/**
 * Create group tracking toogle given a monitor_id
 *
 * @see aux_create_groupal_toggle($monitor_id)
 * @param $monitor_id
 * @return String
 *
 */

function aux_create_groupal_toggle($monitor_id)
{
    $panel = "";
    $panel.= "<a data-toggle='collapse' class='groupal collapsed btn btn-danger btn-univalle btn-card collapsed' data-parent='#accordion_students' style='text-decoration:none' href='#groupal" . $monitor_id . "'>";
    $panel.= "<div class='panel-heading heading_students_tracking'>";
    $panel.= "<h4 class='panel-title'>";
    $panel.= "SEGUIMIENTOS GRUPALES";
    $panel.= "<span class='glyphicon glyphicon-chevron-left'></span>";
    $panel.= "</h4>"; //End panel-title
    $panel.= "</div>"; //End panel-heading
    $panel.= "</a>";
    $panel.= "<div id='groupal$monitor_id'  class='show collapse_v2 collapse border_rt' role='tabpanel' aria-labelledby='headinggroupal$monitor_id' aria-expanded='true'>";
    $panel.= "<div class='panel-body'>";
    $panel.= "</div>"; // End panel-body
    $panel.= "</div>"; // End collapse
    return $panel;
}

/**
 * Get the toggle of the monitor with the groupal follow-ups of each student with the implementation of the new form
 *
 * @see render_monitor_new_form($students_by_monitor)
 * @param $student_by_monitor --> students assigned to a monitor
 * @return String
 *
 */

function render_groupal_tracks_monitor_new_form($groupal_tracks, $monitor_id, $period = null)
{
    $panel = "";
    foreach($groupal_tracks as $student) {
        $current_semester = get_current_semester();
        if ($period == null) {
            $monitor_trackings = get_tracking_grupal_monitor_current_semester($monitor_id, $current_semester->max);
        }
        else {
            $monitor_trackings = get_tracking_grupal_monitor_current_semester($monitor_id, $period);
        }

        $panel.= aux_create_groupal_toggle($monitor_id);
    }

    if (!$groupal_tracks) {
        $panel.= aux_create_groupal_toggle($monitor_id);
    }

    return $panel;
}

/**
 * Get the toggle of the practicant with the trackings of each student that belongs to a certain monitor with the implementation of the new form
 *
 * @see render_practicant_new_form($monitors_of_pract)
 * @param $monitors_of_pract --> monitors of practicants
 * @return String
 *
 */

function render_practicant_new_form($monitors_of_pract, $instance, $period = null)
{
    $panel = "";
    $practicant_counting = [];
    $current_semester = get_current_semester();
    foreach($monitors_of_pract as $monitor) {
        $monitor_id = $monitor->id_usuario;
        $students_by_monitor = get_students_of_monitor($monitor_id, $instance);

        // If the practicant has monitors with students that show

        $panel.= "<a data-toggle='collapse' class='monitor collapsed btn btn-danger btn-univalle btn-card collapsed' data-parent='#accordion_monitors' style='text-decoration:none' href='#monitor" . $monitor->username . "'>";
        $panel.= "<div class='panel-heading heading_monitors_tracking'>";
        $panel.= "<div class='row'><div class='col-sm-5'>";
        $panel.= "<h4 class='panel-title'>";
        $panel.= "$monitor->firstname $monitor->lastname";
        $panel.= "</h4></div>"; //End panel-title
        $panel.= "<div class='col-sm-1'>";
        $panel.= "<span class='glyphicon glyphicon-user subpanel' style='font-size: 20px;'></span> : " . count(get_students_of_monitor($monitor_id, $instance));
        $panel.= "</div>";
        $panel.= "<div class='col-sm-1'>";
        $panel.= "<button type='button' class='see_history btn red_button'>
                <span class='glyphicon glyphicon-time'></span> Ver horas</button>";
        $panel.= "</div>";
        $panel.= "<div class='col-sm-4' id=counting_" . $monitor->username . ">";
        $panel.= '<div class="loader"></div>';
        $panel.= "</div>";
        $panel.= "<div class='col-sm-1'><span class='glyphicon glyphicon-chevron-left'></span></div>";
        $panel.= "</div>";
        $panel.= "</div>"; //End panel-heading
        $panel.= "</a>";
        $panel.= "<div id='monitor$monitor->username'  class='show collapse_v2 collapse border_rt' role='tabpanel' aria-labelledby='headingmonitor$monitor->username' aria-expanded='true'>";
        $panel.= "<div class='panel-body'>";
        $panel.= "</div>"; // End panel-body
        $panel.= "</div>"; // End collapse
    }

    return $panel;
}

/**
 * Get the toggle of the practicant with the trackings of each student that belongs to a certain monitor with the implementation of the new form
 *
 * @see render_practicant_new_form($monitors_of_pract)
 * @param $monitors_of_pract --> monitors of practicants
 * @return String
 *
 */

function render_professional_new_form($practicant_of_prof, $instance, $period = null)
{
    $panel = "";
    $practicant_counting = [];
    $current_semester = get_current_semester();
    foreach($practicant_of_prof as $practicant) {
        $panel.= "<div class='panel panel-default'>";
        $practicant_id = $practicant->id_usuario;
        $monitors_of_pract = get_monitors_of_pract($practicant_id, $instance);

        // If the professional has associate practitioners with monitors that show

        $panel.= "<a data-toggle='collapse' class='practicant collapsed btn btn-danger btn-univalle btn-card collapsed' data-parent='#accordion_practicant' style='text-decoration:none' href='#practicant" . $practicant->username . "'>";
        $panel.= "<div class='panel-heading heading_practicant_tracking'>";
        $panel.= "<div class='row'><div class='col-sm-5'>";
        $panel.= "<h4 class='panel-title'>";
        $panel.= "$practicant->firstname $practicant->lastname";
        $panel.= "</h4></div>"; //End panel-title
        $panel.= "<div class='col-sm-1'>";
        $panel.= "<span class='glyphicon glyphicon-user subpanel' style='font-size: 20px;'></span> : " . count(get_monitors_of_pract($practicant_id, $instance));
        $panel.= "<br /><span class='glyphicon glyphicon-education subpanel' style='font-size: 20px;'></span> : " . get_quantity_students_by_pract($practicant_id, $instance);
        $panel.= "</div>";
        $panel.= "<div class='col-sm-5' id=counting_" . $practicant->username . ">";
        $panel.= '<div class="loader"></div>';
        $panel.= "</div>";
        $panel.= "<div class='col-sm-1'><span class='glyphicon glyphicon-chevron-left'></span></div>";
        $panel.= "</div>";
        $panel.= "</div>"; //End panel-heading
        $panel.= "</a>";
        $panel.= "<div id='practicant$practicant->username'  class='show collapse_v2 collapse border_rt' role='tabpanel' aria-labelledby='heading_practicant_tracking$practicant->username' aria-expanded='true'>";
        $panel.= "<div class='panel-body'>";
        $panel.= "</div>"; // End panel-body
        $panel.= "</div>"; // End collapse
        $panel.= "</div>"; // End panel-collapse
    }

    return $panel;
}

/**
 * Formatting of array with dates of trackings
 *
 * @see format_dates_trackings($array_peer_trackings_dphpforms)
 * @param array_peer_trackings_dphpforms --> array with trackings of student
 * @return array with formatted dates
 *
 */

function render_student_trackings($peer_tracking_v2)
{
    $form_rendered = '';
    if ($peer_tracking_v2) {
        foreach($peer_tracking_v2[0] as $key => $period) {
            $year_number = $period;
            foreach($period as $key => $tracking) {
                $is_reviewed = false;
                foreach($tracking[record][campos] as $key => $review) {
                    if ($review[local_alias] == 'revisado_profesional') {
                        if ($review[respuesta] == 0) {
                            $form_rendered.= '<div id="dphpforms-peer-record-' . $tracking[record][id_registro] . '" class="card-block dphpforms-peer-record peer-tracking-record-review class-'.$tracking[record][alias].'"  data-record-id="' . $tracking[record][id_registro] . '">Registro:   ' . $tracking[record][alias_key][respuesta] . '</div>';
                            $is_reviewed = true;
                        }
                    }
                }

                if (!$is_reviewed) {
                    $form_rendered.= '<div id="dphpforms-peer-record-' . $tracking[record][id_registro] . '" class="card-block dphpforms-peer-record peer-tracking-record class-'.$tracking[record][alias].'"  data-record-id="' . $tracking[record][id_registro] . '">Registro:   ' . $tracking[record][alias_key][respuesta] . '</div>';
                }
            }
        }
    }

    return $form_rendered;
}

/**
 * Filter the trackings of a monitor that are reviewed by the professional
 *
 * @see filter_trackings_by_review($peer_tracking_v2)
 * @param peer_tracking_v2 --> Array of trackings
 * @return
 *
 */

function filter_trackings_by_review($peer_tracking_v2)
{
    $array_review_trackings_prof = [];
    $array_not_review_trackings_prof = [];
    $array_review_trackings_pract = [];
    $array_not_review_trackings_pract = [];
    if ($peer_tracking_v2) {
        foreach($peer_tracking_v2[0] as $key => $period) {
            $year_number = $period;
            foreach($period as $key => $tracking) {
                $is_reviewed_prof = false;
                $is_reviewed_pract = false;
                foreach($tracking[record][campos] as $key => $review) {
                    if ($review[local_alias] == 'revisado_profesional') {
                        if ($review[respuesta] == 0) {
                            array_push($array_review_trackings_prof, $tracking);
                            $is_reviewed_prof = true;
                        }
                    }

                    if ($review[local_alias] == 'revisado_practicante') {
                        if ($review[respuesta] == 0) {
                            array_push($array_review_trackings_pract, $tracking);
                            $is_reviewed_pract = true;
                        }
                    }
                }

                if (!$is_reviewed_prof) {
                    array_push($array_not_review_trackings_prof, $tracking);
                }

                if (!$is_reviewed_pract) {
                    array_push($array_not_review_trackings_pract, $tracking);
                }
            }
        }
    }

    $counting = [];
    $counting[0] = count($array_review_trackings_prof);
    $counting[1] = count($array_not_review_trackings_prof);
    $counting[2] = count($array_review_trackings_pract);
    $counting[3] = count($array_not_review_trackings_pract);
    return $counting;
}

/**
 * Calculate the  tracking count of the practitioner and professional roles
 *
 * @see auxiliary_specific_counting($user_kind,$user_id,$semester,$instance)
 * @param $user_kind --> Name of role
 * @param $user_id --> id of user
 * @param $semester
 * @param $instance --> id of instance
 * @return Array
 *
 */

function auxiliary_specific_counting($user_kind, $user_id, $semester, $instance)
{
    $array_final = array();
    if ($user_kind == 'profesional_ps') {
        $practicant_of_prof = get_pract_of_prof($user_id, $instance);
        foreach($practicant_of_prof as $practicant) {
            $practicant_id = $practicant->id_usuario;
            $monitors_of_pract = get_monitors_of_pract($practicant_id, $instance);
            $profesional_counting = calculate_specific_counting('PROFESIONAL', $monitors_of_pract, $semester->max, $instance);
            $counting_advice = new stdClass();
            $counting_advice->code = $practicant->username;

            // $counting_advice->html="<h6><p class='text-right'><strong class='subpanel'>RP :</strong><label class='review_prof'>".$profesional_counting[0]."</label> - <strong class='subpanel'> N RP: </strong><label class='not_review_prof'>".$profesional_counting[1]."</label> - <strong class='subpanel'>TOTAL:</strong><label class='total_prof'>".($profesional_counting[0]+$profesional_counting[1])."</label></p><p class='text-right'><strong class='subpanel'>Rp :</strong><label class='review_pract'>".$profesional_counting[2]."</label> - <strong class='subpanel'> N Rp: </strong><label class='not_review_pract'>".$profesional_counting[3]."</label> - <strong class='subpanel'>TOTAL:</strong><label class='total_pract'>".($profesional_counting[2]+$profesional_counting[3])."</label></p></h6>";

            array_push($array_final, $counting_advice);
        }
    }
    else
    if ($user_kind == 'practicante_ps') {
        $monitors_of_pract = get_monitors_of_pract($user_id, $instance);
        foreach($monitors_of_pract as $monitor) {
            $monitor_id = $monitor->id_usuario;
            $practicant_counting = calculate_specific_counting("PRACTICANTE", $monitor, $semester->max, $instance);
            $counting_advice = new stdClass();
            $counting_advice->code = $monitor->username;
            $counting_advice->html = "<h6><p class='text-right'><strong class='subpanel'>RP :</strong><label class='review_prof'>" . $practicant_counting[0] . "</label> - <strong class='subpanel'> N RP: </strong><label class='not_review_prof'>" . $practicant_counting[1] . "</label> - <strong class='subpanel'>TOTAL:</strong><label class='total_prof'>" . ($practicant_counting[0] + $practicant_counting[1]) . "</label></p><p class='text-right'><strong class='subpanel'>Rp :</strong><label class='review_pract'>" . $practicant_counting[2] . "</label> - <strong class='subpanel'> N Rp: </strong><label class='not_review_pract'>" . $practicant_counting[3] . "</label> - <strong class='subpanel'>TOTAL:</strong><label class='total_pract'>" . ($practicant_counting[2] + $practicant_counting[3]) . "</label></p></h6>";
            array_push($array_final, $counting_advice);
        }
    }

    return $array_final;
}

/**
 * Calculate the  tracking count of the practitioner and professional roles
 *
 * @see calculate_specific_counting($user_kind,$person,$dates_interval,$instance)
 * @param $user_kind --> Name of role
 * @param $array_people --> class of monitor or practicant
 * @param $dates_interval
 * @param $instance --> id of instance
 * @return Array
 *
 */

function calculate_specific_counting($user_kind, $person, $dates_interval, $instance)
{
    $new_counting = array();
    $new_counting[0] = 0;
    $new_counting[1] = 0;
    $new_counting[2] = 0;
    $new_counting[3] = 0;
    if ($user_kind == 'PRACTICANTE') {
        $tracking_current_semestrer = get_tracking_current_semester('monitor', $person->id_usuario, $dates_interval);
        $counting_trackings = filter_trackings_by_review($tracking_current_semestrer);
        $new_counting[0]+= $counting_trackings[0];
        $new_counting[1]+= $counting_trackings[1];
        $new_counting[2]+= $counting_trackings[2];
        $new_counting[3]+= $counting_trackings[3];
        return $new_counting;
    }
    else
    if ($user_kind == 'PROFESIONAL') {
        foreach($person as $key => $monitor) {
            $tracking_current_semestrer = get_tracking_current_semester('monitor', $monitor->id_usuario, $dates_interval);
            $counting_trackings = filter_trackings_by_review($tracking_current_semestrer);
            $new_counting[0]+= $counting_trackings[0];
            $new_counting[1]+= $counting_trackings[1];
            $new_counting[2]+= $counting_trackings[2];
            $new_counting[3]+= $counting_trackings[3];
        }
    }

    return $new_counting;
}

/**
 * Create the notice sign of the counts by professional and practicant
 *
 * @see create_counting_advice($user_kind,$result)
 * @param $user_kind --> String with the role of user
 * @param $result --> Array with number of reviewed trackings by profesional (0,1) and
 * practicant (2,3).
 * @return String
 *
 */

function create_counting_advice($user_kind, $result)
{
    $advice = "";
    $advice.= '<h2> INFORMACIÓN DE  ' . $user_kind . '</h2><hr>';
    $advice.= '<div class="row">';
    $advice.= '<div class="col-sm-6">';
    $advice.= '<strong>Profesional</strong><br />';
    $advice.= 'Revisado :' . $result[0] . ' - No revisado : ' . $result[1] . ' -  Total :' . ($result[1] + $result[0]) . '</div>';
    $advice.= '<div class="col-sm-6">';
    $advice.= '<strong>Practicante</strong><br />';
    $advice.= 'Revisado :' . $result[2] . ' - No revisado : ' . $result[3] . ' -  Total :' . ($result[2] + $result[3]) . '</div></div>';
    return $advice;
}

/**
 * Formatting of array with dates of trackings
 *
 * @see format_dates_trackings($array_peer_trackings_dphpforms)
 * @param array_peer_trackings_dphpforms --> array with trackings of student
 * @return array with formatted dates
 *
 */

function format_dates_trackings(&$array_detail_peer_trackings_dphpforms, &$array_tracking_date, &$array_peer_trackings_dphpforms)
{
    foreach($array_peer_trackings_dphpforms->results as & $peer_trackings_dphpforms) {
        array_push($array_detail_peer_trackings_dphpforms, json_decode(dphpforms_get_record($peer_trackings_dphpforms->id_registro, 'fecha')));
    }

    foreach($array_detail_peer_trackings_dphpforms as & $peer_tracking) {
        foreach($peer_tracking->record->campos as & $tracking) {
            if ($tracking->local_alias == 'fecha') {
                array_push($array_tracking_date, strtotime($tracking->respuesta));
            }
        }
    }
}

/**
 * FunciĆ³n que ordena en un array los trackings para imprimir
 *
 * @see format_dates_trackings($array_peer_trackings_dphpforms)
 * @param array_peer_trackings_dphpforms --> array with trackings of student
 * @return array with formatted dates
 *
 */

function trackings_sorting($array_detail_peer_trackings_dphpforms, $array_tracking_date, $array_peer_trackings_dphpforms)
{
    $seguimientos_ordenados = new stdClass();
    $seguimientos_ordenados->index = array();

    // Inicio de ordenamiento

    $periodo_a = [1, 2, 3, 4, 5, 6, 7];

    // periodo_b es el resto de meses;

    for ($x = 0; $x < count($array_tracking_date); $x++) {
        $string_date = $array_tracking_date[$x];
        $array_tracking_date[$x] = getdate($array_tracking_date[$x]);
        $year = $array_tracking_date[$x]['year'];
        if (property_exists($seguimientos_ordenados, $year)) {
            if (in_array($array_tracking_date[$x]['mon'], $periodo_a)) {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach($array_detail_peer_trackings_dphpforms[$y]->record->campos as & $tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_a, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            else {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach($array_detail_peer_trackings_dphpforms[$y]->record->campos as & $tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_b, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        else {
            array_push($seguimientos_ordenados->index, $year);
            $seguimientos_ordenados->$year->year = $year;
            $seguimientos_ordenados->$year->per_a = array();
            $seguimientos_ordenados->$year->per_b = array();
            $seguimientos_ordenados->$year->year = $year;
            if (in_array($array_tracking_date[$x]['mon'], $periodo_a)) {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach($array_detail_peer_trackings_dphpforms[$y]->record->campos as & $tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_a, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            else {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach($array_detail_peer_trackings_dphpforms[$y]->record->campos as & $tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_b, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Fin de ordenamiento

    return $seguimientos_ordenados;
}

// ////////////////////////////////////////////////////////////////////////////////////////////////7

/**
 * Does all management to get a final organized by monitor students array
 *
 * @see monitorUser($pares, $grupal, $codigoMonitor, $noMonitor, $instanceid, $role, $fechas, $sistemas = false, $codigoPracticante = null)
 * @param &$pares --> 'seguimiento de pares' information
 * @param &$grupal --> 'seguimiento grupal' (groupal tracks) information
 * @param $codigoMonitor --> monitor id
 * @param $noMonitor --> monitor number
 * @param $instanceid --> instance id
 * @param $role --> monitor role
 * @param $fechas --> dates interval
 * @param $sistemas = false --> role is not a 'sistemas' one
 * @param $codigoPracticante = null --> practicant id is null
 * @return array with students grouped by monitor
 *
 */

function get_peer_trackings_by_monitor($pares, $grupal, $codigoMonitor, $noMonitor, $instanceid, $role, $fechas, $sistemas = false, $codigoPracticante = null)
{
    $fecha_epoch = [];
    $fecha_epoch[0] = strtotime($fechas[0]);
    $fecha_epoch[1] = strtotime($fechas[1]);
    $semestre_periodo = get_current_semester_byinterval($fechas[0], $fechas[1]);
    $monitorstudents = get_seguimientos_monitor($codigoMonitor, $instanceid, $fecha_epoch, $semestre_periodo);
    return $monitorstudents;
}


function replace_content_inside_delimiters($start, $end, $new, $source)
{
    return preg_replace('#(' . preg_quote($start) . ')(.*?)(' . preg_quote($end) . ')#si', '$1' . $new . '$3', $source);
}

/** 
 * Function that erase parts of toogle according to  user permissions
 * @see show_according_permissions(&$table,$actions)
 * @param $table --> Toogle
 * @param $actions --> user permission (licence)
 * @return array --> toogle
 */

function show_according_permissions(&$table, $actions)
{
    $end = '</div>';
    $replace_with = "";
    $tabla_format = "";
    if (isset($actions->update_assigned_tracking_rt) == 0) {
        $start = '<div class="col-sm-8" id="editar_registro">';
        $table = replace_content_inside_delimiters($start, $end, $replace_with, $table);
    }

    if (isset($actions->delete_assigned_tracking_rt) == 0) {
        $start = '<div class="col-sm-2" id="borrar_registro">';
        $table = replace_content_inside_delimiters($start, $end, $replace_with, $table);
    }

    if (isset($actions->send_observations_rt) == 0) {
        $start = '<div class="col-sm-12" id="enviar_correo">';
        $table = replace_content_inside_delimiters($start, $end, $replace_with, $table);
    }

    if (isset($actions->check_tracking_professional_rt) == 0) {
        $start = '<div class="col-sm-6" id="check_profesional">';
        $table = replace_content_inside_delimiters($start, $end, $replace_with, $table);
    }

    if (isset($actions->check_tracking_intern_rt) == 0) {
        $start = '<div class="col-sm-6" id="check_practicante">';
        $table = replace_content_inside_delimiters($start, $end, $replace_with, $table);
    }

    return $table;
}


/**
 * Gets a select organized by existent periods
 * @see get_period_select($periods)
 * @param $periods ---> existent periods
 * @return string html table
 *
 */

function get_period_select($periods)
{
    $table = "";
    $table.= '<div class="container"><form class="form-inline">';
    $table.= '<div class="form-group"><label for="persona">Periodo</label><select class="form-control" id="periodos">';
    foreach($periods as $period) {
        $table.= '<option value="' . $period->id . '">' . $period->nombre . '</option>';
    }

    $table.= '</select></div>';
    return $table;
}

/**
 * Gets a select organized by users role '_ps'
 * @see get_people_select($people)
 * @param $people ---> existent users
 * @return string html table
 *
 */

function get_people_select($people)
{
    $table = "";
    $table.= '<div class="form-group"><label for="persona">Persona</label><select class="form-control" id="personas">';
    foreach($people as $person) {
        $table.= '<option value="' . $person->id_usuario . '">' . $person->username . " - " . $person->firstname . " " . $person->lastname . '</option>';
    }

    $table.= '</select></div>';
    $table.= '<span class="btn btn-info" id="consultar_persona" type="button">Consultar</span></form></div>';
    return $table;
}

?>
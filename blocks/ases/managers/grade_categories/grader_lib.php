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
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Queries from module grades record (registro de notas)

require_once __DIR__ . '/../../../../config.php';
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->dirroot . '/grade/lib.php';
require_once $CFG->dirroot . '/grade/report/user/lib.php';
require_once $CFG->dirroot . '/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot . '/blocks/ases/managers/lib/lib.php';
require_once $CFG->dirroot . '/grade/report/grader/lib.php';

///******************************************///
///*** Get info global_grade_book methods ***///
///******************************************///

/**
 * Gets course information given its id
 * @see get_info_course($id_curso)
 * @param $id_curso --> course id
 * @return object Containing all relevant course information
 */
function get_info_course($id_curso)
{
    global $DB;
    $course = $DB->get_record_sql("SELECT fullname FROM {course} WHERE id = $id_curso");

    $query_teacher = "SELECT concat_ws(' ',firstname,lastname) AS fullname
           FROM
             (SELECT usuario.firstname,
                     usuario.lastname,
                     userenrol.timecreated
              FROM {course} cursoP
              INNER JOIN {context} cont ON cont.instanceid = cursoP.id
              INNER JOIN {role_assignments} rol ON cont.id = rol.contextid
              INNER JOIN {user} usuario ON rol.userid = usuario.id
              INNER JOIN {enrol} enrole ON cursoP.id = enrole.courseid
              INNER JOIN {user_enrolments} userenrol ON (enrole.id = userenrol.enrolid
                                                           AND usuario.id = userenrol.userid)
              WHERE cont.contextlevel = 50
                AND rol.roleid = 3
                AND cursoP.id = $id_curso
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc";
    $profesor = $DB->get_record_sql($query_teacher);

    $query_students = "SELECT usuario.id, usuario.firstname, usuario.lastname, usuario.username
                    FROM {user} usuario INNER JOIN {user_enrolments} enrols ON usuario.id = enrols.userid
                    INNER JOIN {enrol} enr ON enr.id = enrols.enrolid
                    INNER JOIN {course} curso ON enr.courseid = curso.id
                    WHERE curso.id= $id_curso AND usuario.id IN (SELECT user_m.id
                                                                FROM {user} user_m
                                                                INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                                                                INNER JOIN {talentospilos_usuario} user_t ON extended.id_ases_user = user_t.id
                                                                INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
                                                                INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
                                                                WHERE estados.nombre = 'seguimiento')";

    $estudiantes = $DB->get_records_sql($query_students);

    $header_categories = get_categories_global_grade_book($id_curso);

    $curso = new stdClass;
    $curso->nombre_curso = $course->fullname;
    $curso->profesor = $profesor->fullname;
    $curso->estudiantes = $estudiantes;
    $curso->header_categories = $header_categories;

    return $curso;
}

/**
 * Returns a string html table with the students, categories and their notes.
 *

 * @see get_categories_global_grade_book($id_curso)
 * @param $id_curso --> course id
 * @return string HTML table
 **/
function get_categories_global_grade_book($id_curso)
{

    global $USER;
    $USER->gradeediting[$id_curso] = 1;

    $context = context_course::instance($id_curso);

    $gpr = new grade_plugin_return(array('type' => 'report', 'plugin' => 'user', 'courseid' => $id_curso));
    $report = new grade_report_grader($id_curso, $gpr, $context);
    // $tabla = $report->get_grade_table();
    // echo htmlspecialchars($tabla);
    $report->load_users();
    $report->load_final_grades();
    return $report->get_grade_table();
}
// print_r(get_categorias_curso(3));

///**********************************///
///***    Update grades methods   ***///
///**********************************///

/**
 * update all grades from a course which needsupdate
 * @see update_grade_items_by_course($course_id)
 * @param $course_id --> id from course to update grade_items
 * @return integer --> 1 if Ok 0 if not
 */

function update_grade_items_by_course($course_id)
{

    // $course_item = grade_item::fetch_course_item($courseid);
    // $course_item->regrading_finished();
    $grade_items = grade_item::fetch_all(array('courseid'=>$course_id, 'needsupdate'=>1));
    foreach ($grade_items as $item) {
        if($item->needsupdate = 1){
            $item->regrading_finished();
        }
    }

    return '1';
}

//update_grade_items_by_course(9);

/**
 * Updates grades from a student
 *

 * @see update_grades_moodle($userid, $itemid, $finalgrade,$courseid)
 * @param $userid --> user id
 * @param $item --> item id
 * @param $finalgrade --> grade value
 * @param $courseid --> course id
 *
 * @return boolean --> true if there's a successful update, false otherwise.

 */

function update_grades_moodle($userid, $itemid, $finalgrade, $courseid)
{
    if (!$grade_item = grade_item::fetch(array('id' => $itemid, 'courseid' => $courseid))) { // we must verify course id here!
        return false;
    }

    if ($grade_item->update_final_grade($userid, $finalgrade, 'gradebook', false, FORMAT_MOODLE)) {
        if ($finalgrade < 3) {
            return send_email_alert($userid, $itemid, $finalgrade, $courseid);
        } else {
            $resp = new stdClass;
            $resp->nota = true;
            return $resp;
        }
    } else {

        $resp = new stdClass;
        $resp->nota = false;

        return $resp;
    }

}

/**
 * Sends an email alert in case a student final grade is less than 3.0
 *
 * @see send_email_alert($userid, $itemid,$grade,$courseid)
 * @param $userid --> user id
 * @param $itemid --> item id
 * @param $grade --> grade value
 * @param $courseid --> course id
 *
 * @return boolean --> true if there's a successful update, false otherwise.
 */

function send_email_alert($userid, $itemid, $grade, $courseid)
{
    global $USER;
    global $DB;

    $resp = new stdClass;
    $resp->nota = true;

    $sending_user = $DB->get_record_sql("SELECT * FROM {user} WHERE username = 'sistemas1008'");

    $userFromEmail = new stdClass;

    $userFromEmail->email = $sending_user->email;
    $userFromEmail->firstname = $sending_user->firstname;
    $userFromEmail->lastname = $sending_user->lastname;
    $userFromEmail->maildisplay = true;
    $userFromEmail->mailformat = 1;
    $userFromEmail->id = $sending_user->id;
    $userFromEmail->alternatename = '';
    $userFromEmail->middlename = '';
    $userFromEmail->firstnamephonetic = '';
    $userFromEmail->lastnamephonetic = '';

    $user_moodle = get_full_user($userid);
    $nombre_estudiante = $user_moodle->firstname . " " . $user_moodle->lastname;

    $subject = "ALERTA ACADÉMICA $nombre_estudiante";

    $curso = $DB->get_record_sql("SELECT fullname, shortname FROM {course} WHERE id = $courseid");
    $nombre_curso = $curso->fullname . " " . $curso->shortname;
    $query_teacher = "SELECT concat_ws(' ',firstname,lastname) AS fullname
           FROM
             (SELECT usuario.firstname,
                     usuario.lastname,
                     userenrol.timecreated
              FROM {course} cursoP
              INNER JOIN {context} cont ON cont.instanceid = cursoP.id
              INNER JOIN {role_assignments} rol ON cont.id = rol.contextid
              INNER JOIN {user} usuario ON rol.userid = usuario.id
              INNER JOIN {enrol} enrole ON cursoP.id = enrole.courseid
              INNER JOIN {user_enrolments} userenrol ON (enrole.id = userenrol.enrolid
                                                           AND usuario.id = userenrol.userid)
              WHERE cont.contextlevel = 50
                AND rol.roleid = 3
                AND cursoP.id = $courseid
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc";
    $profesor = $DB->get_record_sql($query_teacher)->fullname;
    $item = $DB->get_record_sql("SELECT itemname FROM {grade_items} WHERE id = $itemid");
    $itemname = $item->itemname;
    $nota = number_format($grade, 2);
    $nom_may = strtoupper($nombre_curso);
    $titulo = "<b>ALERTA ACADÉMICA CURSO $nom_may <br> PROFESOR: $profesor</b><br> ";
    $mensaje = "Se le informa que se ha presentado una alerta académica del estudiante $nombre_estudiante en el curso $nombre_curso<br>
        El estudiante ha obtenido la siguiente calificación:<br> <br> <b>$itemname: <b> $nota <br><br>
        Cordialmente<br>
        <b>Oficina TIC<br>
        Estrategia ASES<br>
        Universidad del Valle</b>";

    $user_ases = get_adds_fields_mi($userid);
    $id_tal = $user_ases->idtalentos;

    $monitor = get_assigned_monitor($id_tal);
    $nombre_monitor = $monitor->firstname . " " . $monitor->lastname;
    $saludo_mon = "Estimado monitor $nombre_monitor<br><br>";

    $monitorToEmail = new stdClass;
    $monitorToEmail->email = $monitor->email;
    $monitorToEmail->firstname = $monitor->firstname;
    $monitorToEmail->lastname = $monitor->lastname;
    $monitorToEmail->maildisplay = true;
    $monitorToEmail->mailformat = 1;
    $monitorToEmail->id = $monitor->id;
    $monitorToEmail->alternatename = '';
    $monitorToEmail->middlename = '';
    $monitorToEmail->firstnamephonetic = '';
    $monitorToEmail->lastnamephonetic = '';

    $messageHtml_mon = $titulo . $saludo_mon . $mensaje;
    $messageText_mon = html_to_text($messageHtml_mon);

    $email_result = email_to_user($monitorToEmail, $userFromEmail, $subject, $messageText_mon, $messageHtml_mon, ", ", true);

    if ($email_result != 1) {
        $resp->monitor = false;
    } else {
        $resp->monitor = true;

        $practicante = get_assigned_pract($id_tal);
        $nombre_practicante = $practicante->firstname . " " . $practicante->lastname;
        $saludo_prac = "Estimado practicante $nombre_practicante<br><br>";

        $practicanteToEmail = new stdClass;
        $practicanteToEmail->email = $practicante->email;
        $practicanteToEmail->firstname = $practicante->firstname;
        $practicanteToEmail->lastname = $practicante->lastname;
        $practicanteToEmail->maildisplay = true;
        $practicanteToEmail->mailformat = 1;
        $practicanteToEmail->id = $practicante->id;
        $practicanteToEmail->alternatename = '';
        $practicanteToEmail->middlename = '';
        $practicanteToEmail->firstnamephonetic = '';
        $practicanteToEmail->lastnamephonetic = '';

        $messageHtml_prac = $titulo . $saludo_prac . $mensaje;
        $messageText_prac = html_to_text($messageHtml_prac);

        $email_result_prac = email_to_user($practicanteToEmail, $userFromEmail, $subject, $messageText_prac, $messageHtml_prac, ", ", true);

        if ($email_result_prac != 1) {
            $resp->practicante = false;
        } else {
            $resp->practicante = true;

            $profesional = get_assigned_professional($id_tal);
            $nombre_profesional = $profesional->firstname . " " . $profesional->lastname;
            $saludo_prof = "Estimado profesional $nombre_profesional<br><br>";

            $profesionalToEmail = new stdClass;
            $profesionalToEmail->email = $profesional->email;
            $profesionalToEmail->firstname = $profesional->firstname;
            $profesionalToEmail->lastname = $profesional->lastname;
            $profesionalToEmail->maildisplay = true;
            $profesionalToEmail->mailformat = 1;
            $profesionalToEmail->id = $profesional->id;
            $profesionalToEmail->alternatename = '';
            $profesionalToEmail->middlename = '';
            $profesionalToEmail->firstnamephonetic = '';
            $profesionalToEmail->lastnamephonetic = '';

            $messageHtml_prof = $titulo . $saludo_prof . $mensaje;
            $messageText_prof = html_to_text($messageHtml_prof);

            $email_result_prof = email_to_user($profesionalToEmail, $userFromEmail, $subject, $messageText_prof, $messageHtml_prof, ", ", true);

            if ($email_result_prof != 1) {
                $resp->profesional = false;
            } else {
                $resp->profesional = true;
            }

        }
    }

    return $resp;

}

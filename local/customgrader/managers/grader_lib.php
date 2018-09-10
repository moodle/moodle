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
 * Grader Lib
 *
 * @author     Camilo José Cruz Rivera
 * @package    custom_grader
 * @copyright  2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Queries from module grades record (registro de notas)
require_once 'wizard_lib.php';
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->dirroot . '/grade/lib.php';
require_once $CFG->dirroot . '/grade/report/user/lib.php';
require_once $CFG->dirroot . '/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot . '/blocks/ases/managers/lib/lib.php';
require_once $CFG->dirroot . '/grade/report/grader/lib.php';

////////////////////////////////////////////////////////////////////////////////////////////
////SOLO RAMA UNIVALLE

/**
 * Gets course information given its id
 * @see get_info_students($id_curso)
 * @param $id_curso --> course id
 * @return array Containing all ases students in the course
 */
function get_info_students($id_curso)
{
    global $DB;
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
    return $estudiantes;
}
////////////////////////////////////////////////////////////////////////////////////////////



///******************************************///
///*** Get info global_grade_book methods ***///
///******************************************///

/**
 * Returns a string with the teacher from a course.
 *

 * @see getTeacher($id_curso)
 * @param $id_curso --> course id
 * @return string $teacher_name
 **/

function getTeacher($id_curso)
{
    global $DB;
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
    return $profesor->fullname;
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
    $grade_items = grade_item::fetch_all(array('courseid' => $course_id, 'needsupdate' => 1));
    foreach ($grade_items as $item) {
        if ($item->needsupdate = 1) {
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
        $resp = new stdClass;
        $resp->nota = true;
        return $resp;
    } else {

        $resp = new stdClass;
        $resp->nota = false;

        return $resp;
    }

}

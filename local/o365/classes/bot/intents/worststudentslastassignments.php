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
 * Class worststudentslastassignments implements bot intent interface for teacher-worst-students-last-assignment intent.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot\intents;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/assign/locallib.php');

/**
 * Class worststudentslastassignments implements bot intent interface for teacher-worst-students-last-assignment intent.
 */
class worststudentslastassignments implements \local_o365\bot\intents\intentinterface {

    /**
     * Gets a message for teachers with the list of students who did the worst in last assignment
     * @param string $language - Message language
     * @param mixed $entities - Intent entities. Gives student name.
     * @return array|string - Bot message structure with data
     */
    public static function get_message($language, $entities = null) {
        global $USER, $DB, $PAGE;
        $listitems = [];
        $warnings = [];
        $listtitle = '';
        $message = '';

        $courses = \local_o365\bot\intents\intentshelper::getteachercourses($USER->id);

        if (!empty($courses)) {
            list($coursessql, $coursesparams) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
            $sql = "SELECT ag.assignment
                      FROM {assign_grades} ag
                      JOIN {assign} a ON a.id = ag.assignment
                     WHERE a.course $coursessql
                  ORDER BY ag.timemodified DESC
                     LIMIT 1";
            $assignment = $DB->get_field_sql($sql, $coursesparams);
        }

        if (empty($assignment)) {
            $message = get_string_manager()->get_string('no_graded_assignments_found', 'local_o365', null, $language);
            $warnings[] = array(
                    'item' => 'assignments',
                    'itemid' => 0,
                    'warningcode' => '1',
                    'message' => 'No  graded assignments found'
            );
        } else {
            $cm = get_coursemodule_from_instance('assign', $assignment);
            $listtitle = get_string_manager()->get_string('assignment', 'local_o365', null, $language) . ' - ' . $cm->name;
            $message = get_string_manager()->get_string('list_of_students_with_least_score', 'local_o365', null, $language);
            $sql = 'SELECT *
                      FROM {assign_grades}
                     WHERE assignment = :aid AND grade != :gradenotgraded
                  ORDER BY grade ASC';
            $params = ['aid' => $assignment, 'gradenotgraded' => ASSIGN_GRADE_NOT_SET];
            $grades = $DB->get_records_sql($sql, $params, 0, self::DEFAULT_LIMIT_NUMBER);
            $usersids = array_map(
                function($grade){
                    return $grade->userid;
                },
                $grades);
            if (!empty($usersids)) {
                list($userssql, $usersparams) = $DB->get_in_or_equal($usersids, SQL_PARAMS_NAMED);
                $users = $DB->get_records_sql("SELECT id, username, firstname, lastname
                                                 FROM {user}
                                                WHERE id $userssql", $usersparams);
                foreach ($grades as $g) {
                    $user = $users[$g->userid];
                    $userpicture = new \user_picture($user);
                    $userpicture->size = 1;
                    $pictureurl = $userpicture->get_url($PAGE)->out(false);
                    $subtitledata = new \stdClass();
                    $subtitledata->grade = number_format((float)$g->grade, 1, '.', '');
                    $subtitledata->date = \local_o365\bot\intents\intentshelper::formatdate($g->timemodified);
                    $grade = array(
                        'title' => $user->firstname . ' ' . $user->lastname,
                        'subtitle' => get_string_manager()->get_string('grade_date', 'local_o365', $subtitledata, $language),
                        'icon' => $pictureurl,
                        'action' => null,
                        'actionType' => null
                    );
                    $listitems[] = $grade;
                }
            }
            if (empty($listitems)) {
                $message = get_string_manager()->get_string('no_grades_found', 'local_o365', null, $language);
                $warnings[] = array(
                        'item' => 'grades',
                        'itemid' => 0,
                        'warningcode' => '2',
                        'message' => 'No grades found'
                );
            }
        }

        return array(
                'message' => $message,
                'listTitle' => $listtitle,
                'listItems' => $listitems,
                'warnings' => $warnings
        );
    }
}

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
 * Class dueassignments implements bot intent interface for student-due-assignments.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot\intents;

require_once($CFG->dirroot . '/mod/assign/lib.php');

/**
 * Class dueassignments implements bot intent interface for student-due-assignments.
 */
class dueassignments implements \local_o365\bot\intents\intentinterface {

    /**
     * Gets a message with details about student due assignments.
     *
     * @param string $language - Message language
     * @param mixed $entities - Intent entities (optional and not used at the moment)
     * @return array|string - Bot message structure with data
     */
    public static function get_message($language, $entities = null) {
        global $USER, $DB, $OUTPUT;
        $listitems = [];
        $warnings = [];
        $listtitle = '';
        $message = '';

        $fields = 'sortorder,shortname,fullname,timemodified';

        // We need to check for enrolments.
        $courses = enrol_get_users_courses($USER->id, true, $fields);
        $courses = array_keys($courses);
        if (!empty($courses)) {
            $message = get_string_manager()->get_string('list_of_due_assignments', 'local_o365', null, $language);
            list($coursessql, $coursesparams) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
            $assignments = $DB->get_records_sql("SELECT * FROM {assign} a WHERE a.course $coursessql".
                " AND a.duedate > UNIX_TIMESTAMP() ORDER BY a.duedate ASC", $coursesparams);
            foreach ($assignments as $assignment) {
                $cm = get_coursemodule_from_instance('assign', $assignment->id);
                $course = get_course($assignment->course);
                if (\assign_get_completion_state($course, $cm, $USER->id, false)) {
                    continue;
                }

                $url = new \moodle_url('/mod/assign/view.php', ['id' => $cm->id]);
                $subtitledata = \local_o365\bot\intents\intentshelper::formatdate($assignment->duedate);
                $assignment = array(
                        'title' => $assignment->name,
                        'subtitle' => get_string_manager()->get_string('due_date', 'local_o365', $subtitledata, $language),
                        'icon' => $OUTPUT->image_url('icon', 'assign')->out(),
                        'action' => $url->out(false),
                        'actionType' => 'openUrl'
                );
                $listitems[] = $assignment;
                if (count($listitems) == self::DEFAULT_LIMIT_NUMBER) {
                    break;
                }
            }
        }
        if (empty($listitems)) {
            $message = get_string_manager()->get_string('no_due_assignments_found', 'local_o365', null, $language);
            $warnings[] = array(
                    'item' => 'assignments',
                    'itemid' => 0,
                    'warningcode' => '1',
                    'message' => 'No due assignments found'
            );
        }

        return array(
                'message' => $message,
                'listTitle' => $listtitle,
                'listItems' => $listitems,
                'warnings' => $warnings
        );
    }
}

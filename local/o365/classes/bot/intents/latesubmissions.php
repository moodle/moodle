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
 * Class latesubmissions implements bot intent interface for teacher-late-submissions intent.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot\intents;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Class latesubmissions implements bot intent interface for teacher-late-submissions intent.
 */
class latesubmissions implements \local_o365\bot\intents\intentinterface {

    /**
     * Gets a message for teachers with the list of students late submissions.
     *
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
            list($statussql, $statusparams) = $DB->get_in_or_equal(ASSIGN_SUBMISSION_STATUS_SUBMITTED, SQL_PARAMS_NAMED);
            $sql = "SELECT ass.id, ass.userid, ass.assignment, ass.timemodified, a.duedate, co.fullname as coursename
                      FROM {assign_submission} ass
                      JOIN {assign} a ON ass.assignment = a.id
                      JOIN {course} co ON co.id = a.course
                     WHERE a.course $coursessql AND ass.status $statussql
                           AND a.duedate < ass.timemodified
                  ORDER BY ass.timecreated DESC";
            $sqlparams = array_merge($coursesparams, $statusparams);
            $submissions = $DB->get_records_sql($sql, $sqlparams, 0, self::DEFAULT_LIMIT_NUMBER);
        } else {
            $submissions = [];
        }

        if (empty($submissions)) {
            $message = get_string_manager()->get_string('no_late_submissions_found', 'local_o365', null, $language);
            $warnings[] = array(
                    'item' => 'submissions',
                    'itemid' => 0,
                    'warningcode' => '1',
                    'message' => 'No  late submissions found'
            );
        } else {
            $message = get_string_manager()->get_string('list_of_late_submissions', 'local_o365', null, $language);
            foreach ($submissions as $submission) {
                $cm = get_coursemodule_from_instance('assign', $submission->assignment);
                $user = $DB->get_record('user', ['id' => $submission->userid], 'id, username, firstname, lastname');
                $userpicture = new \user_picture($user);
                $userpicture->size = 1;
                $pictureurl = $userpicture->get_url($PAGE)->out(false);
                $url = new \moodle_url('/mod/assign/view.php',
                        ['action' => 'grading', 'id' => $cm->id, 'tsort' => 'timesubmitted']);
                $subtitledata = new \stdClass();
                $subtitledata->course = $submission->coursename;
                $subtitledata->assignment = $cm->name;
                $subtitledata->submittedon = \local_o365\bot\intents\intentshelper::formatdate($submission->timemodified);
                $subtitledata->duedate = \local_o365\bot\intents\intentshelper::formatdate($submission->duedate);
                $record = array(
                        'title' => $user->firstname . ' ' . $user->lastname,
                        'subtitle' => get_string_manager()->get_string('course_assignment_submitted_due', 'local_o365',
                                $subtitledata, $language),
                        'icon' => $pictureurl,
                        'action' => $url->out(false),
                        'actionType' => 'openUrl'
                );
                $listitems[] = $record;
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

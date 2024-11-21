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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/intelliboard/locallib.php');
require_once($CFG->dirroot .'/local/intelliboard/lib.php');

class local_intelliboard_observer
{
    public static function role_assigned(core\event\role_assigned $event)
    {
        $data = $event->get_data();
        $relatedUser = $data['relateduserid'];

        self::process_event(2, $event, [], ['users' => $relatedUser, 'courses' => $data['courseid']]);
    }

    protected static function process_event($type, $event, $filter = array(), $ex_params = array())
    {
        $notification = new local_intelliboard_notification();
        $excluded = exclude_not_owners($ex_params);
        $notifications = $notification->get_instant_notifications($type, $filter, $excluded);
        $notification->send_notifications($notifications, $event);
    }

    public static function role_unassigned(core\event\role_unassigned $event)
    {
        $data = $event->get_data();
        $relatedUser = $data['relateduserid'];

        self::process_event(2, $event, array(), array('users' => $relatedUser, 'courses' => $data['courseid']));
    }

    public static function post_created(mod_forum\event\post_created $event)
    {
        $eventData = $event->get_data();
        $data = array(
            'forums' => $eventData['other']['forumid'],
            'course' => $eventData['courseid']
        );

        self::process_event(12, $event, $data,
            array('users' => $eventData['userid'], 'courses' => $eventData['courseid']));
    }

    public static function user_graded(\core\event\user_graded $event)
    {
        global $DB;
        $allowedTypes = ['assign', 'quiz'];
        $eventData = $event->get_data();

        $itemid = $eventData['other']['itemid'];
        $item = $DB->get_record('grade_items', ['id' => $itemid], "itemmodule");
        $excluded = ['users' => $eventData['relateduserid'], 'courses' => $eventData['courseid']];

        if (in_array($item->itemmodule, $allowedTypes)) {
            $data = ['course' => $eventData['courseid']];
            self::process_event(13, $event, $data, $excluded);
        }

        if (!$item->itemmodule) { //it's course grade updated
            $data = ['user' => $eventData['relateduserid'], 'course' => $eventData['courseid']];
            $courseGrade = $DB->get_record_sql("
            SELECT
                ROUND((CASE WHEN SUM(g.rawgrademax) > 0 THEN (SUM(g.finalgrade) / SUM(g.rawgrademax)) * 100 ELSE SUM(g.finalgrade) END), 2) as grade
                FROM {grade_grades} as g
                INNER JOIN {grade_items} as gi ON gi.id = g.itemid
                WHERE gi.courseid = ? AND gi.itemtype = 'mod' AND g.userid = ? AND g.finalgrade IS NOT NULL
                GROUP BY gi.courseid
        ", [$eventData['courseid'], $eventData['relateduserid']]);

            if ($courseGrade and isset($courseGrade->grade)) {
                $data['gradeThreshold'] = ['operator' => '>', 'value' => $courseGrade->grade];
                self::process_event(25, $event, $data, $excluded);
            }
        }
    }

    public static function quiz_attempt_submitted(\mod_quiz\event\attempt_submitted $event)
    {
        global $DB;

        $eventData = $event->get_data();

        $isNeededGrading = $DB->get_record_sql("SELECT
            COUNT(qas.id) AS checking
            FROM {question_attempt_steps} qas
            INNER JOIN {question_attempts} qa ON qa.id = qas.questionattemptid
            INNER JOIN {quiz_attempts} q ON q.uniqueid = qa.questionusageid
            WHERE q.id = ? AND qas.state = 'needsgrading'
        ", array($eventData['objectid']));

        if ($isNeededGrading and !empty($isNeededGrading->checking)) {
            self::process_event(15, $event, array(),
                array('users' => $eventData['userid'], 'courses' => $eventData['courseid']));
        }
    }

    public static function assign_attempt_submitted(\mod_assign\event\assessable_submitted $event)
    {
        $eventData = $event->get_data();
        self::process_event(15, $event, array(),
            array('users' => $eventData['userid'], 'courses' => $eventData['courseid']));
    }

    public static function user_enrolment_created(\core\event\user_enrolment_created $event)
    {
        $eventData = $event->get_data();
        $filters = array(
            'course' => $eventData['contextinstanceid']
        );

        self::process_event(23, $event, $filters,
            array('users' => $eventData['relateduserid'], 'courses' => $eventData['contextinstanceid']));
    }

    public static function user_loggedin(\core\event\user_loggedin $event)
    {
        global $CFG;

        $eventData = $event->get_data();
        if (get_config('local_intelliboard', 'instructor_redirect')) {
            $instructor_roles = get_config('local_intelliboard', 'filter10');
            if (!empty($instructor_roles)) {
                $access = false;
                $roles = explode(',', $instructor_roles);
                if (!empty($roles)) {
                    foreach ($roles as $role) {
                        if ($role and user_has_role_assignment($eventData['userid'], $role)) {
                            $access = true;
                            break;
                        }
                    }
                    if ($access) {
                        redirect("$CFG->wwwroot/local/intelliboard/instructor/index.php");
                    }
                }
            }
        }

        if (get_config('local_intelliboard', 'student_redirect')) {
            $student_roles = get_config('local_intelliboard', 'filter11');
            if (!empty($student_roles)) {
                $access = false;
                $roles = explode(',', $student_roles);
                if (!empty($roles)) {
                    foreach ($roles as $role) {
                        if ($role and user_has_role_assignment($eventData['userid'], $role)) {
                            $access = true;
                            break;
                        }
                    }
                    if ($access) {
                        redirect("$CFG->wwwroot/local/intelliboard/student/index.php");
                    }
                }
            }
        }

        return true;
    }

    public static function course_completed(\core\event\course_completed $event)
    {
        $eventData = $event->get_data();
        $filters = array(
            'course' => $eventData['courseid']
        );

        self::process_event(30, $event, $filters,
            array('users' => $eventData['userid'], 'courses' => $eventData['courseid']));
    }

    public static function resource_viewed(\mod_resource\event\course_module_viewed $event) {
        if (get_config('local_intelliboard', 'enabled')) {
            local_intelliboard_insert_tracking(false, [
                'page' => 'module',
                'param' => $event->contextinstanceid,
                'time' => 1
            ]);
        }
    }
}

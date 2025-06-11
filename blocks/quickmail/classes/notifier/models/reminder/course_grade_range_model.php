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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\notifier\models\reminder;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\notifier\models\interfaces\reminder_notification_model_interface;
use block_quickmail\notifier\models\reminder_notification_model;
use block_quickmail\services\grade_calculator\course_grade_calculator;

class course_grade_range_model extends reminder_notification_model implements reminder_notification_model_interface {

    public static $objecttype = 'course';

    public static $conditionkeys = [
        'grade_greater_than',
        'grade_less_than',
    ];

    /**
     * Returns an array of user ids to be notified based on this reminder_notification_model's conditions
     *
     * @return array
     */
    public function get_user_ids_to_notify() {
        // Make sure a grade_greater_than boundary is set.
        if (!$greaterthan = $this->condition->get_value('grade_greater_than')) {
            $greaterthan = 0;
        }

        // Make sure a grade_less_than boundary is set.
        if (!$lessthan = $this->condition->get_value('grade_less_than')) {
            $lessthan = 0;
        }

        // Get distinct user ids.
        // Where users are in a specific course.
        global $DB;

        $queryresults = $DB->get_records_sql('SELECT u.id
            FROM {user} u
            INNER JOIN {user_enrolments} ue ON ue.userid = u.id
            INNER JOIN {enrol} e ON e.id = ue.enrolid
            INNER JOIN {course} c ON c.id = e.courseid
            INNER JOIN {role_assignments} ra ON ra.userid = u.id
            INNER JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.instanceid = c.id
            WHERE ra.roleid IN (SELECT value FROM {config} WHERE name = "gradebookroles")
            AND c.id = ?
            GROUP BY u.id', [$this->get_course_id()]);

        $courseuserids = array_keys($queryresults);

        // Set a default return container.
        $results = [];

        // Attempt to instantiate a grade calculator for this course.
        // If it cannot be constructed, fail gracefully by returning empty results.
        if (!$calculator = course_grade_calculator::for_course($this->get_course_id())) {
            return $results;
        }

        foreach ($courseuserids as $userid) {
            try {
                // Fetch "round" grade for this course user.
                $roundgrade = $calculator->get_user_course_grade($userid, 'round');

                // The user's calculated grade falls within the boundaries.
                if ($roundgrade >= $greaterthan && $roundgrade <= $lessthan) {
                    // Add to the results.
                    $results[] = $userid;
                }
            } catch (\Exception $e) {
                // Maybe we couldn't instantiate the calcuator?
                $results[] = $e->getMessage;
            }
        }

        return $results;
    }

}

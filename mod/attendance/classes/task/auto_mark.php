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
 * Attendance task - auto mark.
 *
 * @package    mod_attendance
 * @copyright  2017 onwards Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\task;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/attendance/locallib.php');
require_once($CFG->libdir . '/grouplib.php');
/**
 * get_scores class, used to get scores for submitted files.
 *
 * @package    mod_attendance
 * @copyright  2017 onwards Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auto_mark extends \core\task\scheduled_task {

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public function get_name() {
        // Shown in admin screens.
        return get_string('automarktask', 'mod_attendance');
    }

    /**
     * Execte the task.
     */
    public function execute() {
        global $DB;
        // Create some cache vars - might be nice to restructure this and make a smaller number of sql calls.
        $cachecm = array();
        $cacheatt = array();
        $cachecourse = array();
        $sql = "SELECT se.*, ss.id as setunmarked
                  FROM {attendance_sessions} se
             LEFT JOIN {attendance_statuses} ss ON ss.attendanceid = se.attendanceid
                       AND ss.setunmarked = 1 AND ss.deleted = 0 AND ss.setnumber = se.statusset
             WHERE se.automark > 0 AND se.automarkcompleted < 2 AND se.sessdate < ?";

        $sessions = $DB->get_recordset_sql($sql, [time()]);

        foreach ($sessions as $session) {
            if (empty($cacheatt[$session->attendanceid])) {
                $cacheatt[$session->attendanceid] = $DB->get_record('attendance', array('id' => $session->attendanceid));
            }
            if (empty($cachecm[$session->attendanceid])) {
                $cachecm[$session->attendanceid] = get_coursemodule_from_instance('attendance',
                    $session->attendanceid, $cacheatt[$session->attendanceid]->course);
            }
            $courseid = $cacheatt[$session->attendanceid]->course;
            if (empty($cachecourse[$courseid])) {
                $cachecourse[$courseid] = $DB->get_record('course', array('id' => $courseid));
            }
            \mod_attendance\local\automark::session($session, $cachecourse[$courseid], $cachecm[$session->attendanceid],
                                                    $cacheatt[$session->attendanceid]);

        }
    }
}

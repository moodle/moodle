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
 * An adhoc task for local Iomad track
 *
 * @package    local_iomad_track
 * @copyright  2020 E-Learn Design https://www.e-learndesign.co.uk
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_iomad_track\task;

defined('MOODLE_INTERNAL') || die();

use core\task\adhoc_task;

class fixenrolleddatetask extends adhoc_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('fixenrolleddatetask', 'local_iomad_track');
    }

    /**
     * Run fixenrolleddatetask
     */
    public function execute() {
        global $DB;

        // Get all of the entries currently in course_completions.
        $allentries = $DB->get_records('course_completions', array());

        // Process them.
        foreach ($allentries as $entry) {
            // Get the enrolment record.
            if ($userenrolment = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue
                                                 JOIN {enrol} e ON (ue.enrolid = e.id)
                                                 WHERE ue.userid = :userid
                                                 AND e.courseid = :courseid
                                                 AND e.status = 0",
                                                 array('userid' => $entry->userid,
                                                       'courseid' => $entry->course))) {
                if ($userenrolment->timestart == $userenrolment->timecreated) {
                    // Don't care about this.
                    continue;
                } else if ($entry->timeenrolled == $userenrolment->timecreated) {
                    // Also don't care.
                    continue;
                } else {
                    // Get the local_iomad_track record if it's wrong.
                    if ($trackrec = $DB->get_record('local_iomad_track', array('userid' => $entry->userid, 'courseid' => $entry->course, 'timeenrolled' => $userenrolment->timestart))) {
                        // Update both this and course_completions.
                        $trackrec->timeenrolled = $userenrolment->timecreated;
                        $DB->update_record('local_iomad_track', $trackrec);
                        $entry->timeenrolled = $userenrolment->timecreated;
                        $DB->update_record('course_completions', $entry);
                    }
                }
            }
        }
    }

    /**
     * Queues the task.
     *
     */
    public static function queue_task() {

        // Let's set up the adhoc task.
        $task = new \local_iomad_track\task\fixenrolleddatetask();
        \core\task\manager::queue_adhoc_task($task, true);
    }
}

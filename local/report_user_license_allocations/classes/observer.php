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
 * @package   local_report_license_usage
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_report_user_license_allocations;

class observer {

    /**
     * Consume user_license_assigned event
     * @param object $event the event object
     */
    public static function user_license_assigned($event) {
        global $DB;

        // Add the event.
        $DB->insert_record('local_report_user_lic_allocs', array('userid' => $event->userid,
                                                                 'licenseid' => $event->other['licenseid'],
                                                                 'issuedate' => $event->other['issuedate'],
                                                                 'courseid' => $event->courseid,
                                                                 'action' => 1,
                                                                 'modifiedtime' => time()));

        return true;
    }

    /**
     * Consume user_license_unassigned event
     * @param object $event the event object
     */
    public static function user_license_unassigned($event) {
        global $DB;

        // Add the event.
        $user = $DB->get_record('user', array('id' => $event->userid));
        $DB->insert_record('local_report_user_lic_allocs', array('userid' => $event->userid,
                                                                 'licenseid' => $event->other['licenseid'],
                                                                 'issuedate' => $event->timecreated,
                                                                 'courseid' => $event->courseid,
                                                                 'action' => 0,
                                                                 'modifiedtime' => time()));

        return true;
    }
}

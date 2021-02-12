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
 * @package   local_report_user_logins
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_report_user_logins;

class observer {

    /**
     * Consume user_loggedin event
     * @param object $event the event object
     */
    public static function user_loggedin($event) {
        global $DB;

        // Get the relevant event date (course_completed event).
        $data = $event->get_data();
        $userid = $data['userid'];

        // Does it alreay exist?
        if ($current = $DB->get_record('local_report_user_logins', array('userid' => $userid))) {
            // Check if this is first log on.
            if (empty($current->firstlogin)) {
                $userrec = $DB->get_record('user', array('id' => $userid));
                $DB->set_field('local_report_user_logins', 'firstlogin', $userrec->firstaccess, array('id' => $current->id));
            }

            // update it.
            $DB->set_field('local_report_user_logins', 'logincount', $current->logincount + 1, array('id' => $current->id));
            $DB->set_field('local_report_user_logins', 'lastlogin', $data['timecreated'], array('id' => $current->id));
            $DB->set_field('local_report_user_logins', 'modifiedtime', $data['timecreated'], array('id' => $current->id));
        } else {
           // Doesn't exist but should. Create it!
           $user = $DB->get_record('user', array('id' => $userid));
           $totallogins = $DB->count_records('logstore_standard_log', array('userid' => $user->id, 'eventname' => '\core\event\user_loggedin'));
           $DB->insert_record('local_report_user_logins', array('userid' => $user->id,
                                                                'created' => $user->timecreated,
                                                                'firstlogin' => $user->firstaccess,
                                                                'lastlogin' => $user->currentlogin,
                                                                'logincount' => $totallogins,
                                                                'modifiedtime' => time()));
        }

        return true;
    }

    /**
     * Consume user_created event
     * @param object $event the event object
     */
    public static function user_created($event) {
        global $DB;

        // Get the relevant event date (course_completed event).
        $data = $event->get_data();
        $userid = $data['relateduserid'];

        // Add the user.
        $user = $DB->get_record('user', array('id' => $userid));
        $DB->insert_record('local_report_user_logins', array('userid' => $user->id,
                                                             'created' => $user->timecreated,
                                                             'firstlogin' => null,
                                                             'lastlogin' => null,
                                                             'logincount' => 0,
                                                             'modifiedtime' => time()));

        return true;
    }
}

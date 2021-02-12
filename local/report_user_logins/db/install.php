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

defined('MOODLE_INTERNAL') || die();

function xmldb_local_report_user_logins_install() {
    global $CFG, $DB;

     upgrade_set_timeout(7200); // Set installation time to 2 hours as this takes a long time.

    // Populate the report table from any previous users.
    $users = $DB->get_records('user', array('deleted' => 0));
    $total = count($users);
    mtrace("Dealing with $total users");
    $count = 0;
    $warn = 10;
    foreach ($users as $user) {
        if ($user->currentlogin != 0) {
            $totallogins = $DB->count_records('logstore_standard_log', array('userid' => $user->id, 'eventname' => '\core\event\user_loggedin'));
        } else {
            $totallogins = 0;
        }
        if ($user->currentlogin == 0 ) {
            $user->currentlogin = null;
        }

        if ($user->firstaccess == 0 ) {
            $user->firstaccess = null;
        }

        $DB->insert_record('local_report_user_logins', array('userid' => $user->id,
                                                             'created' => $user->timecreated,
                                                             'firstlogin' => $user->firstaccess,
                                                             'lastlogin' => $user->currentlogin,
                                                             'logincount' => $totallogins,
                                                             'modifiedtime' => time()));
        $count++;
        if ($count * 100 / $total > $warn) {
            mtrace ("$warn%");
            $warn = $warn + 10;
        }
    }

    // Deal with any that may have been missed.
    if ($missedusers = $DB->get_records_sql("SELECT u.* FROM {user} u
                                             JOIN {local_report_user_logins} lrul ON (u.id = lrul.userid)
                                             WHERE lrul.logincount = 0
                                             AND u.currentlogin != 0")) {
        foreach ($missedusers as $missed) {
            // Not in the logs to we are going to set it to 1 as it's the only evidence we have.
            $DB->set_field('local_report_user_logins', 'logincount', 1, array('userid' => $missed->id));
            $DB->set_field('local_report_user_logins', 'lastlogin', $missed->currentlogin, array('userid' => $missed->id));
            echo ".";
        }
    }
}

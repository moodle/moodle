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
 * @package    enrol_d1
 * @copyright  2022 onwards LSUOnline & Continuing Education
 * @copyright  2022 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
    **********************************************************
    * This is only a test file and will not be used anywhere *
    **********************************************************
*/

// Make sure this can only run via CLI.
define('CLI_SCRIPT', true);

// Include the main Moodle config.
require(__DIR__ . '/../../../config.php');

// This is so we can use the CFG var.
global $CFG;

// Include the CLI lib so we can do this stuff via CLI.
require_once("$CFG->libdir/clilib.php");
require_once("$CFG->libdir/moodlelib.php");

$users = d1hate::grab_users_to_delete();
foreach ($users as $user) {
    // mtrace("Deleting $user->username, with id: $user->id and email: $user->email.")
    $delete = delete_user($user);
    if ($delete) {
       mtrace("Deleted $user->username, with id: $user->id and email: $user->email.");
    }
}

class d1hate {
    public static function grab_users_to_delete() {
        global $DB;

        $sql = 'SELECT u1.*
                FROM mdl_user u1
                  INNER JOIN mdl_user u2 ON u1.email = u2.email
                    AND u1.id <> u2.id
                    AND u1.lastname = u2.lastname
                WHERE u1.deleted = 0
                  AND u2.deleted = 0
                  AND u1.suspended = 0
                  AND u2.suspended = 0
                  AND u1.email <> ""
                  AND u1.idnumber LIKE "X%"
                  AND u1.password = ""
                GROUP BY u1.id
                ORDER BY u1.id ASC';

        $sql = 'SELECT * FROM mdl_user WHERE email LIKE "%@example.com" AND password = ""';

        $users = $DB->get_records_sql($sql);
        return $users;
    }
}

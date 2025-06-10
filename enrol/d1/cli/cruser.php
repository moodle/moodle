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

// Require the main D1 class.
require_once('../classes/d1.php');

$students = lsud1::get_d1students($all=false);
$count = count($students);
mtrace("Updating $count students");

foreach ($students as $student) {
  $update = itrulyhated1::create_update_user2($student);
  if (isset($update->id)) {
      mtrace("Updated $student->id to match with $update->id using $student->email.");
  } else {
      mtrace("Failed to match a single student for id: $student->id using email: $student->email and username: $student->username.");
  }
}

class itrulyhated1 {
    /**
     * Creates or updates users as needed.
     *
     * @param  @object $d1student
     * @return @object $uo
     */
    public static function create_update_user2($d1student, $courseid=null) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->libdir . '/moodlelib.php');

        // Grab the table for future use.
        $table = 'user';

        // Build the auth methods and choose the default one.
        $auth = explode(',', $CFG->auth);
        $auth = reset($auth);

        // TODO: deal with this somehow.
        $auth = 'manual';


        // Set up the user object.
        $user               = new stdClass();
        $user->username     = $d1student->username;
        $user->idnumber     = $d1student->idnumber;
        $user->email        = $d1student->email;
        $user->firstname    = $d1student->firstname;
        $user->lastname     = $d1student->lastname;
        $user->lang         = $CFG->lang;
        $user->auth         = $auth;
        $user->confirmed    = 1;
        $user->timemodified = time();
        $user->mnethostid   = $CFG->mnet_localhost_id;

        if (isset($d1student->userid)) {
            $mconditions = array("id" => $d1student->userid, "mnethostid" => 1, "deleted" => 0);
            $uo = $DB->get_record($table, $mconditions, $fields='*', $strictness=IGNORE_MISSING);
            if (isset($uo->id)) {
                // mtrace("We already have a matching user in Moodle for $d1student->username - $uo->username, skipping");
                return $uo;
            }
        }

        // Build the conditions to get some users.
        $uconditions = array("username"=>$d1student->username, "mnethostid"=>1, "deleted"=>0, "confirmed"=>1, "suspended"=>0);
        $iconditions = array("idnumber"=>$d1student->idnumber, "mnethostid"=>1, "deleted"=>0, "confirmed"=>1, "suspended"=>0);
        $econditions = array("email"=>$d1student->email, "mnethostid"=>1, "deleted"=>0, "confirmed"=>1, "suspended"=>0);

        // Set up the users array and grab users if they exist.
        $users = array();

        // mtrace("Checking and updating record for user: $d1student->username, $d1student->idnumber, $d1student->email.");

        // Get users that match the email address provided above.
        $users['email']    = $DB->get_records($table, $econditions, 'id', $fields='*');

        // If we have more than one user returned for an email address, so some stuff.
        if (is_array($users['email']) && count($users['email']) > 1) {
            mtrace('Found more than one user for email: ' . $d1student->email);

            // Loop through these users to provide more info for the logs.
            foreach($users['email'] as $idn) {

                // If we're running via scheduled task.
                if (!isset($courseid)) {
                    mtrace('  Email: ' . $d1student->email . ' = Username: ' . $idn->username . ' = ID: ' . $idn->id);
                }
            }

            // We cannot automatically update multiple users with a single email. Unset them.
            unset($users['email']);
        } else {
            $users['email'] = reset($users['email']);
        }

        $users['username'] = $DB->get_record($table, $uconditions, $fields='*', $strictness=IGNORE_MISSING);
        $users['idnumber'] = $DB->get_records($table, $iconditions, 'id', $fields='*');
        if (is_array($users['idnumber']) && count($users['idnumber']) > 1) {
            if (!isset($courseid)) {
                mtrace('Found more than one user for idnumber: ' . $d1student->idnumber);
            }
            foreach($users['idnumber'] as $idn) {
                if (!isset($courseid)) {
                    // mtrace('  IDNumber: ' . $d1student->idnumber . ' = Username: ' . $idn->username . ' = ID: ' . $idn->id);
                }
            }
            unset($users['idnumber']);
        } else {
            $users['idnumber'] = reset($users['idnumber']);
        }
        $users['email']    = $DB->get_records($table, $econditions, 'id', $fields='*');
        if (is_array($users['email']) && count($users['email']) > 1) {
            if (!isset($courseid)) {
                mtrace('Found more than one user for email: ' . $d1student->email);
            }

            foreach($users['email'] as $idn) {
                if (!isset($courseid)) {
                    // mtrace('  Email: ' . $d1student->email . ' = Username: ' . $idn->username . ' = ID: ' . $idn->id);
                }
            }
            unset($users['email']);
        } else {
            $users['email'] = reset($users['email']);
        }

        // Loop through this funky stuff and update user info as needed.
        foreach ($users as $key => $u0) {
            // If we have a matching user, upddate them or grab the user object.
            if (isset($u0->id)) {
                // Set this so we can update the user entry if needed.
                $user->id = $u0->id;
                // Now we can compare username, idnumber, email, first and last names.
                if (!isset($courseid)) {
                    mtrace("    User: $user->username with matching $key exsits from search.");
                }
                if (strtolower($user->username) == strtolower($u0->username)
                 && $user->idnumber == $u0->idnumber
                 && strtolower($user->email) == strtolower($u0->email)
                 && strtolower($user->firstname) == strtolower($u0->firstname)
                 && strtolower($user->lastname) == strtolower($u0->lastname)) {
                    mtrace("    User ID: $user->id matched all parts of the Moodle user object.");
                    // User object matches stored data 1:1, grab the object.
                    $uo = $DB->get_record($table, array("id"=>$user->id), $fields='*', $strictness=IGNORE_MISSING);
                    break;
                } else {
                    // User object matches stored data but some differences exist, try to update the object.
                    if ($DB->update_record($table, $user, $bulk=false)) {
                        mtrace("    Updated Moodle user with username: $user->username, idnumber: $user->idnumber, email: $user->email, and name: $user->firstname $user->lastname.");
                        // We successfully updated the user object and stored the data, fetch the data.
                        $uo = $DB->get_record($table, array("id"=>$user->id), $fields='*', $strictness=IGNORE_MISSING);
                        break;
                    } else {
                        mtrace("    Failed to update Moodle user: $user->id with username: $user->username, idnumber: $user->idnumber, email: $user->email, and name: $user->firstname $user->lastname due to a DB error.");
                        continue;
                    }
                }
                // Update the userid of the d1student table JIC.
                $d1student->userid = $uo->id;
                $d1student->id = $d1student->studentsid;

                $DB->update_record('enrol_d1_students', $d1student);
                // If we found a user and did all the above, exit the loop.
                break;
            } else {
                continue;
            }
        }
        // If we have a user object, return it. Otherwise create a new user.
        if (isset($uo->id)) {
            return $uo;
        } else {
            $user->username = strtolower($user->username);
            // We do not have a matching or existing user, please create one.
            $user->password = 'to be generated';
            $id = user_create_user($user, false, false);
            set_user_preference('auth_forcepasswordchange', 1, $id);
            set_user_preference('create_password', 1, $id);

            mtrace("Created userid: $id,  username: $user->username, idnumber: $user->idnumber, fn: $user->firstname, ln: $user->lastname.");

            // Grab the newly created user object and return it.
            $uo = $DB->get_record($table, array("id"=>$id), $fields='*', $strictness=IGNORE_MISSING);

            // Update the userid of the d1student table JIC.
            $d1student->userid = $uo->id;
            $d1student->id = $d1student->studentsid;

            $DB->update_record('enrol_d1_students', $d1student);
            return $uo;
        }
    }

}

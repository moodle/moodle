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
 *
 * @copyright 2023 onwards LSUOnline & Continuing Education
 * @copyright 2023 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @package    enrol_workdaystudent
 * @copyright  2023 onwards LSUOnline & Continuing Education
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class workdaystudent {

    public static function workdaystudent_enrollment($s, $course, $user, $enrollstatus) {
        global $CFG, $DB;

        // Instantiate the enroller.
        $enroller = new enrol_workdaystudent;

        // Grab the role id if one is present, otherwise use the Moodle default.
        $roleid = isset($s->studentrole) ? $s->studentrole : 5;

        // Set the time in seconds from epoch.
        $time = time();

        // Add or remove this student or teacher to the course...
        $stu = new stdClass();
        $stu->userid = $user->id;
        $stu->enrol = 'workdaystudent';
        $stu->course = $course->id;
        $stu->time = $time;
        $stu->timemodified = $time;

        // Set this up for getting the enroll instance.
        $etable      = 'enrol';
        $econditions = array('courseid' => $course->id, 'enrol' => $stu->enrol);

        // Get the enroll instance.
        $einstance   = $DB->get_record($etable, $econditions);

        // If we do not have an existing enrollment instance, add it.
        if (empty($einstance)) {
            self::dtrace("    Creating enroll instance for $stu->enrol in course $course->shortname.");
            $enrollid = $enroller->add_instance($course);
            $einstance = $DB->get_record('enrol', array('id' => $enrollid));
            self::dtrace("    Enroll instance for $einstance->enrol with ID: $einstance->id in course $course->shortname has been created.");
        } else {
            self::dtrace("    Existing enrollment instance for $einstance->enrol with ID: $einstance->id in course $course->shortname is already here.");
        }

        // Determine if we're removing or suspending oa user on unenroll.
        $unenroll = $s->unenroll;

        if ($enrollstatus == "unenroll") {
            // If we're removing them from the course.
            if ($unenroll == 1) {
                // Do the nasty.
                $enrolluser   = $enroller->unenrol_user(
                                    $einstance,
                                    $stu->userid);
                self::dtrace("      User $stu->userid unenrolled from course: $stu->course.");
            // Or we're suspending them.
            } else {
                // Do the nasty.
                $enrolluser   = $enroller->update_user_enrol(
                                    $einstance,
                                    $stu->userid, ENROL_USER_SUSPENDED);
                self::dtrace("    User ID: $stu->userid suspended from course: $stu->course.");
            }
        // If we're enrolling a student in the course.
        } else if ($enrollstatus == "enroll") {
            $enrollstart = 0;
            $enrollend = 0;
            // Do the nasty.
            $enrolluser = $enroller->enrol_user(
                              $einstance,
                              $stu->userid,
                              $roleid,
                              $enrollstart,
                              $enrollend,
                              $status = ENROL_USER_ACTIVE);
            self::dtrace("    User ID: $stu->userid enrolled into course: $stu->course.");
        }

        return true;
    }

    public static function create_moodle_user($user) {
        global $CFG, $DB;
        $table = 'user';
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->libdir . '/moodlelib.php');
        self::dtrace("      Creating username / email: $user->email, idnumber $user->idnumber, full name: $user->firstname $user->lastname.");
        $muserid = user_create_user($user, false, false);
        $muser = $DB->get_record($table, array("id"=>$muserid), $fields='*', $strictness=IGNORE_MISSING);
        mtrace("      Created userid: $muser->id username / email: $muser->email, idnumber $muser->idnumber, full name: $muser->firstname $muser->lastname.");
        return $muser;
    }

    /**
     * Contructs and sends error emails using Moodle functionality.
     *
     * @package   enrol_workdaystudent
     *
     * @param     @object $emaildata
     * @param     @object $s
     *
     * @return    @bool
     */
    public static function send_wdstudent_email($emaildata, $s) {
        global $CFG, $DB;

        // Get email subject from email log.
        $emailsubject = $emaildata->subject;

        // Get email content from email log.
        $emailcontent = $emaildata->body;

        // Grab the list of usernames from Moodle.
        $usernames = explode(",", $s->contacts);

        // Set up the users array.
        $users = array();

        // Loop through the usernames and add each user object to the user array.
        foreach ($usernames as $username) {

            // Make sure we have no spaces.
            $username = trim($username);

            // Add the user object to the array.
            $users[] = $DB->get_record('user', array('username' => $username));
        }

        // Send an email to each of the above users.
        foreach ($users as $user) {

            // Email the message.
            email_to_user($user,
                get_string("workdaystudent_emailname", "enrol_workdaystudent"),
                $emailsubject . " - " . $CFG->wwwroot,
                $emailcontent);
        }
    }
}

class enrol_workdaystudent extends enrol_plugin {

    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array $fields instance fields
     * @return int id of new instance, null if can not be created
     */
    public static function add_enroll_instance($course) {
        return $instance;
    }
}

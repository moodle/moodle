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
 * @package    enrol_d1
 * @copyright  2022 onwards Louisiana State University
 * @copyright  2022 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class enrol_d1_plugin extends enrol_plugin {

    /**
     * Convenience wrapper for redirecting to moodle URLs
     *
     * @param  string  $url
     * @param  array   $urlparams   array of parameters for the given URL
     * @param  int     $delay        delay, in seconds, before redirecting
     * @return (http redirect header)
     */
    public function redirect_to_url($url, $urlparams = [], $delay = 2) {
        $moodleurl = new \moodle_url($url, $urlparams);
        redirect($moodleurl, '', $delay);
    }

    /**
     * Fetches the moodle "scheduled task" object
     *
     * @return \core\task\scheduled_task
     */
    private function get_scheduled_task() {
        $task = \core\task\manager::get_scheduled_task('\enrol_d1\task\d1_full_enroll');

        return $task;
    }

    /**
     * Master method for kicking off DestinyOne enrollment
     *
     * @return boolean
     */
    public static function run_d1_quick_enroll($courseid=null) {
        require_once('classes/d1.php');

        // Get the token.
        $token = lsud1::get_token();

        // Short circuit things if we cannot get a token.
        if ($token == false) {
            mtrace("Token not found, please check credentials.");
            return;
        }

        //Get the approved categories.
        $categories = get_config('enrol_d1', 'categories');

        // Get all enrolled courses in the approved categories.
        $courses = lsud1::get_courses($categories, $courseid);

        $tokentime = microtime(true);

        foreach ($courses as $course) {
            // If our token is older than 600 seconds, get a new one and reset the timer.
            if (microtime(true) - $tokentime > 600) {
                mtrace("Expiring token: $token in lib's courses as course foreach.");
                $token = lsud1::get_token();
                $tokentime = microtime(true);
                mtrace("We fetched a new token: $token.");
            }

            // Log where we are.
            mtrace('Pre-enrollment for ' . $course->idnumber . ' has begun.');

           $prestage = lsud1::prestage_drops($course->idnumber, 'unenroll');
            mtrace("  Prestaged drops for $course->idnumber.");

            // Grab all the enrollments for the course.
            $enrollments = lsud1::set_student_enrollments($token, $course);

            // Log how many enrollments we got.
            mtrace('  Found ' . count($enrollments) . ' enrollments in course: ' . $course->idnumber . '.');

            // If we don't have any enrollments in the specified course, log it and move on.
            if (empty($enrollments[0])) {
                mtrace('  NO D1 Enrollments in ' . $course->idnumber . '.');
                continue;
            }

            // We have some enrollments here, so let's loop through them.
            foreach ($enrollments as $enrollment) {

                // If our token is older than 600 seconds, get a new one and reset the timer.
                if (microtime(true) - $tokentime > 600) {
                    mtrace("Expiring token: $token in lib's enrollments as enrollment foreach.");
                    $token = lsud1::get_token();
                    $tokentime = microtime(true);
                    mtrace("We fetched a new token: $token.");
                }

//                mtrace("    Populating student and enrollment DB for $enrollment->section - $enrollment->username.");
                $user   = lsud1::populate_stu_db($enrollment);
                $enroll = lsud1::insert_or_update_enrolldb($enrollment);
            }
            mtrace("Completed processing of D1 user and enrollment DB for $course->idnumber.");
        }
    }

    /**
     * Master method for kicking off DestinyOne enrollment
     *
     * @return boolean
     */
    public static function run_d1_full_enroll($courseid=null) {
        require_once('classes/d1.php');

        // Get the token.
        $token = lsud1::get_token();

        // Short circuit things if we cannot get a token.
        if ($token == false) {
            mtrace("Token not found, please check credentials.");
            return;
        }

        //Get the approved categories.
        $categories = get_config('enrol_d1', 'categories');

        // Get all enrolled courses in the approved categories.
        $courses = lsud1::get_courses($categories, $courseid);

        $tokentime = microtime(true);

        // Loop through the courses and update the db.
        foreach ($courses as $course) {

            // If our token is older than 600 seconds, get a new one and reset the timer.
            if (microtime(true) - $tokentime > 600) {
                mtrace("Expiring token: $token in lib's courses as course foreach.");
                $token = lsud1::get_token();
                $tokentime = microtime(true);
                mtrace("We fetched a new token: $token.");
            }

            // Log where we are.
            mtrace('Pre-enrollment for ' . $course->idnumber . ' has begun.');

           $prestage = lsud1::prestage_drops($course->idnumber, 'unenroll');
            mtrace("  Prestaged drops for $course->idnumber.");

            // Grab all the enrollments for the course.
            $enrollments = lsud1::set_student_enrollments($token, $course);

            // Log how many enrollments we got.
            mtrace('  Found ' . count($enrollments) . ' enrollments in course: ' . $course->idnumber . '.');

            // If we don't have any enrollments in the specified course, log it and move on.
            if (empty($enrollments[0])) {
                mtrace('  NO D1 Enrollments in ' . $course->idnumber . '.');
                continue;
            }

            // We have some enrollments here, so let's loop through them.
            foreach ($enrollments as $enrollment) {

                // If our token is older than 600 seconds, get a new one and reset the timer.
                if (microtime(true) - $tokentime > 600) {
                    mtrace("Expiring token: $token in lib's enrollments as enrollment foreach.");
                    $token = lsud1::get_token();
                    $tokentime = microtime(true);
                    mtrace("We fetched a new token: $token.");
                }

//                mtrace("    Populating student and enrollment DB for $enrollment->section - $enrollment->username.");
                $user   = lsud1::populate_stu_db($enrollment);
                $enroll = lsud1::insert_or_update_enrolldb($enrollment);
            }
            mtrace("Completed processing of D1 user and enrollment DB for $course->idnumber.");
        }

        mtrace("Updating interstitial enrollment database.");
        // Get courses with matching courseidnumbers without a corresponding id.
        $courseidnumbers = lsud1::get_courseidnumbers_noid();
        mtrace("  Fetched courses with missing ids.");
        // Loop through those and update the courseids as appropriate.
        foreach ($courseidnumbers as $courseidnumber) {
            $updated = lsud1::set_courseids($courseidnumber);
            mtrace("  CourseID for $courseidnumber has been set.\n");
        }

        // Create and update users as necessary. 
        if (!isset($courseid)) {
            $d1students = lsud1::get_d1students($all=true);
            mtrace("Beginning processing Moodle user accounts.");
        } else {
            $d1students = lsud1::get_d1students($all=false, $courseid);
            mtrace("Beginning processing user accounts for course: $courseid.");
        }
        foreach ($d1students as $d1student) {

            // If our token is older than 600 seconds, get a new one and reset the timer.
            if (microtime(true) - $tokentime > 600) {
                mtrace("Expiring token: $token in lib's d1students as d1student foreach.");
                $token = lsud1::get_token();
                $tokentime = microtime(true);
                mtrace("We fetched a new token: $token.");
            }

            if (!isset($courseid)) {
                mtrace("  Processing $d1student->username.");
            }
            $user = lsud1::create_update_user($d1student, $courseid);
        }
        if (!isset($courseid)) {
            mtrace("Completed processing Moodle user accounts.");
        }

        // Grab the courseids in the D1 enrollment table.
        $courseids = lsud1::get_courseids_arr($categories);
        mtrace("Beginning the processing of Moodle enrollments.");

        // Loop through them.
        foreach ($courseids as $coursed) {

            // If our token is older than 600 seconds, get a new one and reset the timer.
            if (microtime(true) - $tokentime > 600) {
                mtrace("Expiring token: $token in lib's courseids as courseid foreach.");
                $token = lsud1::get_token();
                $tokentime = microtime(true);
                mtrace("We fetched a new token: $token.");
            }

            if (!isset($courseid) || $coursed->courseid == $courseid) {
                mtrace("  Processing Moodle enrollments for courseid: $coursed->courseid.");
                // Get a list of courses to be enrolled.
                $d1enrolls = lsud1::get_d1enrolls($coursed->courseid);
                // Loop through these enrollments.
                foreach ($d1enrolls as $d1enroll) {

                    // If our token is older than 600 seconds, get a new one and reset the timer.
                    if (microtime(true) - $tokentime > 600) {
                        mtrace("Expiring token: $token in lib's d1enrolls as d1enroll foreach.");
                        $token = lsud1::get_token();
                        $tokentime = microtime(true);
                        mtrace("We fetched a new token: $token.");
                    }

                    if (!empty($d1enroll->userid)) {
                        // Enroll the student.
                        $enroll = lsud1::d1_enrollment(
                              $d1enroll->courseid,
                              $d1enroll->userid,
                              $d1enroll->status,
                              $d1enroll->enrollstart,
                              $d1enroll->enrollend
                        );
                    } else {
                        mtrace('    No userid found, enrollment skipped. ');
                    }
                }
            mtrace("  Completed the processing of Moodle enrollments for courseid: $coursed->courseid.");
            }
        }

    mtrace("  Updating mismatched and inserting new Moodle user idnumber profile fields.");

    // Get some config vars.
    $fieldid     = get_config('enrol_d1', 'd1_fieldid');
    $startstring = get_config('enrol_d1', 'd1_id_pre');
    mtrace("  * User profile field id: $fieldid.");
    mtrace("  * User profile field start string: $startstring.");

    // Do any outstanding LSUID updates for the field.
    $updates = lsud1::update_moodle_idnumbers($fieldid);
    mtrace("    Updated mismatched user profile fields to match D1.");

    // Get the missing (in Moodle) LSUIDs.
    $idnumbers = lsud1::get_missing_idnumbers ($startstring, $fieldid);
    $idcount   = count($idnumbers);
    mtrace("    Grabbed $idcount missing user profile field idnumbers for field id: $fieldid.");

    // Loop through these and insert the new record.
    foreach ($idnumbers as $lsuid) {

        // If our token is older than 600 seconds, get a new one and reset the timer.
        if (microtime(true) - $tokentime > 600) {
            mtrace("Expiring token: $token in lib's idnumbers as lsuid foreach.");
            $token = lsud1::get_token();
            $tokentime = microtime(true);
            mtrace("We fetched a new token: $token.");
        }

        // Actually insert the record.
        $inserter = lsud1::insert_moodle_idnumber($lsuid->userid, $fieldid, $lsuid->idnumber);
        mtrace("    * Inserted $lsuid->idnumber for user: $lsuid->userid.");
    }

    mtrace("  Updated mismatched and inserted any missing Moodle user idnumber profile fields.");

    $lower = lsud1::d1_lowercase();

    if ($lower) {
        mtrace("  Converted remaining usernames and emails to lower case.");
    }

    mtrace("Completed the processing of Moodle enrollments.");
    }

    /**
     * Typical error log
     *
     * @var array
     */
    private $errors = array();

    /**
     * Typical email log
     *
     * @var array
     */
    private $emaillog = array();

    /**
     * Emails a d1 "startup" report to moodle administrators
     *
     * @param  float  $start_time  the current time in seconds since the Unix epoch
     * @return void
     */
    private function email_startup_report($starttime) {
        // Get all moodle admin users.
        $users = get_admins();
        // Email these users the job has begun.
        $this->email_ues_startup_report_to_users($users, $starttime);
    }

    /**
     * Emails a d1 startup report (notification of start time) to given users
     *
     * @param  array  $users  moodle users
     * @param  float  $start_time  the current time in seconds since the Unix epoch
     * @return void
     */
    private function email_d1_startup_report_to_users($users, $starttime) {
        global $CFG;

        $starttimedisplay = $this->format_time_display($starttime);

        // Get email content from email log.
        $emailcontent = 'This email is to let you know that UES Enrollment has begun at:' . $starttimedisplay;

        // Send to each admin.
        foreach ($users as $user) {
            email_to_user($user, ues::_s('pluginname'), sprintf('UES Enrollment Begun [%s]', $CFG->wwwroot), $emailcontent);
        }
    }

    /**
     * Formats a Unix time for display
     *
     * @param  @int $time
     * @return @string $formatted
     */
    private function format_time_display($time) {
        $dformat = "l jS F, Y - H:i:s";
        $msecs = $time - floor($time);
        $msecs = substr($msecs, 1);

        $formatted = sprintf('%s%s', date($dformat), $msecs);

        return $formatted;
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param  @object $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/d1:delete', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param  @object $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/d1:showhide', $context);
    }

    /**
     * Returns true if the course is a d1 course.
     *
     * @param  @object $course
     * @return @bool
     */
     public static function d1course($course) {
        // Get the D1 course categories in settings.
        $cats = get_config('enrol_d1', 'categories');
        $cats = explode(',', $cats);

        if (in_array($course->category, $cats)) {
            return(true);
        } else {
            return(false);
        }
    }
}

/**
 * This function extends the course navigation with the bulkenrol item
 *
 * @param  @object $navigation
 * @param  @object $course
 * @param  @object $context
 * @return @object $navigation node
 */
function enrol_d1_extend_navigation_course($navigation, $course, $context) {
    // Make sure we can reprocess enrollments.
    if (has_capability('enrol/d1:reprocess', $context)) {

        // Is this a D1 course?
        $d1course = enrol_d1_plugin::d1course($course);

        // If we are not a D1 course, do not render.
        if ($d1course) {

            // Set the url for the reprocesser.
            $url = new moodle_url('/enrol/d1/reprocess.php', array('courseid' => $course->id));

            // Grab the user node.
            $usersnode = $navigation->get('users');

            // Build the folder.
            $d1node = $usersnode->add(get_string('pluginname', 'enrol_d1'),
                null, navigation_node::TYPE_CONTAINER, 'users', 'pgd1');

            // Create a link in the user node.
            $d1node->add(get_string('reprocess', 'enrol_d1'), $url, 
                navigation_node::TYPE_SETTING, null, 'd1link1', new pix_icon('t/enrolusers', ''));

            // Return the link.
            return $d1node;
        }
    }
}

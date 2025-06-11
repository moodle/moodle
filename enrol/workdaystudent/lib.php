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
 * @package    enrol_workdaystudent
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class enrol_workdaystudent_plugin extends enrol_plugin {

    /**
     * Fetches the moodle "scheduled task" object
     *
     * @return \core\task\scheduled_task
     */
    private function get_scheduled_task() {
        $task = \core\task\manager::get_scheduled_task('\enrol_workdaystudent\task\workdaystudent_full_enroll');

        return $task;
    }

    /**
     * Fetches the moodle "scheduled task" object
     *
     * @return \core\task\scheduled_task
     */
    private function get_quick_task() {
        $task = \core\task\manager::get_quick_task('\enrol_workdaystudent\task\workdaystudent_quick_enroll');

        return $task;
    }

    /**
     * Master method for kicking off quick Workday Student enrollment.
     *
     * @return boolean
     */
    public static function run_workdaystudent_quick_enroll($courseid=null) {
        global $CFG;

        // Fetch the main class.
        require_once('classes/workdaystudent.php');

        // Set the start time.
        $starttime = microtime(true);

        mtrace("Starting Moodle Student enrollments.");

        // Process the academic units.
        $cronunits = wdscronhelper::cronunits();

        // Process academic periods.
        $cronperiods = wdscronhelper::cronperiods();

        // Proces programs of study.
        $cronprograms = wdscronhelper::cronprograms();

        // Process courses.
        $croncourses = wdscronhelper::croncourses();

        // Process sections.
        $cronsections = wdscronhelper::cronsections();

        // Process grading schemes.
        $crongradingschemes = wdscronhelper::crongradeschemes();

        // Create and update moodle users.
        $cronstucreate = wdscronhelper::cronmusers();

        // Create course shells.
        $cronshells = wdscronhelper::cronmcourses();

        // Enroll the faculty.
        $cronfenroll = wdscronhelper::cronmfenrolls();

        // Process wds enrollments.
        $cronstuenroll = wdscronhelper::cronstuenroll();

        // Fetch and update any missing students not in an active period.
        $nonactive = workdaystudent::wds_get_insert_missing_students();

        // Enroll the students into courses and groups.
        $cronenrollments = wdscronhelper::cronmenrolls();

        $endtime = microtime(true);
        $elapsedtime = round($endtime - $starttime, 2);

        mtrace("Finished processing Moodle Student enrollments in $elapsedtime seconds.");
    }

    /**
     * Master method for kicking off Reprocessing
     *
     * @return boolean
     */
    public static function run_workdaystudent_reprocess($courseid) {
        global $CFG, $DB;

        // Set the sections table.
        $stable = 'enrol_wds_sections';

        // Set the parms for fetching csdids for this course.
        $sparms = ['moodle_status' => $courseid];

        // Get the section records.
        $sections = $DB->get_records($stable, $sparms);

        // Fetch the main class.
        require_once('classes/workdaystudent.php');

        // Set the start time.
        $starttime = microtime(true);

        // First handle instructor changes.
        workdaystudent::reprocess_instructor_enrollments($courseid);

        foreach ($sections as $section) {

            mtrace("Starting Moodle Student enrollments for $section->section_listing_id..");

            // Process wds enrollments.
            $cronstuenroll = wdscronhelper::cronstuenroll(
                $section->course_section_definition_id
            );

            // Fetch and update any missing students not in an active period.
            $nonactive = workdaystudent::wds_get_insert_missing_students(
                $section->course_section_definition_id
            );

            // Enroll the students into courses and groups.
            $cronenrollments = wdscronhelper::cronmenrolls(
                $section->course_section_definition_id
            );

            mtrace("Finished Moodle Student enrollments for $section->section_listing_id..");
        }

        $endtime = microtime(true);
        $elapsedtime = round($endtime - $starttime, 2);

        mtrace("Finished processing Moodle Student enrollments in $elapsedtime seconds.");
    }


    /**
     * Master method for kicking off Workday Student enrollment
     *
     * @return boolean
     */
    public static function run_workdaystudent_full_enroll($courseid=null) {
        global $CFG;

        // Fetch the main class.
        require_once('classes/workdaystudent.php');

        // Set the start time.
        $starttime = microtime(true);

        mtrace("Starting Moodle Student enrollments.");

        // Process the academic units.
        $cronunits = wdscronhelper::cronunits();

        // Process academic periods.
        $cronperiods = wdscronhelper::cronperiods();

        // Proces programs of study.
        $cronprograms = wdscronhelper::cronprograms();

        // Process courses.
        $croncourses = wdscronhelper::croncourses();

        // Process courses.
        $cronsections = wdscronhelper::cronsections();

        // Process grading schemes.
        $crongradingschemes = wdscronhelper::crongradeschemes();

        // Process students.
        $cronstudents = wdscronhelper::cronstudents();

        // Create and update moodle students.
        $cronstucreate = wdscronhelper::cronmusers();

        // Create course shells.
        $cronshells = wdscronhelper::cronmcourses();

        // Enroll the faculty.
        $cronfenroll = wdscronhelper::cronmfenrolls();

        // Process wds enrollments.
        $cronstuenroll = wdscronhelper::cronstuenroll();

        // Fetch and update any missing students not in an active period.
        $nonactive = workdaystudent::wds_get_insert_missing_students();

        // Enroll the students into courses and groups.
        $cronenrollments = wdscronhelper::cronmenrolls();

        $endtime = microtime(true);
        $elapsedtime = round($endtime - $starttime, 2);

        mtrace("Finished processing Moodle Student enrollments in $elapsedtime seconds.");
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
     * Emails a workdaystudent report to Student administrators.
     *
     * @param  @int $starttime
     * @param  @object $s
     * @return void
     */
    private function get_workdaystudent_contacts($s, $starttime, $log) {
        global $DB;
        // Get all Student admins defined in settings.
        $usernames = explode($s->contacts);

        // Create a blank array for storing the users.
        $users = array();

        // Loop through the usernames and grab the users.
        foreach ($usernames as $username) {
            $users[] = $DB->get_records('user', array('username' => $username));
        }

        // Email these users the workdaystudent log.
        $this->email_workdaystudent_report_to_users($users, $starttime, $log);
    }

    /**
     * Emails a workdaystudent report to Student contacts.
     * (notification of start time, elapsed time, enrollments, unenrollments)
     *
     * @param  @array  $users
     * @param  @int $starttime
     * @param  @array $log
     * @return void
     */
    private function email_workdaystudent_report_to_users($users, $starttime, $log) {
        global $CFG;

        $starttimedisplay = $this->format_time_display($starttime);

        // Get email content from email log.
        $emailcontent = 'This email is to let you know that Workday Student Enrollment has begun at:' . $starttimedisplay;

        // Send to each admin.
        foreach ($users as $user) {
            email_to_user($user, get_string('pluginname', 'enrol_workdaystudent'), sprintf('Workday Student Enrollment Begun [%s]', $CFG->wwwroot), $emailcontent);
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
        return has_capability('enrol/workdaystudent:delete', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param  @object $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/workdaystudent:showhide', $context);
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
function enrol_workdaystudent_extend_navigation_course($navigation, $course, $context) {
    // Make sure we can reprocess enrollments.
    if (is_siteadmin()) {
    // if (has_capability('enrol/workdaystudent:reprocess', $context)) {

        // Set the url for the reprocesser.
        $url = new moodle_url('/enrol/workdaystudent/reprocess.php', array('courseid' => $course->id));

        // Build the navigation node.
        $workdaystudentenrolnode = navigation_node::create(get_string('reprocess', 'enrol_workdaystudent'), $url,
                navigation_node::TYPE_SETTING, null, 'enrol_workdaystudent', new pix_icon('t/enrolusers', ''));

        // Set the users' navigation node.
        $usersnode = $navigation->get('users');

        // If we have an reprocess node, add it to the users' node.
        if (isset($workdaystudentenrolnode) && !empty($usersnode)) {

            // Actually add the node.
            $usersnode->add_node($workdaystudentenrolnode);
        }
    }
}

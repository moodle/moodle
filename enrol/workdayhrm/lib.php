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
 * @package    enrol_workdayhrm
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class enrol_workdayhrm_plugin extends enrol_plugin {

    /**
     * Fetches the moodle "scheduled task" object
     *
     * @return \core\task\scheduled_task
     */
    private function get_scheduled_task() {
        $task = \core\task\manager::get_scheduled_task('\enrol_workdayhrm\task\workdayhrm_full_enroll');

        return $task;
    }

    /**
     * Master method for kicking off Workday HRM enrollment
     *
     * @return boolean
     */
    public static function run_workdayhrm_full_enroll($courseid=null) {
        global $CFG;
        require_once('classes/workdayhrm.php');

        // Get settings.
        $s = workdayhrm::get_wdhrm_settings();

        // Set the start time.
        $starttime = microtime(true);

        mtrace("Starting Moodle HRM enrollments.");
        workdayhrm::dtrace("  Getting the list of employees from the WorkDay webservice endpoint.");

        // Get the list of employees from the webservice.
        $employees = workdayhrm::get_wdhrm_employees($s);

        // Count them.
        $employeecount = count($employees);

        mtrace("  Returned $employeecount employees from the WorkDay webservice endpoint.");
        workdayhrm::dtrace("  Getting the list of enrollable courses from the Moodle plugin config table.");

        // Get the list of HRM courses.
        $courses = workdayhrm::get_wdhrm_courses($s);

        // Get a course count.
        $coursecount = count($courses);

        mtrace("  Returned $coursecount enrollable courses from the Moodle plugin config.");
        workdayhrm::dtrace("  Preparing to clean bad emails.");

        // Clean the employee emails.
        $cleaned = workdayhrm::clean_wdhrm_employees($s, $employees);

        // Get a fresh count.
        $cleanedcount = count($cleaned);

        workdayhrm::dtrace("  Cleaned bad emails, reduced employeecount to $cleanedcount.");
        mtrace("  Updating / inserting records in the enrol_workdayhrml table.");

        $unsetcurrent = workdayhrm::update_wdhrm_statuses_expired();

        // Start a counter.
        $employeecounter = 0;

        // Loop through the cleaned data and update/insert the employees into the interstitial database.
        foreach ($cleaned as $employee) {
            // Increment our counter.
            $employeecounter++;
            workdayhrm::dtrace("    $employeecounter of $cleanedcount: Checking to see if $employee->Legal_First_Name $employee->Legal_Last_Name exists in the enrol_workdayhrm table.");
            $employeerecord = workdayhrm::wdhrm_employee_helper($employee, $s);
            workdayhrm::dtrace("    Finished checking to see if $employee->Legal_First_Name $employee->Legal_Last_Name exists.");
        }
        workdayhrm::dtrace("  Finished Inserting / updating data in the enrol_workdayhrm table.");

        $dupes = workdayhrm::wdhrm_find_duplicates();

        foreach ($dupes as $dupe) {
            $ud = workdayhrm::wdhrm_update_duplicates($dupe);
            $dupe->body = json_encode($dupe);
            $dupe->subject = 'Duplicate Account Found';
            if ($ud) {
                $dupe->updatestatus = 'success';
                workdayhrm::dtrace("  id: $dupe->id - email: $dupe->work_email - name: $dupe->legal_first_name $dupe->legal_last_name - Duplicate email found and updated!");
            } else {
                $dupe->updatestatus = 'failed';
                workdayhrm::dtrace("  id: $dupe->id - email: $dupe->work_email - name: $dupe->legal_first_name $dupe->legal_last_name - Duplicate update failed!");
            }
            $email = workdayhrm::send_wdhrm_email($dupe, $s);
        }

        $wdemployees = workdayhrm::get_clean_employees();
        foreach ($wdemployees as $wdemployee) {
            workdayhrm::dtrace("    Checking to see if $wdemployee->legal_first_name $wdemployee->legal_last_name has a Moodle account.");
            $muser = workdayhrm::create_update_user($wdemployee, $s);
            workdayhrm::dtrace("    Finished checking to see if $wdemployee->legal_first_name $wdemployee->legal_last_name has a Moodle account.");
            workdayhrm::dtrace("      Beginning any enrollments for $wdemployee->legal_first_name $wdemployee->legal_last_name.");
            $enrollstatus = $wdemployee->iscurrent == 1 ? 'enroll' : 'unenroll';
            foreach ($courses as $course) {
                workdayhrm::dtrace("        $enrollstatus" . "ing $muser->username into $course->shortname.");
                if (!isset($muser->notreallyhere)) {
                    $enroll = workdayhrm::workdayhrm_enrollment($s, $course, $muser, $enrollstatus);
                } else {
                    mtrace("Skipping enrollment for duplicate user $muser->firstname $muser->lastname.");
                }
                workdayhrm::dtrace("        $enrollstatus" . "ed $muser->username into $course->shortname.");
            }
            mtrace("      Finished HRM enrollments for $wdemployee->legal_first_name $wdemployee->legal_last_name.");
        }

        workdayhrm::dtrace("Finished processing Moodle HRM enrollments.");
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
     * Emails a workdayhrm report to HRM administrators.
     *
     * @param  @int $starttime
     * @param  @object $s
     * @return void
     */
    private function get_workdayhrm_contacts($s, $starttime, $log) {
        global $DB;
        // Get all HRM admins defined in settings.
        $usernames = explode($s->contacts);

        // Create a blank array for storing the users.
        $users = array();

        // Loop through the usernames and grab the users.
        foreach ($usernames as $username) {
            $users[] = $DB->get_records('user', array('username' => $username));
        }

        // Email these users the workdayhrm log.
        $this->email_workdayhrm_report_to_users($users, $starttime, $log);
    }

    /**
     * Emails a workdayhrm report to HRM contacts.
     * (notification of start time, elapsed time, enrollments, unenrollments)
     *
     * @param  @array  $users
     * @param  @int $starttime
     * @param  @array $log
     * @return void
     */
    private function email_workdayhrm_report_to_users($users, $starttime, $log) {
        global $CFG;

        $starttimedisplay = $this->format_time_display($starttime);

        // Get email content from email log.
        $emailcontent = 'This email is to let you know that Workday HRM Enrollment has begun at:' . $starttimedisplay;

        // Send to each admin.
        foreach ($users as $user) {
            email_to_user($user, ues::_s('pluginname'), sprintf('Workday HRM Enrollment Begun [%s]', $CFG->wwwroot), $emailcontent);
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
        return has_capability('enrol/workdayhrm:delete', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param  @object $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/workdayhrm:showhide', $context);
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
function enrol_workdayhrm_extend_navigation_course($navigation, $course, $context) {
    // Make sure we can reprocess enrollments.
    if (has_capability('enrol/workdayhrm:reprocess', $context)) {

        // Set the url for the reprocesser.
        $url = new moodle_url('/enrol/workdayhrm/reprocess.php', array('courseid' => $course->id));

        // Build the navigation node.
        $workdayhrmenrolnode = navigation_node::create(get_string('reprocess', 'enrol_workdayhrm'), $url,
                navigation_node::TYPE_SETTING, null, 'enrol_workdayhrm', new pix_icon('t/enrolusers', ''));

        // Set the users' navigation node.
        $usersnode = $navigation->get('users');

        // If we have an reprocess node, add it to the users' node.
        if (isset($workdayhrmenrolnode) && !empty($usersnode)) {

            // Actually add the node.
            $usersnode->add_node($workdayhrmenrolnode);
        }
    }
}

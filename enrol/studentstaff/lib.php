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
 * LSU studentstaff enrolment plugin.
 *
 * @package    enrol_studentstaff
 * @copyright  2023 Robert Russo
 * @copyright  2023 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class enrol_studentstaff_plugin extends enrol_plugin {

    /**
     * Override the allow unenroll function.
     *
     * @param stdClass $instance
     * @return bool true
     */
    public function allow_unenrol(stdClass $instance) {
        // Users with unenrol cap may unenrol other users manually manually.
        return true;
    }

    /**
     * Fetches the moodle "scheduled task" object
     *
     * @return \core\task\scheduled_task
     */
    private function get_scheduled_task() {
        // Get the task.
        $task = \core\task\manager::get_scheduled_task('\enrol_studentstaff\task\studentstaff_enroll');

        // Return the task.
        return $task;
    }

    /**
     * Standard function for scheduled task.
     */
    public static function run_studentstaff_enroll() {
        require_once('classes/studentstaff.php');

        // Return the comma separated course role ids.
        $courseroles = get_config('enrol_studentstaff', 'courserolescheck');

        // Return an array of enrollment methods.
        $enrollmethods = explode(",", get_config('enrol_studentstaff', 'enrollmethods'));

        // Loop through the enrollment methods and quote them.
        foreach ($enrollmethods as $enrollmethod) {
            $enroll[] = '"' . $enrollmethod . '"';
        }

        // Implode the array into a comma seperated list.
        $enrolls = implode(",", $enroll);

        // Use the local function to get users from site roles.
        $siteusers = studentstaff::get_site_users_studentstaff();

        // Set up an empty courses array.
        $courses = array();

        // Get the ss role.
        $ssrole = studentstaff::get_studentstaff_role();
        $count = count($siteusers);

        mtrace("Begin assigning $ssrole->shortname role for $count users.");

        // Loop through the site users.
        foreach ($siteusers as $siteuser) {
            mtrace("  Begin assigning $ssrole->shortname role for $siteuser->firstname $siteuser->lastname.");

            // Get a list of the site users courses and data useful for enrolling them.
            $courses = studentstaff::get_user_studentstaff_courses($siteuser->id, $courseroles, $enrolls);

            // Set this up for logging below.
            $ccount = count($courses);

            // Log this.
            if ($ccount == 0) {
                mtrace("  &mdash; No missing $ssrole->shortname roles for $siteuser->firstname $siteuser->lastname.");
            } else {
                mtrace("  &mdash; $ccount missing $ssrole->shortname roles for $siteuser->firstname $siteuser->lastname.");
            }

            // Loop through their courses.
            foreach ($courses as $courseobj) {

                // Enroll them.
                $enrolled = studentstaff::studentstaff_enrollment($courseobj, $ssrole);
            }
            mtrace("  Finished assigning $ssrole->shortname role for $siteuser->firstname $siteuser->lastname.");
        }
        mtrace("Finished assigning $ssrole->shortname role for $count users.");
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param object $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/studentstaff:config', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/studentstaff:config', $context);
    }
}

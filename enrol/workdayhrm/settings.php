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
 * @copyright  2023 Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $studentroles = array();

    if (isset($CFG->gradebookroles)) {
        // Get the "student" roles.
        $roles = explode(',', $CFG->gradebookroles);
    } else {
        $roles = array();
    }

    // Loop through those roles and do stuff.
    foreach ($roles as $role) {
        // Grab the role names from the DB.
        $rname = $DB->get_record('role', array("id" => $role), "shortname");
        // Set the studentroles array for the dropdown.
        $studentroles[$role] = $rname->shortname;
    }

    // Grab the course categories.
    $ccategories = $DB->get_records('course_categories', null, 'name', 'id,name');

    // Loop through those roles and do stuff.
    foreach ($ccategories as $category) {
        // Set the studentroles array for the dropdown.
        $categories[$category->id] = $category->name;
    }

    // Add a heading.
    $settings->add(
        new admin_setting_heading(
            'enrol_workdayhrm_settings',
            '',
            get_string('pluginname_desc', 'enrol_workdayhrm')
        )
    );

    // Workday HRM Webservice Token.
    $settings->add(
        new admin_setting_configpasswordunmask(
            'enrol_workdayhrm/token',
            get_string('workdayhrm_token', 'enrol_workdayhrm'),
            get_string('workdayhrm_token_help', 'enrol_workdayhrm'),
            '', PARAM_RAW
        )
    );

    // Workday HRM Websevice URL.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdayhrm/wsurl',
            get_string('workdayhrm_wsurl', 'enrol_workdayhrm'),
            get_string('workdayhrm_wsurl_help', 'enrol_workdayhrm'),
            'https://someurl.net', PARAM_TEXT
        )
    );

    // Student role.
    $settings->add(
        new admin_setting_configselect(
            'enrol_workdayhrm/studentrole',
            get_string('workdayhrm_studentrole', 'enrol_workdayhrm'),
            get_string('workdayhrm_studentrole_help', 'enrol_workdayhrm'),
            'Student',  // Default.
            $studentroles
        )
    );

    // Suspend or Unenroll.
    $settings->add(
        new admin_setting_configselect(
            'enrol_workdayhrm/unenroll',
            get_string('workdayhrm_suspend_unenroll', 'enrol_workdayhrm'),
            get_string('workdayhrm_suspend_unenroll_help', 'enrol_workdayhrm'),
            0,  // Default.
            array(0 => 'suspend', 1 => 'unenroll')
        )
    );

    // Workday HRM Administrative contacts.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdayhrm/contacts',
            get_string('workdayhrm_contacts', 'enrol_workdayhrm'),
            get_string('workdayhrm_contacts_help', 'enrol_workdayhrm'),
            'admin,hrm', PARAM_TEXT
        )
    );

    // HRM course ids.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdayhrm/courseids',
            get_string('workdayhrm_courseids', 'enrol_workdayhrm'),
            get_string('workdayhrm_courseids_help', 'enrol_workdayhrm'),
            '', PARAM_TEXT
        )
    );

    // Set the home domain.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdayhrm/homedomain',
            get_string('homedomain', 'enrol_workdayhrm'),
            get_string('homedomain_desc', 'enrol_workdayhrm'),
            '@lsu.edu'
        )
    );

    // Set the remote domain.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdayhrm/extdomain',
            get_string('extdomain', 'enrol_workdayhrm'),
            get_string('extdomain_desc', 'enrol_workdayhrm'),
            'admin'
        )
    );
}

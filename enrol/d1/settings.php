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
 * @copyright  2022 Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $studentroles = array();

    // Get the "student" roles.
    if (isset($CFG->gradebookroles)) {
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

    // Build the duedates array.
    $duedates = array();
    $duedates[0] = get_string('dd_calculated', 'enrol_d1');
    $duedates[1] = get_string('dd_specified', 'enrol_d1');
    $duedates[2] = get_string('dd_none', 'enrol_d1');

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
            'enrol_d1_settings',
            '',
            get_string('pluginname_desc', 'enrol_d1')
        )
    );

    // D1 Webservice Username.
    $settings->add(
        new admin_setting_configtext(
            'enrol_d1/username',
            get_string('d1_username', 'enrol_d1'),
            get_string('d1_username_help', 'enrol_d1'),
            '', PARAM_TEXT
        )
    );

    // D1 Webservice password.
    $settings->add(
        new admin_setting_configpasswordunmask(
            'enrol_d1/password',
            get_string('d1_password', 'enrol_d1'),
            get_string('d1_password_help', 'enrol_d1'),
            '', PARAM_RAW
        )
    );

    // D1 Websevice URL.
    $settings->add(
        new admin_setting_configtext(
            'd1_wsurl',
            get_string('d1_wsurl', 'enrol_d1'),
            get_string('d1_wsurl_help', 'enrol_d1'),
            'https://yourschooltestws.destinyone.moderncampus.net', PARAM_TEXT
        )
    );

    // Do we want extra debugging to files?
    $settings->add(
        new admin_setting_configcheckbox(
            'emrol_d1/extradebug',
            get_string('d1_extradebug', 'enrol_d1'),
            get_string('d1_extradebug_help', 'enrol_d1'),
            0  // Default.
        )
    );

    // Debug files location.
    $settings->add(
        new admin_setting_configtext(
            'enrol_d1/debugfiles',
            get_string('d1_debugfiles', 'enrol_d1'),
            get_string('d1_debugfiles_help', 'enrol_d1'),
            $CFG->dataroot, PARAM_TEXT
        )
    );

    // Student role.
    $settings->add(
        new admin_setting_configselect(
            'enrol_d1/studentrole',
            get_string('d1_studentrole', 'enrol_d1'),
            get_string('d1_studentrole_help', 'enrol_d1'),
            5,  // Default.
            $studentroles
        )
    );

    // Suspend or Unenroll.
    $settings->add(
        new admin_setting_configselect(
            'enrol_d1/unenroll',
            get_string('d1_suspend_unenroll', 'enrol_d1'),
            get_string('d1_suspend_unenroll_help', 'enrol_d1'),
            0,  // Default.
            array(0 => 'suspend', 1 => 'unenroll')
        )
    );

    // Course categories to process.
    $settings->add(
        new admin_setting_configmultiselect(
            'enrol_d1/categories',
            get_string('d1_categories', 'enrol_d1'),
            get_string('d1_categories_help', 'enrol_d1'),
            null, // Default.
            $categories
        )
    );

    // Student role.
    $settings->add(
        new admin_setting_configselect(
            'enrol_d1/duedate',
            get_string('d1_duedate', 'enrol_d1'),
            get_string('d1_duedate_help', 'enrol_d1'),
            1,  // Default.
            $duedates
        )
    );

    // Profile field id.
    $settings->add(
        new admin_setting_configtext(
            'enrol_d1/d1_fieldid',
            get_string('d1_fieldid', 'enrol_d1'),
            get_string('d1_fieldid_help', 'enrol_d1'),
            '3', PARAM_INT
        )
    );

    // Idnumber prefix.
    $settings->add(
        new admin_setting_configtext(
            'enrol_d1/d1_id_pre',
            get_string('d1_id_pre', 'enrol_d1'),
            get_string('d1_id_pre_help', 'enrol_d1'),
            '89', PARAM_TEXT
        )
    );

}

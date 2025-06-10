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
 * LSU studentstaff enrolment plugin settings.
 *
 * @package    enrol_studentstaff
 * @copyright  2023 Robert Russo
 * @copyright  2023 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $studentroles = array();
    $teacherroles = array();

    // Get all the roles.
    $aroles = $DB->get_records('role', null, $sort='shortname', $fields='*', $limitfrom=0, $limitnum=0);

    // Loop through them and build the allroles array.
    foreach ($aroles as $arole) {
        $allroles[$arole->id] = $arole->shortname;
    }

    // Get the "system" roles.
    $sql = 'SELECT r.id, r.shortname
            FROM {role_assignments} ra
                INNER JOIN {role} r ON ra.roleid = r.id
            WHERE ra.contextid = 1
            GROUP BY r.id
            ORDER BY r.shortname ASC';

    // Get the system roles.
    $sroles = $DB->get_records_sql($sql);

    // Loop through these roles and build their array.
    $systemroles = array();
    foreach ($sroles as $srole) {
        $systemroles[$srole->id] = $srole->shortname;
    }

    // SQL for getting enrollment methods.
    $sql = 'SELECT e.enrol
            FROM {enrol} e
            WHERE e.status = 0
            GROUP BY e.enrol
            ORDER BY e.enrol ASC';

    // Grab the enrollment methods.
    $enrolls = $DB->get_records_sql($sql);

    // Loop through the enrollment methods and build an array.
    $enrollmethods = array();
    foreach ($enrolls as $enroll) {
        $enrollmethods[$enroll->enrol] = $enroll->enrol;
    }

    if (isset($CFG->gradebookroles)) {
        // Get the "student" roles.
        $gbroles = explode(',', $CFG->gradebookroles);
    } else {
        $gbroles = array();
    }

    // Loop through those roles and do stuff.
    foreach ($gbroles as $gbrole) {

        // Grab the role names from the DB.
        $gbrname = $DB->get_record('role', array("id" => $gbrole), "*");

        // Set the studentroles array for the dropdown.
        $studentroles[$gbrname->id] = $gbrname->shortname;
    }

    if (isset($CFG->profileroles)) {
        // Get the "teacher" roles.
        $proles = explode(',', $CFG->profileroles);
    } else {
        $proles = array();
    }

    // Loop through those roles and do stuff.
    foreach ($proles as $prole) {

        // Grab the role names from the DB.
        $prname = $DB->get_record('role', array("id" => $prole), "*");

        // Set the studentroles array for the dropdown.
        $teacherroles[$prname->id] = $prname->shortname;
    }

    // Combine the arrays, preserving the role ids.
    $courseroles = array_replace($teacherroles, $studentroles);

    // General settings.
    $settings->add(
        new admin_setting_heading(
            'enrol_studentstaff_settings',
            get_string('ss_settings', 'enrol_studentstaff'),
            get_string('ss_settings_help','enrol_studentstaff')
        )
    );

    // Choose enrollment methods.
    if (isset($enrollmethods) && !empty($enrollmethods)) {
        $settings->add(
            new admin_setting_configmultiselect(
                'enrol_studentstaff/enrollmethods',
                get_string('ss_enrollmethods', 'enrol_studentstaff'),
                get_string('ss_enrollmethods_help', 'enrol_studentstaff'),
                null,  // Default.
                $enrollmethods
            )
        );
    }

    if (isset($systemroles) && !empty($systemroles)) {
        // Source system role.
        $settings->add(
            new admin_setting_configmultiselect(
                'enrol_studentstaff/siterolescheck',
                get_string('ss_siterolescheck', 'enrol_studentstaff'),
                get_string('ss_siterolescheck_help', 'enrol_studentstaff'),
                array(1),  // Default.
                $systemroles
            )
        );
    }

    // Source course role.
    $settings->add(
       new admin_setting_configmultiselect(
            'enrol_studentstaff/courserolescheck',
            get_string('ss_courserolescheck', 'enrol_studentstaff'),
            get_string('ss_courserolescheck_help', 'enrol_studentstaff'),
            array(5),  // Default.
            $courseroles
        )
    );

    // Role to assign.
    $settings->add(
        new admin_setting_configselect(
            'enrol_studentstaff/courseroleassign',
            get_string('ss_courseroleassign', 'enrol_studentstaff'),
            get_string('ss_courseroleassign_help', 'enrol_studentstaff'),
            5,  // Default.
            $allroles
        )
    );
}

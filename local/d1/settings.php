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
 * @package   local_d1
 * @copyright 2022 Robert Russo, Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage('local_d1', get_string('pluginname', 'local_d1'));

    $ADMIN->add('localplugins', $settings);

    // Grab the course categories.
    $ccategories = $DB->get_records('course_categories', null, 'name', 'id,name');

    // Loop through those roles and do stuff.
    foreach ($ccategories as $ocategory) {
        // Set the studentroles array for the dropdown.
        $ocategories[$ocategory->id] = $ocategory->name;
    }

    // Loop through those roles and do stuff.
    foreach ($ccategories as $pcategory) {
        // Set the studentroles array for the dropdown.
        $pcategories[$pcategory->id] = $pcategory->name;
    }

    $settings->add(
        new admin_setting_heading('local_d1_header', '',
        get_string('pluginname_desc', 'local_d1'))
    );

    // D1 Webservice Username.
    $settings->add(
        new admin_setting_configtext(
            'local_d1/username',
            get_string('d1_username', 'local_d1'),
            get_string('d1_username_help', 'local_d1'),
            '', PARAM_TEXT
        )
    );

    // D1 Webservice password.
    $settings->add(
        new admin_setting_configpasswordunmask(
            'local_d1/password',
            get_string('d1_password', 'local_d1'),
            get_string('d1_password_help', 'local_d1'),
            '', PARAM_RAW
        )
    );

    // D1 Websevice URL.
    $settings->add(
        new admin_setting_configtext(
            'local_d1/d1_wsurl',
            get_string('d1_wsurl', 'local_d1'),
            get_string('d1_wsurl_help', 'local_d1'),
            'https://yourschooltestws.destinyone.moderncampus.net', PARAM_TEXT
        )
    );

    // ODL course categories to process.
    $settings->add(
        new admin_setting_configmultiselect(
            'local_d1/ocategories',
            get_string('d1_ocategories', 'local_d1'),
            get_string('d1_ocategories_help', 'local_d1'),
            null, // Default.
            $ocategories
        )
    );

    // PD course categories to process.
    $settings->add(
        new admin_setting_configmultiselect(
            'local_d1/pcategories',
            get_string('d1_pcategories', 'local_d1'),
            get_string('d1_pcategories_help', 'local_d1'),
            null, // Default.
            $pcategories
        )
    );

    // Number of days to look in the past for ODL post grades.
    $settings->add(
        new admin_setting_configtext(
            'local_d1/odl_daysprior',
            get_string('odl_daysprior', 'local_d1'),
            get_string('odl_daysprior_help', 'local_d1'),
            '', PARAM_TEXT
        )
    );

    // Number of days to look in the past for PD post grades.
    $settings->add(
        new admin_setting_configtext(
            'local_d1/pd_daysprior',
            get_string('pd_daysprior', 'local_d1'),
            get_string('pd_daysprior_help', 'local_d1'),
            '', PARAM_TEXT
        )
    );

}

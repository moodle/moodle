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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Create the settings block.
$settings = new admin_settingpage($section, get_string('settings', 'block_lsuxe'));

$authdefault = 'manual
cas
ldap';

$intervaldefault = 'Monthly 720
Weekly 168
Daily 24
Hourly 1';


// Make sure only admins see this one.
if ($ADMIN->fulltree) {
    // --------------------------------
    // Dashboard Link.
    $settings->add(
        new admin_setting_heading(
            'lsuxe_link_back_title',
            get_string('lsuxe_link_back_title', 'block_lsuxe'),
            ''
        )
    );
    // --------------------------------
    // LSUXE Settings Title.
    $settings->add(
        new admin_setting_heading(
            'block_lsuxe_interval_main_title',
            get_string('xe_interval_main_title', 'block_lsuxe'),
            ''
        )
    );

    // --------------------------------
    // Interval Settings.
    $settings->add(
        new admin_setting_configtextarea(
            'block_lsuxe_interval_list',
            get_string('xe_interval_list', 'block_lsuxe'),
            'List of Moodle instances',
            $intervaldefault,
            PARAM_TEXT
        )
    );

    // ----------------------------------------------------------------
    // LSUXE Settings Title.
    $settings->add(
        new admin_setting_heading(
            'block_lsuxe_roles_title',
            get_string('xe_roles_title', 'block_lsuxe'),
            ''
        )
    );

    // Remote student role id.
    $settings->add(
        new admin_setting_configtext(
            'block_lsuxe_xestudentroleid',
            get_string('xe_studentroleid', 'block_lsuxe'),
            get_string('xe_studentroleid_help', 'block_lsuxe'),
            5 // Default.
        )
    );

    // Remote teacher role id.
    $settings->add(
        new admin_setting_configtext(
            'block_lsuxe_xeteacherroleid',
            get_string('xe_teacherroleid', 'block_lsuxe'),
            get_string('xe_teacherroleid_help', 'block_lsuxe'),
            3 // Default.
        )
    );

    // --------------------------------
    // Auth methods.
    $settings->add(
        new admin_setting_configtextarea(
            'block_lsuxe_xe_auth_method',
            get_string('xe_auth_method_title', 'block_lsuxe'),
            get_string('xe_auth_method_hint', 'block_lsuxe'),
            $authdefault,
            PARAM_TEXT
        )
    );
    // ----------------------------------------------------------------
    // LSUXE Experimental Title.
    $settings->add(
        new admin_setting_heading(
            'block_lsuxe_xe_experimental_title',
            get_string('xe_experimental_title', 'block_lsuxe'),
            ''
        )
    );

    // Use AJAX for form autocomplete.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_lsuxe_enable_form_auto',
            get_string('xe_form_auto_enable', 'block_lsuxe'),
            get_string('xe_form_auto_enable_desc', 'block_lsuxe'),
            0
        )
    );

    // --------------------------------
    // Use AJAX for form autocomplete.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_lsuxe_enable_dest_test',
            get_string('xe_form_enable_dest_source_test', 'block_lsuxe'),
            get_string('xe_form_enable_dest_source_test_desc', 'block_lsuxe'),
            0
        )
    );

    // --------------------------------
    // Enable wide view for Mappings/Moodles list.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_lsuxe_enable_wide_view',
            get_string('xe_form_enable_wide_view', 'block_lsuxe'),
            get_string('xe_form_enable_wide_view_desc', 'block_lsuxe'),
            0
        )
    );
}

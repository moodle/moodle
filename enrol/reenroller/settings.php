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
 * @package    enrol_reenroller
 * @copyright  2025 Onwards LSU Online & Continuing Education
 * @author     2025 Onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// require_once('classes/admin_setting_configdate.php');

// Use the custom class.
use enrol_reenroller\admin_setting_configdate;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // Create the settings page.
    $settings = new admin_settingpage(
        'enrol_reenroller_settings',
        get_string('pluginname', 'enrol_reenroller')
    );

    // Target category.
    $settings->add(new admin_setting_configmultiselect(
        'enrol_reenroller/targetcategory',
        get_string('setting:targetcategory', 'enrol_reenroller'),
        get_string('setting:targetcategory_desc', 'enrol_reenroller'),
        [],
        core_course_category::make_categories_list()
    ));

    // Source role.
    $roles = get_default_enrol_roles(context_system::instance());
    $settings->add(new admin_setting_configselect(
        'enrol_reenroller/sourcerole',
        get_string('setting:sourcerole', 'enrol_reenroller'),
        get_string('setting:sourcerole_desc', 'enrol_reenroller'),
        5,
        $roles
    ));

    // Target role.
    $settings->add(new admin_setting_configselect(
        'enrol_reenroller/targetrole',
        get_string('setting:targetrole', 'enrol_reenroller'),
        get_string('setting:targetrole_desc', 'enrol_reenroller'),
        5,
        $roles
    ));

    // Target enrollment method.
    $plugins = enrol_get_plugins(true);
    $methods = array_combine(array_keys($plugins), array_keys($plugins));

    $settings->add(new admin_setting_configselect(
        'enrol_reenroller/instance_name',
        get_string('setting:instance_name', 'enrol_reenroller'),
        get_string('setting:instance_name_desc', 'enrol_reenroller'),
        'd1',
        $methods
    ));

    // Expired enarollment date.
    $settings->add(new admin_setting_configdate(
        'enrol_reenroller/startdate',
        get_string('setting:startdate', 'enrol_reenroller'),
        get_string('setting:startdate_desc', 'enrol_reenroller'),
        time()
    ));

    // Add a heading.
    $settings->add(
        new admin_setting_heading(
            'enrol_reenroller/timelineheader',
            get_string('setting:timelineheader', 'enrol_reenroller'),
            get_string('setting:timelineheader_desc', 'enrol_reenroller')
        )
    );

    // Timeline value (integer).
    $settings->add(new admin_setting_configtext(
        'enrol_reenroller/timelinevalue',
        get_string('setting:timelinevalue', 'enrol_reenroller'),
        get_string('setting:timelinevalue_desc', 'enrol_reenroller'),
        1,
        PARAM_INT
    ));

    // Timeline unit (dropdown).
    $settings->add(new admin_setting_configselect(
        'enrol_reenroller/timelineunit',
        get_string('setting:timelineunit', 'enrol_reenroller'),
        get_string('setting:timelineunit_desc', 'enrol_reenroller'),
        'months',
        [
            'days'   => get_string('days'),
            'weeks'  => get_string('weeks'),
            'months' => get_string('months'),
            'years'  => get_string('years')
        ]
    ));
}

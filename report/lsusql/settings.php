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
 * Admin settings tree setup for the Custom SQL admin report.
 *
 * @package report_lsusql
 * @copyright 2011 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Grab this for getting the dataformat plugins.
require_once($CFG->libdir . '/classes/plugin_manager.php');

// Set the enabled dataformats.
$dformats = core_plugin_manager::instance()->get_plugins_of_type('dataformat');

// Build an array of dataformats for use in settings.
$dfoptions = array();
foreach ($dformats as $key => $dformat) {
    if ($dformat->is_enabled()) {
        $dfoptions[$key] = $dformat->name;
    }
}

if ($ADMIN->fulltree) {
    // Start of week, used for the day to run weekly reports.
    $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    $days = array_map(function($day) {
        return get_string($day, 'calendar');
    }, $days);

    $default = \core_calendar\type_factory::get_calendar_instance()->get_starting_weekday();

    // Setting this option to -1 will use the value from the site calendar.
    $options = [-1 => get_string('startofweek_default', 'report_lsusql', $days[$default])] + $days;
    $settings->add(new admin_setting_configselect('report_lsusql/startwday',
            get_string('startofweek', 'report_lsusql'),
            get_string('startofweek_desc', 'report_lsusql'), -1, $options));

    $settings->add(new admin_setting_configmultiselect('report_lsusql/dataformats',
            get_string('dataformats', 'report_lsusql'),
            get_string('dataformats_desc', 'report_lsusql'), array('csv'=>'csv'), $dfoptions));

    $settings->add(new admin_setting_configtext_with_maxlength('report_lsusql/querylimitdefault',
            get_string('querylimitdefault', 'report_lsusql'),
            get_string('querylimitdefault_desc', 'report_lsusql'), 5000, PARAM_INT, null, 10));

    $settings->add(new admin_setting_configtext_with_maxlength('report_lsusql/querylimitmaximum',
            get_string('querylimitmaximum', 'report_lsusql'),
            get_string('querylimitmaximum_desc', 'report_lsusql'), 5000, PARAM_INT, null, 10));

    $settings->add(new admin_setting_configtext('report_lsusql_badwordsexception',
            get_string('badwords', 'report_lsusql'),
            get_string('badwords_help', 'report_lsusql'), ''));
}

$ADMIN->add('reports', new admin_externalpage('report_lsusql',
        get_string('pluginname', 'report_lsusql'),
        new moodle_url('/report/lsusql/index.php'),
        'report/lsusql:view'));

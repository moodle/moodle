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
 * @package   local_report_license_usage
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Basic navigation settings
require($CFG->dirroot . '/local/iomad/lib/basicsettings.php');

$url = new moodle_url( '/local/report_completion_overview/index.php' );
$ADMIN->add('IomadReports', new admin_externalpage('repcompoverview',
             get_string('pluginname', 'local_report_completion_overview'),
             $url, 'local/report_completion_overview:view'));

if ($hassiteconfig && !empty($USER->id)) {

    $settings = new admin_settingpage('local_report_completion_overview', get_string('pluginname', 'local_report_completion_overview'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configduration(
        'local_report_completion_overview/warningduration',
        get_string('warningduration', 'local_report_completion_overview'),
        get_string('warningduration_help', 'local_report_completion_overview'),
        30*24*60*60)
    );

    $settings->add(new admin_setting_configcheckbox(
        'local_report_completion_overview/showfulldetail',
        get_string('showfulldetail', 'local_report_completion_overview'),
        get_string('showfulldetail_help', 'local_report_completion_overview'),
        true)
    );

    $settings->add(new admin_setting_configcheckbox(
        'local_report_completion_overview/showexpiryonly',
        get_string('showexpiryonly', 'local_report_completion_overview'),
        get_string('showexpiryonly_help', 'local_report_completion_overview'),
        false)
    );
}


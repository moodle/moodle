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
 * Flatfile enrolments plugin settings and presets.
 *
 * @package    enrol
 * @subpackage flatfile
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter - based on code by Petr Skoda and others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_flatfile_settings', '', get_string('pluginname_desc', 'enrol_flatfile')));

    $settings->add(new admin_setting_configtext('enrol_flatfile/location', get_string('location', 'enrol_flatfile'), '', ''));

    $settings->add(new admin_setting_configcheckbox('enrol_flatfile/mailstudents', get_string('mailstudents', 'enrol_flatfile'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_flatfile/mailteachers', get_string('mailteachers', 'enrol_flatfile'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_flatfile/mailadmins', get_string('mailadmin', 'enrol_flatfile'), '', 0));

    //--- mapping -------------------------------------------------------------------------------------------
    if (!during_initial_install()) {
        $settings->add(new admin_setting_heading('enrol_flatfile_mapping', get_string('mapping', 'enrol_flatfile'), ''));

        $roles = $DB->get_records('role', null, '', 'id, name, shortname');

        foreach ($roles as $id => $record) {
            $settings->add(new admin_setting_configtext('enrol_flatfile/map_'.$id, format_string($record->name), '', format_string($record->shortname)));
        }
    }
}

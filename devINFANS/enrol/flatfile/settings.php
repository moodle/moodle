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
 * @package    enrol_flatfile
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter - based on code by Petr Skoda and others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/adminlib.php');

if ($ADMIN->fulltree) {

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_flatfile_settings', '', get_string('pluginname_desc', 'enrol_flatfile')));

    $settings->add(new admin_setting_configfile('enrol_flatfile/location', get_string('location', 'enrol_flatfile'), get_string('location_desc', 'enrol_flatfile'), ''));

    $options = core_text::get_encodings();
    $settings->add(new admin_setting_configselect('enrol_flatfile/encoding', get_string('encoding', 'enrol_flatfile'), '', 'UTF-8', $options));

    $settings->add(new admin_setting_configcheckbox('enrol_flatfile/mailstudents', get_string('notifyenrolled', 'enrol_flatfile'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_flatfile/mailteachers', get_string('notifyenroller', 'enrol_flatfile'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_flatfile/mailadmins', get_string('notifyadmin', 'enrol_flatfile'), '', 0));

    $options = array(ENROL_EXT_REMOVED_UNENROL        => get_string('extremovedunenrol', 'enrol'),
                     ENROL_EXT_REMOVED_KEEP           => get_string('extremovedkeep', 'enrol'),
                     ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'));
    $settings->add(new admin_setting_configselect('enrol_flatfile/unenrolaction', get_string('extremovedaction', 'enrol'), get_string('extremovedaction_help', 'enrol'), ENROL_EXT_REMOVED_SUSPENDNOROLES, $options));

    // Note: let's reuse the ext sync constants and strings here, internally it is very similar,
    //       it describes what should happen when users are not supposed to be enrolled any more.
    $options = array(
        ENROL_EXT_REMOVED_KEEP           => get_string('extremovedkeep', 'enrol'),
        ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'),
        ENROL_EXT_REMOVED_UNENROL        => get_string('extremovedunenrol', 'enrol'),
    );
    $settings->add(new admin_setting_configselect('enrol_flatfile/expiredaction', get_string('expiredaction', 'enrol_flatfile'), get_string('expiredaction_help', 'enrol_flatfile'), ENROL_EXT_REMOVED_SUSPENDNOROLES, $options));

    //--- mapping -------------------------------------------------------------------------------------------
    if (!during_initial_install()) {
        $settings->add(new admin_setting_heading('enrol_flatfile_mapping', get_string('mapping', 'enrol_flatfile'), ''));

        $roles = role_fix_names(get_all_roles());

        foreach ($roles as $role) {
            $settings->add(new enrol_flatfile_role_setting($role));
        }
        unset($roles);
    }
}

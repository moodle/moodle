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
 * Meta enrolment plugin settings and presets.
 *
 * @package    enrol
 * @subpackage meta
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_meta_settings', '', get_string('pluginname_desc', 'enrol_meta')));

    if (!during_initial_install()) {
        $allroles = array();
        foreach (get_all_roles() as $role) {
                $rolename = strip_tags(format_string($role->name)) . ' ('. $role->shortname . ')';
                $allroles[$role->id] = $rolename;
        }
        $settings->add(new admin_setting_configmultiselect('enrol_meta/nosyncroleids', get_string('nosyncroleids', 'enrol_meta'), get_string('nosyncroleids_desc', 'enrol_meta'), array(), $allroles));
    }
}

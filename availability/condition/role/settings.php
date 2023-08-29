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
 * Availability role - Settings file
 *
 * @package    availability_role
 * @copyright  2017 David Knuplesch, Ulm University <david.knuplesch@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // Settings title to group preset related settings together with a common heading. We don't want a description here.
    $name = 'availability_role/setting_supportedrolesheading';
    $title = get_string('setting_supportedrolesheading', 'availability_role', null, true);
    $setting = new admin_setting_heading($name, $title, null);
    $settings->add($setting);

    // Setting for guest role.
    $name = 'availability_role/setting_supportguestrole';
    $title = get_string('setting_supportguestrole', 'availability_role', null, true);
    $description = get_string('setting_supportguestrole_desc', 'availability_role', null, true);
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $settings->add($setting);

    // Setting for not-logged-in role.
    $name = 'availability_role/setting_supportnotloggedinrole';
    $title = get_string('setting_supportnotloggedinrole', 'availability_role', null, true);
    $description = get_string('setting_supportnotloggedinrole_desc', 'availability_role', null, true);
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $settings->add($setting);
}


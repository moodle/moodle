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
 * This file defines the settings pages for licenses.
 *
 * @package    core
 * @copyright  2020 Tom Dickman <tomdickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/licenselib.php');

if ($hassiteconfig) {

    $temp = new admin_settingpage('licensesettings', new lang_string('licensesettings', 'admin'));

    $licenses = license_manager::get_active_licenses_as_array();

    $temp->add(new admin_setting_configselect('sitedefaultlicense',
        new lang_string('configsitedefaultlicense', 'admin'),
        new lang_string('configsitedefaultlicensehelp', 'admin'),
        'unknown',
        $licenses));
    $temp->add(new admin_setting_configcheckbox('rememberuserlicensepref',
        new lang_string('rememberuserlicensepref', 'admin'),
        new lang_string('rememberuserlicensepref_help', 'admin'),
        1));
    $ADMIN->add('license', $temp);
}

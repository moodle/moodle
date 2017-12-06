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
 * Admin settings and defaults.
 *
 * @package    auth_fc
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_fc/pluginname', '', new lang_string('auth_fcdescription', 'auth_fc')));

    // Host.
    $settings->add(new admin_setting_configtext('auth_fc/host', get_string('auth_fchost_key', 'auth_fc'),
            get_string('auth_fchost', 'auth_fc'), '127.0.0.1', PARAM_HOST));

    // Port.
    $settings->add(new admin_setting_configtext('auth_fc/fppport', get_string('auth_fcfppport_key', 'auth_fc'),
            get_string('auth_fcfppport', 'auth_fc'), '3333', PARAM_INT));

    // User ID.
    $settings->add(new admin_setting_configtext('auth_fc/userid', get_string('auth_fcuserid_key', 'auth_fc'),
            get_string('auth_fcuserid', 'auth_fc'), 'fcMoodle', PARAM_RAW));

    // Password.
    $settings->add(new admin_setting_configpasswordunmask('auth_fc/passwd', get_string('auth_fcpasswd_key', 'auth_fc'),
            get_string('auth_fcpasswd', 'auth_fc'), ''));

    // Creators.
    $settings->add(new admin_setting_configtext('auth_fc/creators', get_string('auth_fccreators_key', 'auth_fc'),
            get_string('auth_fccreators', 'auth_fc'), '', PARAM_RAW));

    // Password change URL.
    $settings->add(new admin_setting_configtext('auth_fc/changepasswordurl',
            get_string('auth_fcchangepasswordurl', 'auth_fc'),
            get_string('changepasswordhelp', 'auth'), '', PARAM_URL));

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('fc');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields,
            get_string('auth_fieldlocks_help', 'auth'), false, false);
}

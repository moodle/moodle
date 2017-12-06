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
 * @package    auth_nntp
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_nntp/pluginname', '', new lang_string('auth_nntpdescription', 'auth_nntp')));

    // Host.
    $settings->add(new admin_setting_configtext('auth_nntp/host', get_string('auth_nntphost_key', 'auth_nntp'),
            get_string('auth_nntphost', 'auth_nntp') . ' ' .get_string('auth_multiplehosts', 'auth'),
            '127.0.0.1', PARAM_RAW));

    // Port.
    $settings->add(new admin_setting_configtext('auth_nntp/port', get_string('auth_nntpport_key', 'auth_nntp'),
            get_string('auth_nntpport', 'auth_nntp'), '119', PARAM_INT));

    // Password change URL.
    $settings->add(new admin_setting_configtext('auth_nntp/changepasswordurl',
            get_string('auth_nntpchangepasswordurl_key', 'auth_nntp'),
            get_string('changepasswordhelp', 'auth'), '', PARAM_URL));

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('nntp');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields,
            get_string('auth_fieldlocks_help', 'auth'), false, false);
}

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
 * @package auth_pop3
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_pop3/pluginname', '', new lang_string('auth_pop3description', 'auth_pop3')));

    // Host.
    $settings->add(new admin_setting_configtext('auth_pop3/host', get_string('auth_pop3host_key', 'auth_pop3'),
            get_string('auth_pop3host', 'auth_pop3') . ' ' .get_string('auth_multiplehosts', 'auth'),
            '127.0.0.1', PARAM_RAW));

    // Type.
    $pop3options = array();
    $pop3types = array('pop3', 'pop3cert', 'pop3notls');
    foreach ($pop3types as $pop3type) {
        $pop3options[$pop3type] = $pop3type;
    }

    $settings->add(new admin_setting_configselect('auth_pop3/type',
        new lang_string('auth_pop3type_key', 'auth_pop3'),
        new lang_string('auth_pop3type', 'auth_pop3'), 'pop3', $pop3options));

    // Port.
    $settings->add(new admin_setting_configtext('auth_pop3/port', get_string('auth_pop3port_key', 'auth_pop3'),
            get_string('auth_pop3port', 'auth_pop3'), '143', PARAM_INT));

    // Mailbox.
    $settings->add(new admin_setting_configtext('auth_pop3/mailbox', get_string('auth_pop3mailbox_key', 'auth_pop3'),
            get_string('auth_pop3mailbox', 'auth_pop3'), 'INBOX', PARAM_ALPHANUMEXT));

    // Password change URL.
    $settings->add(new admin_setting_configtext('auth_pop3/changepasswordurl',
            get_string('auth_pop3changepasswordurl_key', 'auth_pop3'),
            get_string('changepasswordhelp', 'auth'), '', PARAM_URL));

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('pop3');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields,
            get_string('auth_fieldlocks_help', 'auth'), false, false);
}

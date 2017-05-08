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
 * @package auth_imap
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_imap/pluginname', '', new lang_string('auth_imapdescription', 'auth_imap')));

    // Host.
    $settings->add(new admin_setting_configtext('auth_imap/host', get_string('auth_imaphost_key', 'auth_imap'),
            get_string('auth_imaphost', 'auth_imap') . ' ' .get_string('auth_multiplehosts', 'auth'),
            '127.0.0.1', PARAM_RAW));

    // Type.
    $imapoptions = array();
    $imaptypes = array('imap', 'imapssl', 'imapcert', 'imapnosslcert', 'imaptls');
    foreach ($imaptypes as $imaptype) {
        $imapoptions[$imaptype] = $imaptype;
    }

    $settings->add(new admin_setting_configselect('auth_imap/type',
        new lang_string('auth_imaptype_key', 'auth_imap'),
        new lang_string('auth_imaptype', 'auth_imap'), 'imap', $imapoptions));

    // Port.
    $settings->add(new admin_setting_configtext('auth_imap/port', get_string('auth_imapport_key', 'auth_imap'),
            get_string('auth_imapport', 'auth_imap'), '143', PARAM_INT));

    // Password change URL.
    $settings->add(new admin_setting_configtext('auth_imap/changepasswordurl',
            get_string('auth_imapchangepasswordurl_key', 'auth_imap'),
            get_string('changepasswordhelp', 'auth'), '', PARAM_URL));

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('imap');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields,
            get_string('auth_fieldlocks_help', 'auth'), false, false);

}

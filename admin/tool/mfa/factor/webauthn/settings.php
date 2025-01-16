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
 * Settings
 *
 * @package     factor_webauthn
 * @author      Alex Morris <alex.morris@catalyst.net.nz
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading('factor_webauthn/description', '',
        new lang_string('settings:description', 'factor_webauthn')));
    $settings->add(new admin_setting_heading('factor_webauthn/settings', new lang_string('settings', 'moodle'), ''));

    $enabled = new admin_setting_configcheckbox('factor_webauthn/enabled',
        new lang_string('settings:enablefactor', 'tool_mfa'),
        new lang_string('settings:enablefactor_help', 'tool_mfa'), 0);
    $enabled->set_updatedcallback(function() {
        \tool_mfa\manager::do_factor_action('webauthn', get_config('factor_webauthn', 'enabled') ? 'enable' : 'disable');
    });
    $settings->add($enabled);

    $settings->add(new admin_setting_configtext('factor_webauthn/weight',
        new lang_string('settings:weight', 'tool_mfa'),
        new lang_string('settings:weight_help', 'tool_mfa'), 100, PARAM_INT));

    $authenticators = [
        'usb' => get_string('authenticator:usb', 'factor_webauthn'),
        'nfc' => get_string('authenticator:nfc', 'factor_webauthn'),
        'ble' => get_string('authenticator:ble', 'factor_webauthn'),
        'hybrid' => get_string('authenticator:hybrid', 'factor_webauthn'),
        'internal' => get_string('authenticator:internal', 'factor_webauthn'),
    ];
    $settings->add(new admin_setting_configmultiselect('factor_webauthn/authenticatortypes',
        new lang_string('settings:authenticatortypes', 'factor_webauthn'),
        new lang_string('settings:authenticatortypes_help', 'factor_webauthn'),
        array_keys($authenticators), $authenticators));

    $settings->add(new admin_setting_configselect('factor_webauthn/userverification',
        new lang_string('settings:userverification', 'factor_webauthn'),
        new lang_string('settings:userverification_help', 'factor_webauthn'),
        'preferred',
        $userverification = [
            'required' => get_string('userverification:required', 'factor_webauthn'),
            'preferred' => get_string('userverification:preferred', 'factor_webauthn'),
            'discouraged' => get_string('userverification:discouraged', 'factor_webauthn'),
        ]));
}

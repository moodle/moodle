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
 * Login administration settings.
 *
 * @package    core_admin
 * @copyright  2026 Muhammad Arnaldo <muhammad.arnaldo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $temp = new admin_settingpage('loginsettings', new lang_string('loginsettings', 'admin'));

    // Force to show the login page for fresh installs.
    $temp->add(new admin_setting_configcheckbox(
        'forcelogin',
        new lang_string('forcelogin', 'admin'),
        new lang_string('configforcelogin', 'admin'),
        1
    ));

    // Self registration.
    $temp->add(new admin_setting_special_registerauth());

    // Allow log in via email.
    $temp->add(new admin_setting_configcheckbox(
        'authloginviaemail',
        new lang_string('authloginviaemail', 'core_auth'),
        new lang_string('authloginviaemail_desc', 'core_auth'),
        1,
    ));

    // Autofocus login page form.
    $temp->add(new admin_setting_configcheckbox(
        'loginpageautofocus',
        new lang_string('loginpageautofocus', 'admin'),
        new lang_string('loginpageautofocus_help', 'admin'),
        0
    ));

    // Guest login button.
    $temp->add(new admin_setting_configselect(
        'guestloginbutton',
        new lang_string('guestloginbutton', 'auth'),
        new lang_string('showguestlogin', 'auth'),
        '0',
        [0 => new lang_string('hide'), 1 => new lang_string('show')],
    ));

    // Alternate login URL.
    $temp->add(new admin_setting_configtext(
        'alternateloginurl',
        new lang_string('alternateloginurl', 'auth'),
        new lang_string('alternatelogin', 'auth', htmlspecialchars(get_login_url(), ENT_COMPAT)),
        ''
    ));

    // Display manual login form.
    $temp->add(new admin_setting_configcheckbox(
        'showloginform',
        new lang_string('showloginform', 'core_auth'),
        new lang_string('showloginform_desc', 'core_auth'),
        1
    ));

    // Forgotten password URL.
    $temp->add(new admin_setting_configtext(
        'forgottenpasswordurl',
        new lang_string('forgottenpasswordurl', 'auth'),
        new lang_string('forgottenpassword', 'auth'),
        '',
        PARAM_URL
    ));

    // Instructions shown on the login page.
    $temp->add(new admin_setting_confightmleditor(
        'auth_instructions',
        new lang_string('instructions', 'auth'),
        new lang_string('authinstructions', 'auth'),
        ''
    ));

    // Enable reCAPTCHA for login.
    $temp->add(new admin_setting_configselect(
        'enableloginrecaptcha',
        new lang_string('auth_loginrecaptcha', 'auth'),
        new lang_string('auth_loginrecaptcha_desc', 'auth'),
        0,
        [
            new lang_string('no'),
            new lang_string('yes'),
        ],
    ));

    // Enable reCAPTCHA for forgot password.
    $temp->add(new admin_setting_configcheckbox(
        'enableforgotpasswordrecaptcha',
        new lang_string('auth_forgotpasswordrecaptcha', 'auth'),
        new lang_string('auth_forgotpasswordrecaptcha_desc', 'auth'),
        0,
    ));

    // ReCAPTCHA site key.
    $setting = new admin_setting_configtext(
        'recaptchapublickey',
        new lang_string('recaptchapublickey', 'admin'),
        new lang_string('configrecaptchapublickey', 'admin'),
        '',
        PARAM_NOTAGS
    );
    $setting->set_force_ltr(true);
    $temp->add($setting);

    // ReCAPTCHA secret key.
    $setting = new admin_setting_configtext(
        'recaptchaprivatekey',
        new lang_string('recaptchaprivatekey', 'admin'),
        new lang_string('configrecaptchaprivatekey', 'admin'),
        '',
        PARAM_NOTAGS
    );
    $setting->set_force_ltr(true);
    $temp->add($setting);

    // Password visibility toggle.
    $temp->add(new admin_setting_configselect(
        'loginpasswordtoggle',
        new lang_string('auth_loginpasswordtoggle', 'auth'),
        new lang_string('auth_loginpasswordtoggle_desc', 'auth'),
        TOGGLE_SENSITIVE_ENABLED,
        [
            TOGGLE_SENSITIVE_DISABLED => get_string('disabled', 'admin'),
            TOGGLE_SENSITIVE_ENABLED => get_string('enabled', 'admin'),
            TOGGLE_SENSITIVE_SMALL_SCREENS_ONLY => get_string('smallscreensonly', 'admin'),
        ],
    ));

    $ADMIN->add('login', $temp);
}

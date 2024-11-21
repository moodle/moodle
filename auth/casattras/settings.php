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
 * @package    auth_casattras
 * @copyright  2019 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // CAS server configuration label.
    $settings->add(new admin_setting_heading('auth_casattras/pluginname', '',
            new lang_string('auth_casattras_server_settings', 'auth_casattras')));

    // Authentication method name.
    $settings->add(new admin_setting_configtext('auth_casattras/auth_name',
            new lang_string('auth_casattras_auth_name', 'auth_casattras'),
            new lang_string('auth_casattras_auth_name_description', 'auth_casattras'),
            new lang_string('auth_casattras_auth_service', 'auth_casattras'),
            PARAM_RAW_TRIMMED));

    // Authentication method logo.
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $settings->add(new admin_setting_configstoredfile('auth_casattras/auth_logo',
            new lang_string('auth_casattras_auth_logo', 'auth_casattras'),
            new lang_string('auth_casattras_auth_logo_description', 'auth_casattras'), 'logo', 0, $opts));

    // Hostname.
    $settings->add(new admin_setting_configtext('auth_casattras/hostname',
            new lang_string('auth_casattras_hostname_key', 'auth_casattras'),
            new lang_string('auth_casattras_hostname', 'auth_casattras'), '', PARAM_RAW_TRIMMED));

    // Base URI.
    $settings->add(new admin_setting_configtext('auth_casattras/baseuri',
            new lang_string('auth_casattras_baseuri_key', 'auth_casattras'),
            new lang_string('auth_casattras_baseuri', 'auth_casattras'), '', PARAM_RAW_TRIMMED));

    // Port.
    $settings->add(new admin_setting_configtext('auth_casattras/port',
            new lang_string('auth_casattras_port_key', 'auth_casattras'),
            new lang_string('auth_casattras_port', 'auth_casattras'), '', PARAM_INT));

    // CAS Version.
    $casversions = array();
    $casversions['CAS_VERSION_1_0'] = 'CAS 1.0';
    $casversions['CAS_VERSION_2_0'] = 'CAS 2.0';
    $casversions['CAS_VERSION_3_0'] = 'CAS 3.0';
    $casversions['SAML_VERSION_1_1'] = 'SAML 1.1';
    $settings->add(new admin_setting_configselect('auth_casattras/casversion',
            new lang_string('auth_casattras_casversion', 'auth_casattras'),
            new lang_string('auth_casattras_version', 'auth_casattras'), 'CAS_VERSION_2_0', $casversions));

    // Proxy.
    $yesno = array(
        new lang_string('no'),
        new lang_string('yes'),
    );

    $settings->add(new admin_setting_configselect('auth_casattras/proxycas',
            new lang_string('auth_casattras_proxycas_key', 'auth_casattras'),
            new lang_string('auth_casattras_proxycas', 'auth_casattras'), 0 , $yesno));

    // Logout option.
    $settings->add(new admin_setting_configselect('auth_casattras/logoutcas',
            new lang_string('auth_casattras_logoutcas_key', 'auth_casattras'),
            new lang_string('auth_casattras_logoutcas', 'auth_casattras'), 0 , $yesno));

    // Multi-auth.
    $settings->add(new admin_setting_configselect('auth_casattras/multiauth',
            new lang_string('auth_casattras_multiauth_key', 'auth_casattras'),
            new lang_string('auth_casattras_multiauth', 'auth_casattras'), 0 , $yesno));

    // Server validation.
    $settings->add(new admin_setting_configselect('auth_casattras/certificatecheck',
            new lang_string('auth_casattras_certificate_check_key', 'auth_casattras'),
            new lang_string('auth_casattras_certificate_check', 'auth_casattras'), 0 , $yesno));

    // Certificate path.
    $settings->add(new admin_setting_configfile('auth_casattras/certificatepath',
            new lang_string('auth_casattras_certificate_path_key', 'auth_casattras'),
            new lang_string('auth_casattras_certificate_path', 'auth_casattras'), ''));

    // Alt Logout URL.
    $settings->add(new admin_setting_configtext('auth_casattras/logoutreturnurl',
            new lang_string('auth_casattras_logout_return_url_key', 'auth_casattras'),
            new lang_string('auth_casattras_logout_return_url', 'auth_casattras'), '', PARAM_URL));

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('casattras');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields, '', true, true,
            $authplugin->get_custom_user_profile_fields());
}

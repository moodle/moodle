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
 * @package    auth_shibboleth
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // We use a couple of custom admin settings since we need to massage the data before it is inserted into the DB.
    require_once($CFG->dirroot.'/auth/shibboleth/classes/admin_setting_special_wayf_select.php');
    require_once($CFG->dirroot.'/auth/shibboleth/classes/admin_setting_special_idp_configtextarea.php');

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_shibboleth/pluginname', '',
            new lang_string('auth_shibbolethdescription', 'auth_shibboleth')));

    // Username.
    $settings->add(new admin_setting_configtext('auth_shibboleth/user_attribute', get_string('username'),
            get_string('auth_shib_username_description', 'auth_shibboleth'), '', PARAM_RAW));

    // COnvert Data configuration file.
    $settings->add(new admin_setting_configfile('auth_shibboleth/convert_data',
            get_string('auth_shib_convert_data', 'auth_shibboleth'),
            get_string('auth_shib_convert_data_description', 'auth_shibboleth'), ''));

    // WAYF.
    $settings->add(new auth_shibboleth_admin_setting_special_wayf_select());

    // Organization_selection.
    $settings->add(new auth_shibboleth_admin_setting_special_idp_configtextarea());

    // Logout handler.
    $settings->add(new admin_setting_configtext('auth_shibboleth/logout_handler',
            get_string('auth_shib_logout_url', 'auth_shibboleth'),
            get_string('auth_shib_logout_url_description', 'auth_shibboleth'), '', PARAM_URL));

    // Logout return URL.
    $settings->add(new admin_setting_configtext('auth_shibboleth/logout_return_url',
            get_string('auth_shib_logout_return_url', 'auth_shibboleth'),
            get_string('auth_shib_logout_return_url_description', 'auth_shibboleth'), '', PARAM_URL));

    // Authentication method name.
    $settings->add(new admin_setting_configtext('auth_shibboleth/login_name',
            get_string('auth_shib_auth_method', 'auth_shibboleth'),
            get_string('auth_shib_auth_method_description', 'auth_shibboleth'), 'Shibboleth Login', PARAM_RAW_TRIMMED));

    // Authentication method logo.
    $settings->add(new admin_setting_configstoredfile('auth_shibboleth/auth_logo',
                get_string('auth_shib_auth_logo', 'auth_shibboleth'),
                get_string('auth_shib_auth_logo_description', 'auth_shibboleth'), 'logo', 0, ['accepted_types' => ['image']]));

    // Login directions.
    $settings->add(new admin_setting_configtextarea('auth_shibboleth/auth_instructions',
            get_string('auth_shib_instructions_key', 'auth_shibboleth'),
            get_string('auth_shib_instructions_help', 'auth_shibboleth', $CFG->wwwroot.'/auth/shibboleth/index.php'),
            get_string('auth_shib_instructions', 'auth_shibboleth', $CFG->wwwroot.'/auth/shibboleth/index.php'), PARAM_RAW_TRIMMED));

    // Password change URL.
    $settings->add(new admin_setting_configtext('auth_shibboleth/changepasswordurl',
            get_string('auth_shib_changepasswordurl', 'auth_shibboleth'),
            get_string('changepasswordhelp', 'auth'), '', PARAM_URL));

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('shibboleth');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields,
            '', true, false, $authplugin->get_custom_user_profile_fields());

}

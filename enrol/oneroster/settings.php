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
 * One Roster Enrolment plugin.
 *
 * This plugin synchronises enrolment and roles with a One Roster endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading(
        'enrol_oneroster',
        '',
        get_string('pluginname_desc', 'enrol_database')
    ));

    // Connections settings:
    // - One Roster version;
    // - OAuth version;
    // - OAuth Token URL;
    // - One Roster Root URL;
    // - OAuth Client; and
    // - OAuth Secret.

    $settings->add(new admin_setting_heading(
        'enrol_oneroster/connection',
        get_string('settings_connection_settings', 'enrol_oneroster'),
        ''
    ));

    // One Roster version:
    // - Version 1.0 (obsolete - not currently supported);
    // - Version 1.1 (current); and
    // - Version 1.2 (next - not yet supported).
    $settings->add(new admin_setting_configselect(
        'enrol_oneroster/oneroster_version',
        get_string('settings_connection_oneroster_version', 'enrol_oneroster'),
        get_string('settings_connection_oneroster_version_desc', 'enrol_oneroster'),
        enrol_oneroster\client_helper::VERSION_V1P1,
        [
            enrol_oneroster\client_helper::VERSION_V1P1 => get_string('settings_connection_v1p1', 'enrol_oneroster'),
        ]
    ));

    // OAuth version:
    // - OAuth 1 (deprecated); and
    // - OAuth 2.0 (current).
    $settings->add(new admin_setting_configselect(
        'enrol_oneroster/oauth_version',
        get_string('settings_connection_oauth_version', 'enrol_oneroster'),
        get_string('settings_connection_oauth_version_desc', 'enrol_oneroster'),
        enrol_oneroster\client_helper::OAUTH_20,
        [
            enrol_oneroster\client_helper::OAUTH_10 => get_string('settings_connection_oauth_1', 'enrol_oneroster'),
            enrol_oneroster\client_helper::OAUTH_20 => get_string('settings_connection_oauth_2', 'enrol_oneroster'),
        ]
    ));

    // Connection settings - OAuth Token URL.
    $settings->add(new admin_setting_configtext(
        'enrol_oneroster/token_url',
        get_string('settings_connection_token_url', 'enrol_oneroster'),
        get_string('settings_connection_token_url_desc', 'enrol_oneroster'),
        ''
    ));

    // Connection settings - One Roster Root URL.
    $settings->add(new admin_setting_configtext(
        'enrol_oneroster/root_url',
        get_string('settings_connection_root_url', 'enrol_oneroster'),
        get_string('settings_connection_root_url_desc', 'enrol_oneroster'),
        ''
    ));

    // Connection settings - OAuth Client.
    $settings->add(new admin_setting_configtext(
        'enrol_oneroster/clientid',
        get_string('settings_connection_clientid', 'enrol_oneroster'),
        get_string('settings_connection_clientid_desc', 'enrol_oneroster'),
        ''
    ));

    // Connection settings - OAuth Secret.
    $settings->add(new admin_setting_configpasswordunmask(
        'enrol_oneroster/secret',
        get_string('settings_connection_secret', 'enrol_oneroster'),
        get_string('settings_connection_secret_desc', 'enrol_oneroster'),
        ''
    ));

    // Connection settings - Page size.
    $settings->add(new admin_setting_configtext(
        'enrol_oneroster/pagesize',
        get_string('settings_connection_pagesize', 'enrol_oneroster'),
        get_string('settings_connection_pagesize_desc', 'enrol_oneroster'),
        200,
        PARAM_INT,
        4
    ));

    // Test connection.
    $settings->add(new admin_setting_heading(
        'enrol_oneroster/testconnection',
        new lang_string('settings_testconnection', 'enrol_oneroster'),
        new lang_string('settings_testconnection_detail', 'enrol_oneroster')
    ));


    $settings->add(new admin_setting_heading(
        'enrol_oneroster/testconnection_action',
        '',
        html_writer::link(
            new moodle_url('/enrol/oneroster/testconnection.php'),
            get_string('settings_testconnection_link', 'enrol_oneroster')
        )
    ));

    // User creation settings.
    $settings->add(new admin_setting_heading(
        'enrol_oneroster/newuser',
        get_string('settings_newuser', 'enrol_oneroster'),
        get_string('settings_newuser_desc', 'enrol_oneroster')
    ));

    $enabled = get_string('pluginenabled', 'core_plugin');
    $disabled = get_string('plugindisabled', 'core_plugin');

    $authoptions = [
        $enabled => [],
        $disabled => [],
    ];
    $auths = core_component::get_plugin_list('auth');
    foreach (array_keys($auths) as $auth) {
        $authinst = get_auth_plugin($auth);

        if (!$authinst->is_internal()) {
            $cannotchangeusername[] = $auth;
        }

        if (is_enabled_auth($auth)) {
            $authoptions[$enabled][$auth] = get_string('pluginname', "auth_{$auth}");
        } else {
            $authoptions[$disabled][$auth] = get_string('pluginname', "auth_{$auth}");
        }
    }

    $settings->add(
        new admin_setting_configselect(
            'enrol_oneroster/newuser_auth',
            get_string('settings_newuser_auth', 'enrol_oneroster'),
            get_string('settings_newuser_auth_desc', 'enrol_oneroster'),
            'manual',
            $authoptions
        )
    );


    // Role mappings for the following One Roster roles:
    // - student;
    // - teacher;
    // - parent (not current supported);
    // - guardian (not currently supported);
    // - relative (not currently supported);
    // - aide (not currently supported);
    // - administratort (not currently supported); and
    // - proctor.

    $settings->add(new admin_setting_heading(
        'enrol_oneroster/rolemapping',
        get_string('settings_rolemapping', 'enrol_oneroster'),
        get_string('settings_rolemapping_generic_desc', 'enrol_oneroster')
    ));

    $allroles = array_merge(
        [
            -1 => 'notmapped',
        ],
        array_map(function($role) {

            return $role->shortname;
        }, get_all_roles(null))
    );
    $courseroles = array_merge(
        [
            -1 => get_string('settings_notmapped', 'enrol_oneroster'),
        ],
        role_get_names(\context_course::instance(SITEID), ROLENAME_ALIAS, true)
    );

    // Mapping for the 'student' role.
    \enrol_oneroster\settings::add_role_mapping($settings, 'student', $allroles, $courseroles, 'student');

    // Mapping for the 'teacher' role.
    \enrol_oneroster\settings::add_role_mapping($settings, 'teacher', $allroles, $courseroles, 'editingteacher');

    // Mapping for the 'aide' role.
    \enrol_oneroster\settings::add_role_mapping($settings, 'aide', $allroles, $courseroles);

    // Mapping for the 'proctor' role.
    \enrol_oneroster\settings::add_role_mapping($settings, 'proctor', $allroles, $courseroles);

    // Mapping for the 'parent' role.
    \enrol_oneroster\settings::add_role_mapping($settings, 'parent', $allroles, $courseroles);

    // Mapping for the 'guardian' role.
    \enrol_oneroster\settings::add_role_mapping($settings, 'guardian', $allroles, $courseroles);

    // Mapping for the 'relative' role.
    \enrol_oneroster\settings::add_role_mapping($settings, 'relative', $allroles, $courseroles);

    // Data to synchronise:
    // - Fetch list of available schools button; and
    // - List of schools to sync.

    $settings->add(new admin_setting_heading(
        'enrol_oneroster/datasync',
        get_string('settings_datasync', 'enrol_oneroster'),
        ''
    ));

    $availableschools = [];
    if ($availableschoolsjson = get_config('enrol_oneroster', 'availableschools')) {
        $availableschools = (array) json_decode($availableschoolsjson);
    }

    if (empty($availableschoolsjson)) {
        $availableschools[''] = get_string('none', 'admin');
    }

    $settings->add(new admin_setting_configmultiselect(
        'enrol_oneroster/datasync_schools',
        get_string('settings_datasync_schools', 'enrol_oneroster'),
        get_string('settings_datasync_schools_desc', 'enrol_oneroster'),
        [],
        $availableschools
    ));

}

if ($hassiteconfig) {
    $ADMIN->add(
        'enrolments',
        new admin_externalpage(
            'enrol_oneroster/testconnection',
            get_string('test_oneroster_connection', 'enrol_oneroster'),
            new moodle_url('/enrol/oneroster/testconnection.php'),
            'moodle/site:config',
            true
        )
    );
}

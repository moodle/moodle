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
 * Plugin settings.
 *
 * @package auth_iomadoidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

defined('MOODLE_INTERNAL') || die();

use auth_iomadoidc\adminsetting\auth_iomadoidc_admin_setting_iconselect;
use auth_iomadoidc\adminsetting\auth_iomadoidc_admin_setting_loginflow;
use auth_iomadoidc\adminsetting\auth_iomadoidc_admin_setting_redirecturi;
use auth_iomadoidc\utils;

// IOMAD
require_once($CFG->dirroot . '/local/iomad/lib/company.php');
$companyid = iomad::get_my_companyid(context_system::instance(), false);
if (!empty($companyid)) {
    $postfix = "_$companyid";
} else {
    $postfix = ""; 
}

require_once($CFG->dirroot . '/auth/iomadoidc/lib.php');

if ($hassiteconfig) {
    // Add folder for IOMAD OIDC settings.
    $iomadoidcfolder = new admin_category('iomadoidcfolder', get_string('pluginname', 'auth_iomadoidc'));
    $ADMIN->add('authsettings', $iomadoidcfolder);

    // Application configuration page.
    $ADMIN->add('iomadoidcfolder', new admin_externalpage('auth_iomadoidc_application', get_string('settings_page_application', 'auth_iomadoidc'),
        new moodle_url('/auth/iomadoidc/manageapplication.php')));

    // Other settings page and its settings.
    $settings = new admin_settingpage($section, get_string('settings_page_other_settings', 'auth_iomadoidc'));

    // Basic heading.
    $settings->add(new admin_setting_heading('auth_iomadoidc/basic_heading', get_string('heading_basic', 'auth_iomadoidc'),
        get_string('heading_basic_desc', 'auth_iomadoidc')));

    // Redirect URI.
    $settings->add(new auth_iomadoidc_admin_setting_redirecturi('auth_iomadoidc/redirecturi'. $postfix,
        get_string('cfg_redirecturi_key', 'auth_iomadoidc'), get_string('cfg_redirecturi_desc', 'auth_iomadoidc'), utils::get_redirecturl()));

    // Link to authentication options.
    $authenticationconfigurationurl = new moodle_url('/auth/iomadoidc/manageapplication.php');
    $settings->add(new admin_setting_description('auth_iomadoidc/authenticationlink'. $postfix,
        get_string('settings_page_application', 'auth_iomadoidc'),
        get_string('cfg_authenticationlink_desc', 'auth_iomadoidc', $authenticationconfigurationurl->out())));

    // Additional options heading.
    $settings->add(new admin_setting_heading('auth_iomadoidc/additional_options_heading',
        get_string('heading_additional_options', 'auth_iomadoidc'), get_string('heading_additional_options_desc', 'auth_iomadoidc')));

    // Force redirect.
    $settings->add(new admin_setting_configcheckbox('auth_iomadoidc/forceredirect'. $postfix,
        get_string('cfg_forceredirect_key', 'auth_iomadoidc'), get_string('cfg_forceredirect_desc', 'auth_iomadoidc'), 0));

    // Auto-append.
    $settings->add(new admin_setting_configtext('auth_iomadoidc/autoappend'. $postfix,
        get_string('cfg_autoappend_key', 'auth_iomadoidc'), get_string('cfg_autoappend_desc', 'auth_iomadoidc'), '', PARAM_TEXT));

    // Domain hint.
    $settings->add(new admin_setting_configtext('auth_iomadoidc/domainhint'. $postfix,
        get_string('cfg_domainhint_key', 'auth_iomadoidc'), get_string('cfg_domainhint_desc', 'auth_iomadoidc'), '' , PARAM_TEXT));

    // Login flow.
    $settings->add(new auth_iomadoidc_admin_setting_loginflow('auth_iomadoidc/loginflow'. $postfix,
        get_string('cfg_loginflow_key', 'auth_iomadoidc'), '', 'authcode'));

    // User restrictions heading.
    $settings->add(new admin_setting_heading('auth_iomadoidc/user_restrictions_heading',
        get_string('heading_user_restrictions', 'auth_iomadoidc'), get_string('heading_user_restrictions_desc', 'auth_iomadoidc')));

    // User restrictions.
    $settings->add(new admin_setting_configtextarea('auth_iomadoidc/userrestrictions'. $postfix,
        get_string('cfg_userrestrictions_key', 'auth_iomadoidc'), get_string('cfg_userrestrictions_desc', 'auth_iomadoidc'), '', PARAM_TEXT));

    // User restrictions case sensitivity.
    $settings->add(new admin_setting_configcheckbox('auth_iomadoidc/userrestrictionscasesensitive'. $postfix,
        get_string('cfg_userrestrictionscasesensitive_key', 'auth_iomadoidc'),
        get_string('cfg_userrestrictionscasesensitive_desc', 'auth_iomadoidc'), '1'));

    // Sign out integration heading.
    $settings->add(new admin_setting_heading('auth_iomadoidc/sign_out_heading',
        get_string('heading_sign_out', 'auth_iomadoidc'), get_string('heading_sign_out_desc', 'auth_iomadoidc')));

    // Single sign out from Moodle to IdP.
    $settings->add(new admin_setting_configcheckbox('auth_iomadoidc/single_sign_off'. $postfix,
        get_string('cfg_signoffintegration_key', 'auth_iomadoidc'),
        get_string('cfg_signoffintegration_desc', 'auth_iomadoidc', $CFG->wwwroot), '0'));

    // IdP logout endpoint.
    $settings->add(new admin_setting_configtext('auth_iomadoidc/logouturi'. $postfix,
        get_string('cfg_logoutendpoint_key', 'auth_iomadoidc'), get_string('cfg_logoutendpoint_desc', 'auth_iomadoidc'),
        'https://login.microsoftonline.com/common/oauth2/logout', PARAM_URL));

    // Front channel logout URL.
    $settings->add(new auth_iomadoidc_admin_setting_redirecturi('auth_iomadoidc/logoutendpoint'. $postfix,
        get_string('cfg_frontchannellogouturl_key', 'auth_iomadoidc'), get_string('cfg_frontchannellogouturl_desc', 'auth_iomadoidc'),
        utils::get_frontchannellogouturl()));

    // Display heading.
    $settings->add(new admin_setting_heading('auth_iomadoidc/display_heading',
        get_string('heading_display', 'auth_iomadoidc'), get_string('heading_display_desc', 'auth_iomadoidc')));

    // Provider Name (opname).
    $settings->add(new admin_setting_configtext('auth_iomadoidc/opname'. $postfix,
        get_string('cfg_opname_key', 'auth_iomadoidc'), get_string('cfg_opname_desc', 'auth_iomadoidc'),
        get_string('pluginname', 'auth_iomadoidc'), PARAM_TEXT));

    // Icon.
    $icons = [
        [
            'pix' => 'o365',
            'alt' => new lang_string('cfg_iconalt_o365', 'auth_iomadoidc'),
            'component' => 'auth_iomadoidc',
        ],
        [
            'pix' => 't/locked',
            'alt' => new lang_string('cfg_iconalt_locked', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 't/lock',
            'alt' => new lang_string('cfg_iconalt_lock', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 't/go',
            'alt' => new lang_string('cfg_iconalt_go', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 't/stop',
            'alt' => new lang_string('cfg_iconalt_stop', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 't/user',
            'alt' => new lang_string('cfg_iconalt_user', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 'u/user35',
            'alt' => new lang_string('cfg_iconalt_user2', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 'i/permissions',
            'alt' => new lang_string('cfg_iconalt_key', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 'i/cohort',
            'alt' => new lang_string('cfg_iconalt_group', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 'i/groups',
            'alt' => new lang_string('cfg_iconalt_group2', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 'i/mnethost',
            'alt' => new lang_string('cfg_iconalt_mnet', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 'i/permissionlock',
            'alt' => new lang_string('cfg_iconalt_userlock', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 't/more',
            'alt' => new lang_string('cfg_iconalt_plus', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 't/approve',
            'alt' => new lang_string('cfg_iconalt_check', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
        [
            'pix' => 't/right',
            'alt' => new lang_string('cfg_iconalt_rightarrow', 'auth_iomadoidc'),
            'component' => 'moodle',
        ],
    ];
    $settings->add(new auth_iomadoidc_admin_setting_iconselect('auth_iomadoidc/icon'. $postfix,
        get_string('cfg_icon_key', 'auth_iomadoidc'), get_string('cfg_icon_desc', 'auth_iomadoidc'), 'auth_iomadoidc:o365', $icons));

    // Custom icon.
    $configkey = new lang_string('cfg_customicon_key', 'auth_iomadoidc');
    $configdesc = new lang_string('cfg_customicon_desc', 'auth_iomadoidc');
    $customiconsetting = new admin_setting_configstoredfile('auth_iomadoidc/customicon'. $postfix,
        get_string('cfg_customicon_key', 'auth_iomadoidc'), get_string('cfg_customicon_desc', 'auth_iomadoidc'), 'customicon');
    $customiconsetting->set_updatedcallback('auth_iomadoidc_initialize_customicon');
    $settings->add($customiconsetting);

    // Debugging heading.
    $settings->add(new admin_setting_heading('auth_iomadoidc/debugging_heading',
        get_string('heading_debugging', 'auth_iomadoidc'), get_string('heading_debugging_desc', 'auth_iomadoidc')));

    // Record debugging messages.
    $settings->add(new admin_setting_configcheckbox('auth_iomadoidc/debugmode'. $postfix,
        get_string('cfg_debugmode_key', 'auth_iomadoidc'), get_string('cfg_debugmode_desc', 'auth_iomadoidc'), '0'));

    $ADMIN->add('iomadoidcfolder', $settings);

    // Cleanup IOMAD OIDC tokens page.
    $ADMIN->add('iomadoidcfolder', new admin_externalpage('auth_iomadoidc_cleanup_iomadoidc_tokens',
        get_string('settings_page_cleanup_iomadoidc_tokens', 'auth_iomadoidc'), new moodle_url('/auth/iomadoidc/cleanupiomadoidctokens.php')));

    // Other settings page and its settings.
    $fieldmappingspage = new admin_settingpage('auth_iomadoidc_field_mapping', get_string('settings_page_field_mapping', 'auth_iomadoidc'));
    $ADMIN->add('iomadoidcfolder', $fieldmappingspage);

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('iomadoidc');
    auth_iomadoidc_display_auth_lock_options($fieldmappingspage, $authplugin->authtype, $authplugin->userfields,
        get_string('cfg_field_mapping_desc', 'auth_iomadoidc'), true, false, $authplugin->get_custom_user_profile_fields());
}

$settings = null;

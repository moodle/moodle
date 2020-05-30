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
 * @package auth_iomadoidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

require_once(__DIR__.'/lib.php');
// IOMAD
require_once($CFG->dirroot . '/local/iomad/lib/company.php');
$companyid = iomad::get_my_companyid(context_system::instance(), false);
if (!empty($companyid)) {
    $postfix = "_$companyid";
} else {
    $postfix = "";
}

$configkey = new lang_string('cfg_opname_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_opname_desc', 'auth_iomadoidc');
$configdefault = new lang_string('pluginname', 'auth_iomadoidc');
$settings->add(new admin_setting_configtext('auth_iomadoidc/opname' . $postfix, $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_clientid_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_clientid_desc', 'auth_iomadoidc');
$settings->add(new admin_setting_configtext('auth_iomadoidc/clientid' . $postfix, $configkey, $configdesc, '', PARAM_TEXT));

$configkey = new lang_string('cfg_clientsecret_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_clientsecret_desc', 'auth_iomadoidc');
$settings->add(new admin_setting_configtext('auth_iomadoidc/clientsecret' . $postfix, $configkey, $configdesc, '', PARAM_TEXT));

$configkey = new lang_string('cfg_authendpoint_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_authendpoint_desc', 'auth_iomadoidc');
$configdefault = 'https://login.microsoftonline.com/common/oauth2/authorize';
$settings->add(new admin_setting_configtext('auth_iomadoidc/authendpoint' . $postfix, $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_tokenendpoint_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_tokenendpoint_desc', 'auth_iomadoidc');
$configdefault = 'https://login.microsoftonline.com/common/oauth2/token';
$settings->add(new admin_setting_configtext('auth_iomadoidc/tokenendpoint' . $postfix, $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_iomadoidcresource_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_iomadoidcresource_desc', 'auth_iomadoidc');
$configdefault = 'https://graph.microsoft.com';
$settings->add(new admin_setting_configtext('auth_iomadoidc/iomadoidcresource' . $postfix, $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_iomadoidcscope_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_iomadoidcscope_desc', 'auth_iomadoidc');
$configdefault = 'openid profile email';
$settings->add(new admin_setting_configtext('auth_iomadoidc/iomadoidcscope' . $postfix, $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_redirecturi_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_redirecturi_desc', 'auth_iomadoidc');
$settings->add(new \auth_iomadoidc\form\adminsetting\redirecturi('auth_iomadoidc/redirecturi' . $postfix, $configkey, $configdesc));

$configkey = new lang_string('cfg_forceredirect_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_forceredirect_desc', 'auth_iomadoidc');
$configdefault = 0;
$settings->add(new admin_setting_configcheckbox('auth_iomadoidc/forceredirect' . $postfix, $configkey, $configdesc, $configdefault));

$configkey = new lang_string('cfg_autoappend_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_autoappend_desc', 'auth_iomadoidc');
$configdefault = '';
$settings->add(new admin_setting_configtext('auth_iomadoidc/autoappend' . $postfix, $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_domainhint_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_domainhint_desc', 'auth_iomadoidc');
$configdefault = '';
$settings->add(new admin_setting_configtext('auth_iomadoidc/domainhint' . $postfix, $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_loginflow_key', 'auth_iomadoidc');
$configdesc = '';
$configdefault = 'authcode';
$settings->add(new \auth_iomadoidc\form\adminsetting\loginflow('auth_iomadoidc/loginflow' . $postfix, $configkey, $configdesc, $configdefault));

$configkey = new lang_string('cfg_userrestrictions_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_userrestrictions_desc', 'auth_iomadoidc');
$configdefault = '';
$settings->add(new admin_setting_configtextarea('auth_iomadoidc/userrestrictions' . $postfix, $configkey, $configdesc, $configdefault, PARAM_TEXT));

$label = new lang_string('cfg_debugmode_key', 'auth_iomadoidc');
$desc = new lang_string('cfg_debugmode_desc', 'auth_iomadoidc');
$settings->add(new \admin_setting_configcheckbox('auth_iomadoidc/debugmode', $label, $desc, '0'));

$configkey = new lang_string('cfg_icon_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_icon_desc', 'auth_iomadoidc');
$configdefault = 'auth_iomadoidc:o365';
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
$settings->add(new \auth_iomadoidc\form\adminsetting\iconselect('auth_iomadoidc/icon' . $postfix, $configkey, $configdesc, $configdefault, $icons));

$configkey = new lang_string('cfg_customicon_key', 'auth_iomadoidc');
$configdesc = new lang_string('cfg_customicon_desc', 'auth_iomadoidc');
$setting = new admin_setting_configstoredfile('auth_iomadoidc/customicon' . $postfix, $configkey, $configdesc, 'customicon');
$setting->set_updatedcallback('auth_iomadoidc_initialize_customicon');
$settings->add($setting);

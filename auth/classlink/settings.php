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
 * @package auth_classlink
 * @author Gopal Sharma <gopalsharma66@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2020 Gopal Sharma <gopalsharma66@gmail.com>
 */
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__.'/lib.php');

$configkey = new lang_string('cfg_scope_key', 'auth_classlink');
$configdesc = new lang_string('cfg_scope_desc', 'auth_classlink');
$configdefault = "openid profile";
$settings->add(new admin_setting_configtext('auth_classlink/scope', $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_opname_key', 'auth_classlink');
$configdesc = new lang_string('cfg_opname_desc', 'auth_classlink');
$configdefault = new lang_string('pluginname', 'auth_classlink');
$settings->add(new admin_setting_configtext('auth_classlink/opname', $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_clientid_key', 'auth_classlink');
$configdesc = new lang_string('cfg_clientid_desc', 'auth_classlink');
$settings->add(new admin_setting_configtext('auth_classlink/clientid', $configkey, $configdesc, '', PARAM_TEXT));

$configkey = new lang_string('cfg_clientsecret_key', 'auth_classlink');
$configdesc = new lang_string('cfg_clientsecret_desc', 'auth_classlink');
$settings->add(new admin_setting_configtext('auth_classlink/clientsecret', $configkey, $configdesc, '', PARAM_TEXT));

$configkey = new lang_string('cfg_authendpoint_key', 'auth_classlink');
$configdesc = new lang_string('cfg_authendpoint_desc', 'auth_classlink');
$configdefault = 'https://launchpad.classlink.com/oauth2/v2/auth';
$settings->add(new admin_setting_configtext('auth_classlink/authendpoint', $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_tokenendpoint_key', 'auth_classlink');
$configdesc = new lang_string('cfg_tokenendpoint_desc', 'auth_classlink');
$configdefault = 'https://launchpad.classlink.com/oauth2/v2/token';
$settings->add(new admin_setting_configtext('auth_classlink/tokenendpoint', $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_classlinkresource_key', 'auth_classlink');
$configdesc = new lang_string('cfg_classlinkresource_desc', 'auth_classlink');
$configdefault = 'https://graph.windows.net';
$settings->add(new admin_setting_configtext('auth_classlink/classlinkresource', $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_redirecturi_key', 'auth_classlink');
$configdesc = new lang_string('cfg_redirecturi_desc', 'auth_classlink');
$settings->add(new \auth_classlink\form\adminsetting\redirecturi('auth_classlink/redirecturi', $configkey, $configdesc));

$configkey = new lang_string('cfg_autoappend_key', 'auth_classlink');
$configdesc = new lang_string('cfg_autoappend_desc', 'auth_classlink');
$configdefault = '';
$settings->add(new admin_setting_configtext('auth_classlink/autoappend', $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_domainhint_key', 'auth_classlink');
$configdesc = new lang_string('cfg_domainhint_desc', 'auth_classlink');
$configdefault = '';
$settings->add(new admin_setting_configtext('auth_classlink/domainhint', $configkey, $configdesc, $configdefault, PARAM_TEXT));

$configkey = new lang_string('cfg_loginflow_key', 'auth_classlink');
$configdesc = '';
$configdefault = 'authcode';
$settings->add(new \auth_classlink\form\adminsetting\loginflow('auth_classlink/loginflow', $configkey, $configdesc, $configdefault));

$configkey = new lang_string('cfg_userrestrictions_key', 'auth_classlink');
$configdesc = new lang_string('cfg_userrestrictions_desc', 'auth_classlink');
$configdefault = '';
$settings->add(new admin_setting_configtextarea('auth_classlink/userrestrictions', $configkey, $configdesc, $configdefault, PARAM_TEXT));

$label = new lang_string('cfg_debugmode_key', 'auth_classlink');
$desc = new lang_string('cfg_debugmode_desc', 'auth_classlink');
$settings->add(new \admin_setting_configcheckbox('auth_classlink/debugmode', $label, $desc, '0'));

$configkey = new lang_string('cfg_icon_key', 'auth_classlink');
$configdesc = new lang_string('cfg_icon_desc', 'auth_classlink');
$configdefault = 'auth_classlink:classlink';
$icons = [
    [
        'pix' => 'classlink',
        'alt' => new lang_string('cfg_iconalt_classlink', 'auth_classlink'),
        'component' => 'auth_classlink',
    ],
];
$settings->add(new \auth_classlink\form\adminsetting\iconselect('auth_classlink/icon', $configkey, $configdesc, $configdefault, $icons));

$configkey = new lang_string('cfg_customicon_key', 'auth_classlink');
$configdesc = new lang_string('cfg_customicon_desc', 'auth_classlink');
$setting = new admin_setting_configstoredfile('auth_classlink/customicon', $configkey, $configdesc, 'customicon');
$setting->set_updatedcallback('auth_classlink_initialize_customicon');
$settings->add($setting);

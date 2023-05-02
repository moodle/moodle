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
 * OIDC application configuration page.
 *
 * @package auth_oidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2022 onwards Microsoft, Inc. (http://microsoft.com/)
 */

use auth_oidc\form\application;

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/auth/oidc/lib.php');

require_login();

$url = new moodle_url('/auth/oidc/manageapplication.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_heading(get_string('settings_page_application', 'auth_oidc'));
$PAGE->set_title(get_string('settings_page_application', 'auth_oidc'));

$jsparams = [AUTH_OIDC_IDP_TYPE_MICROSOFT, AUTH_OIDC_AUTH_METHOD_SECRET, AUTH_OIDC_AUTH_METHOD_CERTIFICATE,
    get_string('auth_method_certificate', 'auth_oidc')];
$jsmodule = [
    'name' => 'auth_oidc',
    'fullpath' => '/auth/oidc/js/module.js',
];
$PAGE->requires->js_init_call('M.auth_oidc.init', $jsparams, true, $jsmodule);

admin_externalpage_setup('auth_oidc_application');

require_admin();

$oidcconfig = get_config('auth_oidc');

$form = new application(null, ['oidcconfig' => $oidcconfig]);

$formdata = [];
foreach (['idptype', 'clientid', 'clientauthmethod', 'clientsecret', 'clientprivatekey', 'clientcert',
    'authendpoint', 'tokenendpoint', 'oidcresource', 'oidcscope'] as $field) {
    if (isset($oidcconfig->$field)) {
        $formdata[$field] = $oidcconfig->$field;
    }
}

$form->set_data($formdata);

if ($form->is_cancelled()) {
    redirect($url);
} else if ($fromform = $form->get_data()) {
    // Handle odd cases where clientauthmethod is not received.
    if (!isset($fromform->clientauthmethod)) {
        $fromform->clientauthmethod = optional_param('clientauthmethod', AUTH_OIDC_AUTH_METHOD_SECRET, PARAM_INT);
    }

    // Prepare config settings to save.
    $configstosave = ['idptype', 'clientid', 'clientauthmethod', 'authendpoint', 'tokenendpoint',
        'oidcresource', 'oidcscope'];

    // Depending on the value of clientauthmethod, save clientsecret or (clientprivatekey and clientcert).
    switch ($fromform->clientauthmethod) {
        case AUTH_OIDC_AUTH_METHOD_SECRET:
            $configstosave[] = 'clientsecret';
            break;
        case AUTH_OIDC_AUTH_METHOD_CERTIFICATE:
            $configstosave[] = 'clientprivatekey';
            $configstosave[] = 'clientcert';
            break;
    }

    // Save config settings.
    foreach ($configstosave as $config) {
        $existingsetting = get_config('auth_oidc', $config);
        if ($fromform->$config != $existingsetting) {
            set_config($config, $fromform->$config, 'auth_oidc');
            add_to_config_log($config, $existingsetting, $fromform->$config, 'auth_oidc');
        }
    }

    // Redirect message depend on IdP type.
    $showprovideadminconsentnotification = false;

    if ($fromform->idptype != AUTH_OIDC_IDP_TYPE_OTHER) {
        if (auth_oidc_is_local_365_installed()) {
            require_once($CFG->dirroot . '/local/o365/classes/utils.php');
            if (method_exists('\local_o365\utils', 'is_connected')) {
                if (\local_o365\utils::is_connected()) {
                    $showprovideadminconsentnotification = true;
                }
            }
        }
    }

    if ($showprovideadminconsentnotification) {
        $localo365configurl = new moodle_url('/admin/settings.php', ['section' => 'local_o365']);
        redirect($url, get_string('application_updated_azure', 'auth_oidc', $localo365configurl->out()));
    } else {
        redirect($url, get_string('application_updated', 'auth_oidc'));
    }
}

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();


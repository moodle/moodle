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

$jsparams = [AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM, AUTH_OIDC_AUTH_METHOD_SECRET, AUTH_OIDC_AUTH_METHOD_CERTIFICATE,
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
    'clientcertsource', 'clientprivatekeyfile', 'clientcertfile', 'clientcertpassphrase',
    'authendpoint', 'tokenendpoint', 'oidcresource', 'oidcscope', 'secretexpiryrecipients',
    'bindingusernameclaim', 'customclaimname'] as $field) {
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
            $configstosave[] = 'secretexpiryrecipients';
            break;
        case AUTH_OIDC_AUTH_METHOD_CERTIFICATE:
            $configstosave[] = 'clientcertsource';
            $configstosave[] = 'clientcertpassphrase';
            switch ($fromform->clientcertsource) {
                case AUTH_OIDC_AUTH_CERT_SOURCE_TEXT:
                    $configstosave[] = 'clientprivatekey';
                    $configstosave[] = 'clientcert';
                    break;
                case AUTH_OIDC_AUTH_CERT_SOURCE_FILE:
                    $configstosave[] = 'clientprivatekeyfile';
                    $configstosave[] = 'clientcertfile';
                    break;
            }
            break;
    }

    // Save config settings.
    $updateapplicationtokenrequired = false;
    $settingschanged = false;
    foreach ($configstosave as $config) {
        $existingsetting = get_config('auth_oidc', $config);
        if ($fromform->$config != $existingsetting) {
            add_to_config_log($config, $existingsetting, $fromform->$config, 'auth_oidc');
            set_config($config, $fromform->$config, 'auth_oidc');
            $settingschanged = true;
            if ($config != 'secretexpiryrecipients') {
                $updateapplicationtokenrequired = true;
            }
        }
    }

    // Redirect destination and message depend on IdP type.
    $isgraphapiconnected = false;
    if ($fromform->idptype != AUTH_OIDC_IDP_TYPE_OTHER) {
        if (auth_oidc_is_local_365_installed()) {
            $isgraphapiconnected = true;
        }
    }

    if ($updateapplicationtokenrequired) {
        if ($isgraphapiconnected) {
            // First, delete the existing application token and purge cache.
            unset_config('apptokens', 'local_o365');
            unset_config('azuresetupresult', 'local_o365');
            purge_all_caches();

            // Then show the message to the user with instructions to update the application token.
            $localo365configurl = new moodle_url('/admin/settings.php', ['section' => 'local_o365']);
            redirect($localo365configurl, get_string('application_updated_microsoft', 'auth_oidc'));
        } else {
            redirect($url, get_string('application_updated', 'auth_oidc'));
        }
    } else if ($settingschanged) {
        redirect($url, get_string('application_updated', 'auth_oidc'));
    } else {
        redirect($url, get_string('application_not_changed', 'auth_oidc'));
    }
}

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();

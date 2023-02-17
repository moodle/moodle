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
 * IOMADoIDC application configuration page.
 *
 * @package auth_iomadoidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2022 onwards Microsoft, Inc. (http://microsoft.com/)
 */

use auth_iomadoidc\form\application;

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/auth/iomadoidc/lib.php');

// IOMAD
require_once($CFG->dirroot . '/local/iomad/lib/company.php');
$companyid = iomad::get_my_companyid(context_system::instance(), false);
if (!empty($companyid)) {
    $postfix = "_$companyid";
} else {
    $postfix = "";
}

require_login();

$url = new moodle_url('/auth/iomadoidc/manageapplication.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_heading(get_string('settings_page_application', 'auth_iomadoidc'));
$PAGE->set_title(get_string('settings_page_application', 'auth_iomadoidc'));

$jsparams = [AUTH_IOMADoIDC_IDP_TYPE_MICROSOFT, AUTH_IOMADoIDC_AUTH_METHOD_SECRET, AUTH_IOMADoIDC_AUTH_METHOD_CERTIFICATE,
    get_string('auth_method_certificate', 'auth_iomadoidc')];
$jsmodule = [
    'name' => 'auth_iomadoidc',
    'fullpath' => '/auth/iomadoidc/js/module.js',
];
$PAGE->requires->js_init_call('M.auth_iomadoidc.init', $jsparams, true, $jsmodule);

admin_externalpage_setup('auth_iomadoidc_application');

require_admin();

$iomadoidcconfig = get_config('auth_iomadoidc' . $postfix);

$form = new application(null, ['iomadoidcconfig' => $iomadoidcconfig]);

$formdata = [];
foreach (['idptype', 'clientid', 'clientauthmethod', 'clientsecret', 'clientprivatekey', 'clientcert', 'tenantnameorguid',
    'authendpoint', 'tokenendpoint', 'iomadoidcresource', 'iomadoidcscope'] as $field) {
    if (!empty($iomadoidcconfig->$field . $postfix)) {
        $formdata[$field] = $iomadoidcconfig->$field;
    }
}

$form->set_data($formdata);

if ($form->is_cancelled()) {
    redirect($url);
} else if ($fromform = $form->get_data()) {
    // Handle odd cases where clientauthmethod is not received.
    if (!isset($fromform->clientauthmethod)) {
        $fromform->clientauthmethod = optional_param('clientauthmethod', AUTH_IOMADoIDC_AUTH_METHOD_SECRET, PARAM_INT);
    }

    // Prepare config settings to save.
    $configstosave = ['idptype', 'clientid', 'tenantnameorguid', 'clientauthmethod', 'authendpoint', 'tokenendpoint',
        'iomadoidcresource', 'iomadoidcscope'];

    // Depending on the value of clientauthmethod, save clientsecret or (clientprivatekey and clientcert).
    switch ($fromform->clientauthmethod) {
        case AUTH_IOMADoIDC_AUTH_METHOD_SECRET:
            $configstosave[] = 'clientsecret';
            break;
        case AUTH_IOMADoIDC_AUTH_METHOD_CERTIFICATE:
            $configstosave[] = 'clientprivatekey';
            $configstosave[] = 'clientcert';
            break;
    }

    // Save config settings.
    foreach ($configstosave as $config) {
        $existingsetting = get_config('auth_iomadoidc', $config . $postfix);
        if ($fromform->$config != $existingsetting) {
            set_config($config . $postfix, $fromform->$config, 'auth_iomadoidc');
            add_to_config_log($config . $postfix, $existingsetting, $fromform->$config, 'auth_iomadoidc');
        }
    }

    // Redirect message depend on IdP type.
    if ($fromform->idptype == AUTH_IOMADoIDC_IDP_TYPE_OTHER) {
        redirect($url, get_string('application_updated', 'auth_iomadoidc'));
    } else {
        $localo365configurl = new moodle_url('/admin/settings.php', ['section' => 'local_o365']);
        redirect($url, get_string('application_updated_azure', 'auth_iomadoidc', $localo365configurl->out()));
    }
}

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();


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
 * Manage binding username claim page.
 *
 * @package auth_oidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2023 onwards Microsoft, Inc. (http://microsoft.com/)
 */

use auth_oidc\form\binding_username_claim;

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/auth/oidc/lib.php');

require_login();

$url = new moodle_url('/auth/oidc/binding_username_claim.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_heading(get_string('settings_page_binding_username_claim', 'auth_oidc'));
$PAGE->set_title(get_string('settings_page_binding_username_claim', 'auth_oidc'));

admin_externalpage_setup('auth_oidc_binding_username_claim');

require_admin();

$form = new binding_username_claim(null);
$formdata = [];

// Validate auth_oidc_binding_username_claim settings.
$predefinedbindingclaims = ['auto', 'preferred_username', 'email', 'upn', 'unique_name', 'sub', 'oid', 'samaccountname'];

$oidcconfig = get_config('auth_oidc');
if (!isset($oidcconfig->bindingusernameclaim)) {
    // Bindingusernameclaim is not set, set default value.
    $formdata['bindingusernameclaim'] = 'auto';
    $formdata['customclaimname'] = '';
    set_config('bindingusernameclaim', 'auto', 'auth_oidc');
} else if (!$oidcconfig->bindingusernameclaim) {
    $formdata['bindingusernameclaim'] = 'auto';
    $formdata['customclaimname'] = '';
} else if (in_array($oidcconfig->bindingusernameclaim, $predefinedbindingclaims)) {
    $formdata['bindingusernameclaim'] = $oidcconfig->bindingusernameclaim;
    $formdata['customclaimname'] = '';
} else {
    $formdata['bindingusernameclaim'] = 'custom';
    $formdata['customclaimname'] = $oidcconfig->bindingusernameclaim;
}

$form->set_data($formdata);

if ($form->is_cancelled()) {
    redirect($url);
} else if ($fromform = $form->get_data()) {
    $configstosave = ['bindingusernameclaim', 'customclaimname'];

    $configchanged = false;

    foreach ($configstosave as $config) {
        if (isset($fromform->$config)) {
            $existingsetting = $oidcconfig->$config;
            if ($fromform->$config != $existingsetting) {
                $configchanged = true;
                set_config($config, $fromform->$config, 'auth_oidc');
                add_to_config_log($config, $existingsetting, $fromform->$config, 'auth_oidc');
            }
        }
    }

    if ($configchanged) {
        redirect($url, get_string('binding_username_claim_updated', 'auth_oidc'));
    } else {
        redirect($url);
    }
}

$existingclaims = auth_oidc_get_existing_claims();

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('binding_username_claim_heading', 'auth_oidc'));
$bindingusernametoolurl = new moodle_url('/auth/oidc/change_binding_username_claim_tool.php');
echo html_writer::tag('p', get_string('binding_username_claim_description', 'auth_oidc', $bindingusernametoolurl->out()));
if ($existingclaims) {
    echo html_writer::tag('p', get_string('binding_username_claim_description_existing_claims', 'auth_oidc',
        implode(' / ', $existingclaims)));
}

$form->display();

echo $OUTPUT->footer();

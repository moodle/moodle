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
 * This file allows for testing of login via configured oauth2 IDP poviders.
 *
 * @package auth_oauth2
 * @copyright 2021 Matt Porritt <mattp@catalyst-au.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

// Require_login is not needed here.
// phpcs:disable moodle.Files.RequireLogin.Missing
require_once('../../config.php');

require_sesskey();

$issuerid = required_param('id', PARAM_INT);
$url = new moodle_url('/auth/oauth2/test.php', ['id' => $issuerid, 'sesskey' => sesskey()]);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

if (!\auth_oauth2\api::is_enabled()) {
    throw new \moodle_exception('notenabled', 'auth_oauth2');
}

$issuer = new \core\oauth2\issuer($issuerid);
if (!$issuer->is_available_for_login()) {
    throw new \moodle_exception('issuernologin', 'auth_oauth2');
}

$client = \core\oauth2\api::get_user_oauth_client($issuer, $url);

if ($client) {
    // We have a valid client, now lets see if we can log into the IDP.
    if (!$client->is_logged_in()) {
        redirect($client->get_login_url());
    }

    echo $OUTPUT->header();

    // We were successful logging into the IDP.
    echo $OUTPUT->notification(get_string('loggedin', 'auth_oauth2'), 'notifysuccess');

    // Try getting user info from the IDP.
    $endpointurl = $client->get_issuer()->get_endpoint_url('userinfo');
    $response = $client->get($endpointurl);
    $userinfo = json_decode($response, true);

    $templateinfo = [];
    foreach ($userinfo as $key => $value) {
        // We are just displaying the data from the IdP for testing purposes,
        // so we are more interested in displaying it to the admin than
        // processing it.
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $templateinfo[] = ['name' => $key, 'value' => $value];
    }

    // Display user info.
    if (!empty($templateinfo)) {
        echo $OUTPUT->render_from_template('auth_oauth2/idpresponse', ['pairs' => $templateinfo]);
    }

} else {
    throw new moodle_exception('Could not get an OAuth client.');
}

echo $OUTPUT->footer();

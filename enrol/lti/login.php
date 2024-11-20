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
 * LTI 1.3 login endpoint.
 *
 * See: http://www.imsglobal.org/spec/security/v1p0/#step-1-third-party-initiated-login
 *
 * This must support both POST and GET methods, as per the spec.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use auth_lti\local\ltiadvantage\utility\cookie_helper;
use enrol_lti\local\ltiadvantage\lib\lti_cookie;
use enrol_lti\local\ltiadvantage\lib\issuer_database;
use enrol_lti\local\ltiadvantage\lib\launch_cache_session;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use Packback\Lti1p3\LtiOidcLogin;

require_once(__DIR__."/../../config.php");

// Required fields for OIDC 3rd party initiated login.
// See http://www.imsglobal.org/spec/security/v1p0/#step-1-third-party-initiated-login.
// Validate these here, despite further validation in the LTI 1.3 library.
$iss = required_param('iss', PARAM_URL); // Issuer URI of the calling platform.
$loginhint = required_param('login_hint', PARAM_RAW); // Platform ID for the person to login.
$targetlinkuri = required_param('target_link_uri', PARAM_URL); // The took launch URL.

// Optional lti_message_hint. See https://www.imsglobal.org/spec/lti/v1p3#additional-login-parameters-0.
// If found, this must be returned unmodified to the platform.
$ltimessagehint = optional_param('lti_message_hint', null, PARAM_RAW);

// The target_link_uri param should contain the endpoint that will be executed at the end of the OIDC login process.
// In Moodle, this will either be:
// - enrol/lti/launch.php endpoint (for regular resource link launches) or
// - enrol/lti/launch_deeplink.php endpoint (for deep linking launches)
// Thus, the target_link_uri signifies intent to perform a certain launch type. It can be used to generate the
// redirect_uri param for the auth request but must first be verified, as it is unsigned data at this stage.
// See here: https://www.imsglobal.org/spec/lti/v1p3/impl#verify-the-target_link_uri.
//
// Also note that final redirection to the resource (after the login process is complete) should rely on the
// https://purl.imsglobal.org/spec/lti/claim/target_link_uri claim instead of the target_link_uri value provided here.
// See here: http://www.imsglobal.org/spec/lti/v1p3/#target-link-uri.
$validuris = [
    (new moodle_url('/enrol/lti/launch.php'))->out(false), // Resource link launches.
    (new moodle_url('/enrol/lti/launch_deeplink.php'))->out(false) // Deep linking launches.
];

// This code verifies the target_link_uri. Only two values are permitted (see endpoints listed above).
if (!in_array($targetlinkuri, $validuris)) {
    $msg = 'The target_link_uri param must match one of the redirect URIs set during tool registration.';
    throw new coding_exception($msg);
}

// Because client_id is optional, this endpoint receives a param 'id', a unique id generated when creating the registration.
// A registration can thus be located by either the tuple {iss, client_id} (if client_id is provided), or by the tuple {iss, id},
// (if client_id is not provided). See https://www.imsglobal.org/spec/lti/v1p3/#client_id-login-parameter.
global $_REQUEST;
if (empty($_REQUEST['client_id']) && !empty($_REQUEST['id'])) {
    $_REQUEST['client_id'] = $_REQUEST['id'];
}

// Before beginning the OIDC authentication, ensure the MoodleSession cookie can be used. Browser-specific steps may need to be
// taken to set cookies in 3rd party contexts. Skip the check if the user is already auth'd. This means that either cookies aren't
// an issue in the current browser/launch context.
if (!isloggedin()) {
    cookie_helper::do_cookie_check(new moodle_url('/enrol/lti/login.php', [
        'iss' => $iss,
        'login_hint' => $loginhint,
        'target_link_uri' => $targetlinkuri,
        'lti_message_hint' => $ltimessagehint,
        'client_id' => $_REQUEST['client_id'],
    ]));
    if (!cookie_helper::cookies_supported()) {
        global $OUTPUT, $PAGE;
        $PAGE->set_context(context_system::instance());
        $PAGE->set_url(new moodle_url('/enrol/lti/login.php'));
        $PAGE->set_pagelayout('popup');
        echo $OUTPUT->header();
        $renderer = $PAGE->get_renderer('enrol_lti');
        echo $renderer->render_cookies_required_notice();
        echo $OUTPUT->footer();
        die();
    }
}

// Now, do the OIDC login.
$redirecturl = LtiOidcLogin::new(
    new issuer_database(new application_registration_repository(), new deployment_repository()),
    new launch_cache_session(),
    new lti_cookie()
)->getRedirectUrl($targetlinkuri, $_REQUEST);

redirect($redirecturl);

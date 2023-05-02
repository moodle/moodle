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
 * Return token
 *
 * @package    local_o365
 * @copyright  2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('REQUIRE_CORRECT_ACCESS', true);
define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/lib/authlib.php');
require_once($CFG->libdir . '/externallib.php');
require_once('lib.php');

$username = required_param('username', PARAM_USERNAME);
$serviceshortname = required_param('service', PARAM_TEXT);

$headers = apache_request_headers();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    throw new moodle_exception('invalidlogin');
}
local_o365_check_sharedsecret();
$authtoken = substr($headers['Authorization'], 7);
[$headerencoded, $payloadencoded, $signatureencoded] = explode('.', $authtoken);
$dataencoded = "$headerencoded.$payloadencoded";
$signature = local_o365_base64urldecode($signatureencoded);
$secret = get_config('local_o365', 'bot_sharedsecret');
$rawsignature = hash_hmac('sha256', $dataencoded, $secret, true);
if (!hash_equals($rawsignature, $signature)) {
    http_response_code(401);
    throw new moodle_exception('invalidlogin');
}

$payload = json_decode(local_o365_base64urldecode($payloadencoded));

$headr = [];
$headr[] = 'Content-length: 0';
$headr[] = 'Content-type: application/json';
$headr[] = 'Authorization: Bearer ' . $payload->token;
$curl = curl_init();
curl_setopt_array($curl,
    [CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => "https://graph.microsoft.com/v1.0/me/", CURLOPT_HTTPHEADER => $headr]);
$data = json_decode(curl_exec($curl));
curl_close($curl);

if (strtolower($data->mail) !== strtolower($username)) {
    http_response_code(401);
    throw new moodle_exception('invalidlogin');
}

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');

echo $OUTPUT->header();

if (!$CFG->enablewebservices) {
    http_response_code(503);
    throw new moodle_exception('enablewsdescription', 'webservice');
}

$systemcontext = context_system::instance();

$user = $DB->get_record('user', ['username' => $username, 'auth' => 'oidc']);
if (empty($user)) {
    http_response_code(401);
    throw new moodle_exception('invalidlogin');
}

login_attempt_valid($user);

if (!empty($user)) {
    // Cannot authenticate unless maintenance access is granted.
    $hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', $systemcontext, $user);
    if (!empty($CFG->maintenance_enabled) and !$hasmaintenanceaccess) {
        http_response_code(503);
        throw new moodle_exception('sitemaintenance', 'admin');
    }

    if (isguestuser($user)) {
        http_response_code(401);
        throw new moodle_exception('noguest');
    }
    if (empty($user->confirmed)) {
        http_response_code(401);
        throw new moodle_exception('usernotconfirmed', 'moodle', '', $user->username);
    }
    // Check credential expiry.
    $userauth = get_auth_plugin($user->auth);
    if (!empty($userauth->config->expiration) and $userauth->config->expiration == 1) {
        $days2expire = $userauth->password_expire($user->username);
        if (intval($days2expire) < 0) {
            http_response_code(401);
            throw new moodle_exception('passwordisexpired', 'webservice');
        }
    }

    // Let enrol plugins deal with new enrolments if necessary.
    enrol_check_plugins($user);

    // Setup user session to check capability.
    \core\session\manager::set_user($user);

    // Check if the service exists and is enabled.
    $service = $DB->get_record('external_services', ['shortname' => $serviceshortname, 'enabled' => 1]);
    if (empty($service)) {
        http_response_code(503);
        throw new moodle_exception('servicenotavailable', 'webservice');
    }

    // Get an existing token or create a new one.
    $token = external_generate_token_for_current_user($service);
    $privatetoken = $token->privatetoken;
    external_log_token_request($token);

    $siteadmin = has_capability('moodle/site:config', $systemcontext, $USER->id);

    $usertoken = new stdClass;
    $usertoken->token = $token->token;
    // Private token, only transmitted to https sites and non-admin users.
    if (is_https() and !$siteadmin) {
        $usertoken->privatetoken = $privatetoken;
    } else {
        $usertoken->privatetoken = null;
    }
    echo json_encode($usertoken);
} else {
    throw new moodle_exception('invalidlogin');
}

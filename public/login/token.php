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
 * @package    moodlecore
 * @copyright  2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('REQUIRE_CORRECT_ACCESS', true);
define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../config.php');

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');

if (!$CFG->enablewebservices) {
    throw new moodle_exception('enablewsdescription', 'webservice');
}

// This script is used by the mobile app to check that the site is available and web services
// are allowed. In this mode, no further action is needed.
if (optional_param('appsitecheck', 0, PARAM_INT)) {
    echo json_encode((object)['appsitecheck' => 'ok']);
    exit;
}

$username = required_param('username', PARAM_USERNAME);
$password = required_param('password', PARAM_RAW);
$serviceshortname = required_param('service', PARAM_ALPHANUMEXT);

echo $OUTPUT->header();

$username = trim(core_text::strtolower($username));
if (\core\di::get(\core\authentication::class)->is_restored_user($username)) {
    throw new moodle_exception('restoredaccountresetpassword', 'webservice');
}

$systemcontext = context_system::instance();

$reason = null;
$user = authenticate_user_login($username, $password, false, $reason, false);
if (!empty($user)) {
    $uservalidator = \core\di::get(\core_auth\validate_user::class);

    try {
        $uservalidator->validate_before_token_login($user);
    } catch (\core_auth\exception\maintenance_mode_enabled_exception $e) {
        throw new moodle_exception('sitemaintenance', 'admin', previous: $e);
    } catch (\core_auth\exception\user_not_confirmed_exception $e) {
        throw new moodle_exception('usernotconfirmed', 'moodle', '', $user->username, previous: $e);
    } catch (\core_auth\exception\user_is_guest_exception $e) {
        throw new moodle_exception('noguest', previous: $e);
    } catch (\core_auth\exception\credentials_expired_exception $e) {
        throw new moodle_exception('passwordisexpired', 'webservice', previous: $e);
    }

    // Let enrol plugins deal with new enrolments if necessary.
    enrol_check_plugins($user);

    // Setup user session to check capability.
    \core\session\manager::set_user($user);

    // Check if the service exists and is enabled.
    $service = $DB->get_record('external_services', ['shortname' => $serviceshortname, 'enabled' => 1]);
    if (empty($service)) {
        // Will throw exception if no token found.
        throw new moodle_exception('servicenotavailable', 'webservice');
    }

    // Get an existing token or create a new one.
    $token = \core_external\util::generate_token_for_current_user($service);
    $privatetoken = $token->privatetoken;
    \core_external\util::log_token_request($token);

    $siteadmin = has_capability('moodle/site:config', $systemcontext, $USER->id);

    $usertoken = (object) [
        'token' => $token->token,
    ];

    // Private token, only transmitted to https sites and non-admin users.
    if (is_https() && !$siteadmin) {
        $usertoken->privatetoken = $privatetoken;
    } else {
        $usertoken->privatetoken = null;
    }
    echo json_encode($usertoken);
} else {
    throw new moodle_exception('invalidlogin');
}

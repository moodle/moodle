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
require_once($CFG->libdir . '/externallib.php');

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');

$username = required_param('username', PARAM_USERNAME);
$password = required_param('password', PARAM_RAW);
$serviceshortname  = required_param('service',  PARAM_ALPHANUMEXT);

echo $OUTPUT->header();

if (!$CFG->enablewebservices) {
    throw new moodle_exception('enablewsdescription', 'webservice');
}
$username = trim(core_text::strtolower($username));
if (is_restored_user($username)) {
    throw new moodle_exception('restoredaccountresetpassword', 'webservice');
}
$user = authenticate_user_login($username, $password);
if (!empty($user)) {

    // Cannot authenticate unless maintenance access is granted.
    $hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', context_system::instance(), $user);
    if (!empty($CFG->maintenance_enabled) and !$hasmaintenanceaccess) {
        throw new moodle_exception('sitemaintenance', 'admin');
    }

    if (isguestuser($user)) {
        throw new moodle_exception('noguest');
    }
    if (empty($user->confirmed)) {
        throw new moodle_exception('usernotconfirmed', 'moodle', '', $user->username);
    }
    // check credential expiry
    $userauth = get_auth_plugin($user->auth);
    if (!empty($userauth->config->expiration) and $userauth->config->expiration == 1) {
        $days2expire = $userauth->password_expire($user->username);
        if (intval($days2expire) < 0 ) {
            throw new moodle_exception('passwordisexpired', 'webservice');
        }
    }

    // Check whether the user should be changing password.
    if (get_user_preferences('auth_forcepasswordchange', false, $user)) {
        if ($userauth->can_change_password()) {
            throw new moodle_exception('forcepasswordchangenotice');
        } else {
            throw new moodle_exception('nopasswordchangeforced', 'auth');
        }
    }

    // let enrol plugins deal with new enrolments if necessary
    enrol_check_plugins($user);

    // setup user session to check capability
    \core\session\manager::set_user($user);

    //check if the service exists and is enabled
    $service = $DB->get_record('external_services', array('shortname' => $serviceshortname, 'enabled' => 1));
    if (empty($service)) {
        // will throw exception if no token found
        throw new moodle_exception('servicenotavailable', 'webservice');
    }

    // Get an existing token or create a new one.
    $token = external_generate_token_for_current_user($service);

    // log token access
    $DB->set_field('external_tokens', 'lastaccess', time(), array('id'=>$token->id));

    $params = array(
        'objectid' => $token->id,
    );
    $event = \core\event\webservice_token_sent::create($params);
    $event->add_record_snapshot('external_tokens', $token);
    $event->trigger();

    $usertoken = new stdClass;
    $usertoken->token = $token->token;
    echo json_encode($usertoken);
} else {
    throw new moodle_exception('invalidlogin');
}

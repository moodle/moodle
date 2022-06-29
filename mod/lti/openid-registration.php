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
 * This file receives a registration request along with the registration token and returns a client_id.
 *
 * @copyright  2020 Claude Vervoort (Cengage), Carlos Costa, Adrian Hutchinson (Macgraw Hill)
 * @package    mod_lti
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true);

use mod_lti\local\ltiopenid\registration_helper;
use mod_lti\local\ltiopenid\registration_exception;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');

$code = 200;
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' or ($_SERVER['REQUEST_METHOD'] === 'GET')) {
    $doregister = $_SERVER['REQUEST_METHOD'] === 'POST';
    // Retrieve registration token from Bearer Authorization header.
    $authheader = moodle\mod\lti\OAuthUtil::get_headers()['Authorization'] ?? '';
    if (!($authheader && substr($authheader, 0, 7) == 'Bearer ')) {
        $message = 'missing_registration_token';
        $code = 401;
    } else {

        // Registers tool.
        try {
            $tokenres = registration_helper::get()->validate_registration_token(trim(substr($authheader, 7)));
            $type = new stdClass();
            $type->state = LTI_TOOL_STATE_PENDING;
            if (array_key_exists('type', $tokenres)) {
                $type = $tokenres['type'];
            }
            if ($doregister) {
                $registrationpayload = json_decode(file_get_contents('php://input'), true);
                $config = registration_helper::get()->registration_to_config($registrationpayload, $tokenres['clientid']);
                if ($type->id) {
                    lti_update_type($type, clone $config);
                    $typeid = $type->id;
                } else {
                    $typeid = lti_add_type($type, clone $config);
                }
                header('Content-Type: application/json; charset=utf-8');
                $message = json_encode(registration_helper::get()->config_to_registration((object)$config, $typeid));
            } else if ($type) {
                $config = lti_get_type_config($type->id);
                header('Content-Type: application/json; charset=utf-8');
                $message = json_encode(registration_helper::get()->config_to_registration((object)$config, $type->id, $type));
            } else {
                $code = 404;
                $message = "No registration found.";
            }
        } catch (registration_exception $e) {
            $code = $e->getCode();
            $message = $e->getMessage();
        }
    }
} else {
    $code = 400;
    $message = 'Unsupported operation';
}
$response = new \mod_lti\local\ltiservice\response();
// Set code.
$response->set_code($code);
// Set body.
$response->set_body($message);
$response->send();

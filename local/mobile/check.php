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
 * JSON Rest service to check several Moodle Mobile settings:
 * - Web Services enabled
 * - Mobile Services enabled
 * - Site is in maintenance mode
 * - The user has to login in the  site using the browser (instead in the app)
 *
 * @package    local_mobile
 * @copyright  2014 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('REQUIRE_CORRECT_ACCESS', true);
define('NO_MOODLE_COOKIES', true);

require_once(dirname(__FILE__) . '/../../config.php');

$serviceshortname  = required_param('service',  PARAM_ALPHANUMEXT);

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');
echo $OUTPUT->header();

$response = new stdClass();
$response->error = 0;
$response->code  = 0;    // Code is used for both success or failure status.

if (!empty($CFG->maintenance_enabled)) {
    $response->error = 1;
    $response->code  = 1;
    echo json_encode($response);
    die;
}

if (!$CFG->enablewebservices) {
    $response->error = 1;
    $response->code  = 2;
    echo json_encode($response);
    die;
}

// Check if the service exists and is enabled.
if (!$DB->record_exists('external_services', array('shortname' => $serviceshortname , 'enabled' => 1))) {
    $response->error = 1;

    // There is at least one mobile service enabled.
    if ($DB->record_exists('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE , 'enabled' => 1))) {
        $response->code  = 3;
    } else {
        $response->code  = 4;
    }
    echo json_encode($response);
    die;
}

// Normal login using the app.
$response->code  = 1;
// Indicate that core supports the launch.
$response->coresupported = 1;

$typeoflogin = get_config('tool_mobile', 'typeoflogin');
if (!empty($typeoflogin)) {
    $response->code = $typeoflogin;
}

echo json_encode($response);
die;
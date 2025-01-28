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
 * This file is used to call any registered externallib function in Moodle.
 *
 * It will process more than one request and return more than one response if required.
 * It is recommended to add webservice functions and re-use this script instead of
 * writing any new custom ajax scripts.
 *
 * @since Moodle 2.9
 * @package core
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_external\external_api;
use core_external\external_settings;

define('AJAX_SCRIPT', true);
// Services can declare 'readonlysession' in their config located in db/services.php, if not present will default to false.
define('READ_ONLY_SESSION', true);

if (!empty($_GET['nosessionupdate'])) {
    define('NO_SESSION_UPDATE', true);
}

require_once(__DIR__ . '/../../config.php');

define('PREFERRED_RENDERER_TARGET', RENDERER_TARGET_GENERAL);

$arguments = '';
$cacherequest = false;
if (defined('ALLOW_GET_PARAMETERS')) {
    $arguments = optional_param('args', '', PARAM_RAW);
    $cachekey = optional_param('cachekey', '', PARAM_INT);
    if ($cachekey && $cachekey > 0 && $cachekey <= time()) {
        $cacherequest = true;
    }
}

// Either we are not allowing GET parameters or we didn't use GET because
// we did not pass a cache key or the URL was too long.
if (empty($arguments)) {
    $arguments = file_get_contents('php://input');
}

$requests = json_decode($arguments, true);

if ($requests === null) {
    $lasterror = json_last_error_msg();
    throw new coding_exception('Invalid json in request: ' . $lasterror);
}
$responses = [];

// Defines the external settings required for Ajax processing.
$settings = external_settings::get_instance();
$settings->set_file('pluginfile.php');
$settings->set_fileurl(true);
$settings->set_filter(true);
$settings->set_raw(false);

$haserror = false;
foreach ($requests as $request) {
    $response = [];
    $methodname = clean_param($request['methodname'], PARAM_ALPHANUMEXT);
    $index = clean_param($request['index'], PARAM_INT);
    $args = $request['args'];

    $response = external_api::call_external_function($methodname, $args, true);
    $responses[$index] = $response;

    if ($response['error']) {
        $haserror = true;
        if (!NO_MOODLE_COOKIES) {
            // If there was an error, and this HTTP request includes a Moodle cookie (and therefore a login), reject all
            // subsequent changes.
            //
            // The reason for this is that an earlier step may be performing a dependant action. Consider the following:
            // 1) Backup a thing
            // 2) Reset the thing to its initial state
            // 3) Restore the thing from the backup made in step 1.
            //
            // In the above example you do not want steps 2 and 3 to happen if step 1 fails.
            // Do not process the remaining requests.

            // If the request came through service-nologin.php which does not allow any kind of login,
            // then it is not possible to make changes to the DB, session, site, etc.
            // For all other cases, we *MUST* stop processing subsequent requests.
            break;
        }
    }
}

if ($cacherequest && !$haserror) {
    // 90 days only - based on Moodle point release cadence being every 3 months.
    $lifetime = 60 * 60 * 24 * 90;

    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime . ', immutable');
    header('Accept-Ranges: none');
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($responses);

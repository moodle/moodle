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

define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/externallib.php');

require_login(null, true, null, true, true);
require_sesskey();

$rawjson = file_get_contents('php://input');

$requests = json_decode($rawjson, true);
if ($requests === null) {
    $lasterror = json_last_error_msg();
    throw new coding_exception('Invalid json in request: ' . $lasterror);
}
$responses = array();


foreach ($requests as $request) {
    $response = array();
    $methodname = clean_param($request['methodname'], PARAM_ALPHANUMEXT);
    $index = clean_param($request['index'], PARAM_INT);
    $args = $request['args'];

    try {
        $externalfunctioninfo = external_function_info($methodname);

        if (!$externalfunctioninfo->allowed_from_ajax) {
            throw new moodle_exception('servicenotavailable', 'webservice');
        }

        // Validate params, this also sorts the params properly, we need the correct order in the next part.
        $callable = array($externalfunctioninfo->classname, 'validate_parameters');
        $params = call_user_func($callable,
                                 $externalfunctioninfo->parameters_desc,
                                 $args);

        // Execute - gulp!
        $callable = array($externalfunctioninfo->classname, $externalfunctioninfo->methodname);
        $result = call_user_func_array($callable,
                                       array_values($params));

        $response['error'] = false;
        $response['data'] = $result;
        $responses[$index] = $response;
    } catch (Exception $e) {
        $jsonexception = get_exception_info($e);
        unset($jsonexception->a);
        if (!debugging('', DEBUG_DEVELOPER)) {
            unset($jsonexception->debuginfo);
            unset($jsonexception->backtrace);
        }
        $response['error'] = true;
        $response['exception'] = $jsonexception;
        $responses[$index] = $response;
        // Do not process the remaining requests.
        break;
    }
}

echo json_encode($responses);

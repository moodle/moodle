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
 * Entry point for web service via tokens access to draftfile.php.
 *
 * @package    core
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * AJAX_SCRIPT - exception will be converted into JSON.
 */
define('AJAX_SCRIPT', true);

/**
 * NO_MOODLE_COOKIES - we don't want any cookie.
 */
define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/webservice/lib.php');

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');

// Authenticate the user.
$token = required_param('token', PARAM_ALPHANUM);

$webservicelib = new webservice();
$authenticationinfo = $webservicelib->authenticate_user($token);

// Check the service allows file download.
if (empty($authenticationinfo['service']->downloadfiles)) {
    throw new webservice_access_exception('Web service file downloading must be enabled in external service settings.');
}

require_once($CFG->dirroot . '/draftfile.php');

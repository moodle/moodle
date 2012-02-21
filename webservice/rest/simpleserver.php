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
 * REST web service entry point. The authentication is done via username/password.
 *
 * @package    webservice_rest
 * @copyright  2009 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * NO_DEBUG_DISPLAY - disable moodle specific debug messages and any errors in output
 */
define('NO_DEBUG_DISPLAY', true);

/**
 * NO_MOODLE_COOKIES - no cookies with web service
 */
define('NO_MOODLE_COOKIES', true);

require('../../config.php');
require_once("$CFG->dirroot/webservice/rest/locallib.php");

if (!webservice_protocol_is_enabled('rest')) {
    die;
}

$restformat = optional_param('moodlewsrestformat', 'xml', PARAM_ALPHA);
//remove the alt from the request
if (isset($_REQUEST['moodlewsrestformat'])) {
    unset($_REQUEST['moodlewsrestformat']);
}
if (isset($_GET['moodlewsrestformat'])) {
    unset($_GET['moodlewsrestformat']);
}
if (isset($_POST['moodlewsrestformat'])) {
    unset($_POST['moodlewsrestformat']);
}

$server = new webservice_rest_server(WEBSERVICE_AUTHMETHOD_USERNAME);
$server->run();
die;



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
 * This file returns an array of available public keys for the LTI 1.3 tool.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Packback\Lti1p3\JwksEndpoint;

define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../../config.php');

$privatekey = get_config('enrol_lti', 'lti_13_privatekey');
$key = get_config('enrol_lti', 'lti_13_kid');
$keyendpoint = JwksEndpoint::new([$key => $privatekey]);

@header('Content-Type: application/json; charset=utf-8');
$keyendpoint->outputJwks();

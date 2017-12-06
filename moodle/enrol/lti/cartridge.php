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
 * Generates an XML IMS Cartridge with the details for the given tool
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/lib/weblib.php');

$toolid = null;
$token = null;

$filearguments = get_file_argument();
$arguments = explode('/', trim($filearguments, '/'));
if (count($arguments) >= 2) { // Can put cartridge.xml at the end, or anything really.
    list($toolid, $token) = $arguments;
}

$toolid = optional_param('id', $toolid, PARAM_INT);
$token = optional_param('token', $token, PARAM_ALPHANUM);

// Only show the cartridge if the token parameter is correct.
// If we do not compare with a shared secret, someone could very easily
// guess an id for the enrolment.
if (!\enrol_lti\helper::verify_cartridge_token($toolid, $token)) {
    throw new \moodle_exception('incorrecttoken', 'enrol_lti');
}

$tool = \enrol_lti\helper::get_lti_tool($toolid);

if (!is_enabled_auth('lti')) {
    print_error('pluginnotenabled', 'auth', '', get_string('pluginname', 'auth_lti'));

} else if (!enrol_is_enabled('lti')) {
    print_error('enrolisdisabled', 'enrol_lti');

} else if ($tool->status != ENROL_INSTANCE_ENABLED) {
    print_error('enrolisdisabled', 'enrol_lti');

} else {
    header('Content-Type: text/xml; charset=utf-8');
    echo \enrol_lti\helper::create_cartridge($toolid);
}

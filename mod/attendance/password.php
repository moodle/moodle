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
 * Displays help via AJAX call or in a new page
 *
 * Use {@see core_renderer::help_icon()} or {@see addHelpButton()} to display
 * the help icon.
 *
 * @copyright  2017 Dan Marsden
 * @package    mod_attendance
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->libdir.'/tcpdf/tcpdf_barcodes_2d.php'); // Used for generating qrcode.

$session = required_param('session', PARAM_INT);
$session = $DB->get_record('attendance_sessions', array('id' => $session), '*', MUST_EXIST);

$cm = get_coursemodule_from_instance('attendance', $session->attendanceid);
require_login($cm->course, $cm);

$context = context_module::instance($cm->id);
$capabilities = array('mod/attendance:manageattendances', 'mod/attendance:takeattendances', 'mod/attendance:changeattendances');
if (!has_any_capability($capabilities, $context)) {
    exit;
}

if (optional_param('returnpasswords', 0, PARAM_INT) == 1) {
    header('Content-Type: application/json');
    echo attendance_return_passwords($session);
    exit;
}

$PAGE->set_url('/mod/attendance/password.php');
$PAGE->set_pagelayout('popup');

$PAGE->set_context(context_system::instance());

$PAGE->set_title(get_string('password', 'attendance'));

echo $OUTPUT->header();

$showpassword = (isset($session->studentpassword) && strlen($session->studentpassword) > 0);
$showqr = (isset($session->includeqrcode) && $session->includeqrcode == 1);
$rotateqr = (isset($session->rotateqrcode) && $session->rotateqrcode == 1);

if ($showpassword  && !$rotateqr) {
    attendance_renderpassword($session);
}

if ($showqr) {
    attendance_renderqrcode($session);
}

if ($rotateqr) {
    attendance_generate_passwords($session);
    attendance_renderqrcoderotate($session);
}

echo $OUTPUT->footer();

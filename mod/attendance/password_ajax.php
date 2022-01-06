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
define('AJAX_SCRIPT', true);
require_once(dirname(__FILE__).'/../../config.php');

$session = required_param('session', PARAM_INT);
$session = $DB->get_record('attendance_sessions', array('id' => $session), '*', MUST_EXIST);

$cm = get_coursemodule_from_instance('attendance', $session->attendanceid);
require_login($cm->course, $cm);

$context = context_module::instance($cm->id);
$capabilities = array('mod/attendance:manageattendances', 'mod/attendance:takeattendances', 'mod/attendance:changeattendances');
if (!has_any_capability($capabilities, $context)) {
    exit;
}

$PAGE->set_url('/mod/attendance/password.php');
$PAGE->set_pagelayout('popup');

$PAGE->set_context(context_system::instance());

$data->heading = get_string('passwordgrp', 'attendance');
if (isset($session->includeqrcode) && $session->includeqrcode == 1) {
    $studentattendancepage = '/mod/attendance/password.php?session=' . $session->id;
    $data->text = html_writer::tag('p', html_writer::span($session->studentpassword, 'student-password') .
        html_writer::empty_tag('br') .
        html_writer::link($CFG->wwwroot . $studentattendancepage, get_string('showqrcode', 'attendance')));
} else {
    $data->text = html_writer::span($session->studentpassword, 'student-password');
}

echo json_encode($data);

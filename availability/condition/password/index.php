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
 * Availability password - Simple page for entering password (without AJAX)
 *
 * @package    availability_password
 * @copyright  2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
global $PAGE, $OUTPUT;

$cmid = required_param('id', PARAM_INT);
/** @var cm_info $cm */
list($course, $cm) = get_course_and_cm_from_cmid($cmid);

$url = new \core\url('/availability/condition/password/index.php', ['id' => $cm->id]);
$PAGE->set_url($url);

require_login($course, false);

$custom = ['cm' => $cm];
$form = new \availability_password\password_form(null, $custom);

$error = '';
$format = course_get_format($course);
$courseredir = $format->get_view_url(null);
if ($form->is_cancelled()) {
    redirect($courseredir);
}
if ($data = $form->get_data()) {
    if (\availability_password\condition::submit_password_for_cm($cm, $data->activitypassword)) {
        redirect($courseredir);
    } else {
        $error = get_string('wrongpassword', 'availability_password');
    }
}

$title = get_string('enterpasswordfor', 'availability_password', $cm->get_formatted_name());
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();
if ($error) {
    echo \core\output\html_writer::div($error, 'alert alert-danger');
}
$form->display();
echo $OUTPUT->footer();

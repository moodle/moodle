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
 * Customise the course role names.
 *
 * @package    core_enrol
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once("$CFG->dirroot/course/lib.php");


$id = required_param('id', PARAM_INT);
$action  = optional_param('action', '', PARAM_ALPHANUMEXT);
$filter = optional_param('ifilter', 0, PARAM_INT);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
$context = core\context\course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('moodle/course:renameroles', $context);

if ($course->id == SITEID) {
    redirect("$CFG->wwwroot/");
}

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/enrol/renameroles.php', ['id' => $course->id]);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('rolerenaming'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->render_participants_tertiary_nav($course);

echo $OUTPUT->paragraph(get_string('rolerenaming_help'));

$customdata = [
    'id' => $course->id,
    'roles' => role_get_names($context, ROLENAME_ORIGINAL),
];
$mform = new core_enrol\form\renameroles(null, $customdata);
if ($data = $mform->get_data()) {
    save_local_role_names($course->id, (array)$data);
    core\notification::add(get_string('rolerenaming_success'), core\notification::SUCCESS);
}

$mform->display();

echo $OUTPUT->footer();

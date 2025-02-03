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
 * Course activities overview page.
 *
 * @package    core_course
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once($CFG->libdir . '/completionlib.php');

$courseid = required_param('id', PARAM_INT);

$PAGE->set_url('/course/overview.php', ['id' => $courseid]);

$course = get_course($courseid);

$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('moodle/course:viewoverview', $context);

$output = $PAGE->get_renderer('format_' . $course->format);
$overview = new core_course\output\local\overview\overviewpage($course);

$PAGE->set_pagelayout('incourse');

$PAGE->set_title(get_string('overview_page_title', 'course', $course->fullname));
$PAGE->set_heading($course->fullname);
echo $output->header();

echo $output->heading(get_string('activities'), 2, 'h4');
echo $output->paragraph(get_string('overview_info', 'course'));

echo $output->render($overview);

echo $OUTPUT->footer();

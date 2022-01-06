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
 * Page called by teacher to carry out functions without loading course page.
 *
 * @package format_tiles
 * @copyright  2020 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

require_once('../../../config.php');

global $PAGE, $OUTPUT;

$courseid = required_param('courseid', PARAM_INT);
require_login($courseid, false);
$context = context_course::instance($courseid);
require_capability('moodle/course:viewhiddenactivities', $context);

$action = required_param('action', PARAM_TEXT);
$pageurl = new moodle_url('/course/format/tiles/teachertools.php', array('action' => $action));
$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

$PAGE->set_url($pageurl);
$PAGE->set_context($context);

$o = '';

switch ($action) {
    case 'reordersections':
        require_sesskey();
        \format_tiles\course_section_manager::resolve_section_misnumbering($courseid);
        redirect($courseurl);
        break;
    default:
        break;
}

echo $OUTPUT->header();
echo $o;
echo $OUTPUT->footer();


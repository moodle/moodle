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
 * The main index page.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = required_param('id', PARAM_INT); // Course id.

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

$strboard         = get_string('modulename', 'board');
$strboards        = get_string('modulenameplural', 'board');
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/mod/board/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strboards);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strboards);
echo $OUTPUT->header();
echo $OUTPUT->heading($strboards);
if (!$boards = get_all_instances_in_course('board', $course)) {
    notice(get_string('thereareno', 'moodle', $strboards), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($boards as $board) {
    $cm = $modinfo->cms[$board->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($board->section !== $currentsection) {
            if ($board->section) {
                $printsection = get_section_name($course, $board->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $board->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($board->timemodified)."</span>";
    }

    $class = $board->visible ? '' : 'class="dimmed"'; // Hidden modules are dimmed.

    $table->data[] = array (
        $printsection,
        "<a $class href=\"view.php?id=$cm->id\">".format_string($board->name)."</a>",
        format_module_intro('board', $board, $cm->id));
}

echo html_writer::table($table);

echo $OUTPUT->footer();

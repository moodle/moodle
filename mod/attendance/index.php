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
 * redjrects to the first Attendance in the course.
 *
 * @package   mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);

$PAGE->set_url('/mod/attendance/index.php', array('id' => $id));
$PAGE->set_pagelayout('incourse');

\mod_attendance\event\course_module_instance_list_viewed::create_from_course($course)->trigger();

// Print the header.
$strplural = get_string("modulename", "attendance");
$PAGE->navbar->add($strplural);
$PAGE->set_title($strplural);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($strplural));

$context = context_course::instance($course->id);

require_capability('mod/attendance:view', $context);

if (! $atts = get_all_instances_in_course("attendance", $course)) {
    $url = new moodle_url('/course/view.php', array('id' => $course->id));
    notice(get_string('thereareno', 'moodle', $strplural), $url);
    die;
}

$usesections = course_format_uses_sections($course->format);

// Print the list of instances.

$timenow = time();
$strname  = get_string("name");

$table = new html_table();

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname);
    $table->align = array ("center", "left");
} else {
    $table->head  = array ($strname);
    $table->align = array ("left");
}

foreach ($atts as $att) {
    // Get the responses of each attendance.
    $viewurl = new moodle_url('/mod/attendance/view.php', array('id' => $att->coursemodule));

    $dimmedclass = $att->visible ? '' : 'class="dimmed"';
    $link = '<a '.$dimmedclass.' href="'.$viewurl->out().'">'.$att->name.'</a>';

    if ($usesections) {
        $tabledata = array (get_section_name($course, $att->section), $link);
    } else {
        $tabledata = array ($link);
    }

    $table->data[] = $tabledata;
}

echo "<br />";

echo html_writer::table($table);

echo $OUTPUT->footer();
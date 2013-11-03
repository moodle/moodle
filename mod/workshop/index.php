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
 * Prints the list of all workshops in the course
 *
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // course

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course);

add_to_log($course->id, 'workshop', 'view all', "index.php?id=$course->id", '');

$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/mod/workshop/index.php', array('id' => $course->id));
$PAGE->set_title($course->fullname);
$PAGE->set_heading($course->shortname);
$PAGE->navbar->add(get_string('modulenameplural', 'workshop'));

/// Output starts here

echo $OUTPUT->header();

/// Get all the appropriate data

if (! $workshops = get_all_instances_in_course('workshop', $course)) {
    echo $OUTPUT->heading(get_string('noworkshops', 'workshop'), 2);
    echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id' => $course->id)));
    echo $OUTPUT->footer();
    die();
}

$usesections = course_format_uses_sections($course->format);

$timenow        = time();
$strname        = get_string('name');
$table          = new html_table();

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname);
    $table->align = array ('center', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left');
}

foreach ($workshops as $workshop) {
    if (empty($workshop->visible)) {
        $link = html_writer::link(new moodle_url('/mod/workshop/view.php', array('id' => $workshop->coursemodule)),
                                  $workshop->name, array('class' => 'dimmed'));
    } else {
        $link = html_writer::link(new moodle_url('/mod/workshop/view.php', array('id' => $workshop->coursemodule)),
                                  $workshop->name);
    }

    if ($usesections) {
        $table->data[] = array(get_section_name($course, $workshop->section), $link);
    } else {
        $table->data[] = array($link);
    }
}

echo $OUTPUT->heading(get_string('modulenameplural', 'workshop'), 2);
echo html_writer::table($table);
echo $OUTPUT->footer();

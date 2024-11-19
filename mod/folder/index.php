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
 * List of file folders in course
 *
 * @package   mod_folder
 * @copyright 2009 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = required_param('id', PARAM_INT); // course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

$params = array(
    'context' => context_course::instance($course->id)
);
$event = \mod_folder\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->trigger();

$strfolder       = get_string('modulename', 'folder');
$strfolders      = get_string('modulenameplural', 'folder');
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/mod/folder/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strfolders);
$PAGE->set_heading($course->fullname);
$PAGE->set_secondary_active_tab('coursehome');
$PAGE->navbar->add($strfolders);
echo $OUTPUT->header();
if (!$PAGE->has_secondary_navigation()) {
    echo $OUTPUT->heading($strfolders);
}

if (!$folders = get_all_instances_in_course('folder', $course)) {
    notice(get_string('thereareno', 'moodle', $strfolders), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $strsectionname = course_get_format($course)->get_generic_section_name();
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($folders as $folder) {
    $cm = $modinfo->cms[$folder->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($folder->section !== $currentsection) {
            if ($folder->section) {
                $printsection = get_section_name($course, $folder->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $folder->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($folder->timemodified)."</span>";
    }

    $class = $folder->visible ? '' : 'class="dimmed"'; // hidden modules are dimmed
    $table->data[] = array (
        $printsection,
        "<a $class href=\"view.php?id=$cm->id\">".format_string($folder->name)."</a>",
        format_module_intro('folder', $folder, $cm->id));
}

echo html_writer::table($table);

echo $OUTPUT->footer();

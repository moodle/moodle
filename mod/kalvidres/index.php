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
 * Kaltura video resource library script.
 *
 * @package    mod_kalvidres
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT); // Course ID.
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

$strlastmodified = get_string("lastmodified");                           // Last Modified.
$strintro = get_string("moduleintro");                                   // Description.
$strsectionname  = get_string('sectionname', 'format_'.$course->format); // Section.
$strplural = get_string("modulenameplural", "mod_kalvidres");            // Video Resource.

$PAGE->set_url('/mod/kalvidres/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strplural);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strplural);
echo $OUTPUT->header();
echo $OUTPUT->heading($strplural);

// Since $includeinvisible defaults to false, all res in vidres will be uservisible according to get_fast_modinfo.
if (!$vidres = get_all_instances_in_course('kalvidres', $course)) {
    notice(get_string('noresource', 'mod_kalvidres'), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $table->head  = array ($strsectionname, $strplural, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array  ($strlastmodified, $strplural, $strintro);
    $table->align = array  ('left', 'left', 'left');
}

$currentsection = '';
foreach ($vidres as $res) {
    if ($usesections) {
        $printsection = '';
        if ($res->section !== $currentsection) {
            if ($res->section) {
                $printsection = get_section_name($course, $res->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $res->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($res->timemodified)."</span>";
    }

    $icon = $OUTPUT->pix_icon('icon', get_string('modulename', 'mod_kalvidres'), 'mod_kalvidres');

    $class = $res->visible ? '' : 'class="dimmed"'; // Hidden modules are dimmed.

    $table->data[] = array (
        $printsection,
        "<a $class href=\"view.php?id=$res->coursemodule\">".$icon.format_string($res->name)."</a>",
        format_module_intro('kalvidres', $res, $res->coursemodule)
    );

}

echo html_writer::table($table);

echo $OUTPUT->footer();
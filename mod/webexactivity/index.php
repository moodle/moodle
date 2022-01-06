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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Code to display all instances in the passed course.

require('../../config.php');

$id = required_param('id', PARAM_INT); // Course ID.
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_login($course);
$actvitiesstring = get_string("modulenameplural", "webexactivity");

// Page setup.
$returnurl = new moodle_url('/mod/webexactivity/index.php', array('id' => $id));
$PAGE->set_url($returnurl);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($course->shortname.': '.$actvitiesstring);

// Security.
require_capability('mod/webexactivity:view', $PAGE->context);


echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($actvitiesstring));

// Get all the appropriate data.
if (!$webexs = get_all_instances_in_course("webexactivity", $course)) {
    notice(get_string('thereareno', 'moodle', $actvitiesstring), "../../course/view.php?id=$course->id");
    die;
}

// Setup headings.
$headings = array(get_string('name'), get_string('starttime', 'webexactivity'), get_string('recordings', 'webexactivity'));

if (course_format_uses_sections($course->format)) {
    array_unshift($headings, get_string('sectionname', 'format_'.$course->format));
} else {
    array_unshift($headings, '');
}

$table = new html_table();
$table->head = $headings;

// Build the table.
$currentsection = '';
foreach ($webexs as $webex) {
    $cm = get_coursemodule_from_instance('webexactivity', $webex->id);
    $context = context_module::instance($cm->id);
    $canhost = has_capability('mod/webexactivity:hostmeeting', $context);
    $data = array();

    // Section number/name if necessary.
    $strsection = '';
    if ($webex->section != $currentsection) {
        if ($webex->section) {
            $strsection = $webex->section;
            $strsection = get_section_name($course, $webex->section);
        }
        if ($currentsection) {
            $learningtable->data[] = 'hr';
        }
        $currentsection = $webex->section;
    }
    $data[] = $strsection;

    // Set the class for hidden.
    $class = '';
    if (!$webex->visible) {
        $class = ' class="dimmed"';
    }

    $data[] = "<a$class href=\"view.php?id=$webex->coursemodule\">" .
            format_string($webex->name, true) . '</a>';

    $data[] = userdate($webex->starttime);

    $params = array('webexid' => $webex->id);
    if (!$canhost) {
        $params['visible'] = '1';
    }
    $count = $DB->count_records('webexactivity_recording', $params);

    if ($count) {
        $countstr = (string)$count;
    } else {
        $countstr = '-';
    }
    $data[] = $countstr;

    $table->data[] = $data;
}

// Display the table.
echo html_writer::table($table);

echo $OUTPUT->footer();

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

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/export/lib.php';
require_once 'grade_export_xml.php';

$id = required_param('id', PARAM_INT); // course id

$PAGE->set_url('/grade/export/xml/index.php', array('id'=>$id));

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);

require_capability('moodle/grade:export', $context);
require_capability('gradeexport/xml:view', $context);

print_grade_page_head($COURSE->id, 'export', 'xml', get_string('exportto', 'grades') . ' ' . get_string('pluginname', 'gradeexport_xml'));

if (!empty($CFG->gradepublishing)) {
    $CFG->gradepublishing = has_capability('gradeexport/xml:publish', $context);
}

//'idnumberrequired'=>true excludes grade items that dont have an ID to use during import
$mform = new grade_export_form(null, array('idnumberrequired'=>true, 'publishing'=>true, 'updategradesonly'=>true));

$groupmode    = groups_get_course_groupmode($course);   // Groups are being used
$currentgroup = groups_get_course_group($course, true);
if ($groupmode == SEPARATEGROUPS and !$currentgroup and !has_capability('moodle/site:accessallgroups', $context)) {
    echo $OUTPUT->heading(get_string("notingroup"));
    echo $OUTPUT->footer();
    die;
}

// process post information
if ($data = $mform->get_data()) {
    $export = new grade_export_xml($course, $currentgroup, '', false, $data->updatedgradesonly, $data->display, $data->decimals);

    // print the grades on screen for feedbacks
    $export->process_form($data);
    $export->print_continue();

    $export->display_preview(true); //true == skip users without idnumber as they cannot be identified when importing
    echo $OUTPUT->container(get_string('useridnumberwarning','gradeexport_xml'), 'useridnumberwarning mdl-align');

    echo $OUTPUT->footer();
    exit;
}

groups_print_course_menu($course, 'index.php?id='.$id);
echo '<div class="clearer"></div>';

$mform->display();

echo $OUTPUT->footer();


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
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once 'edit_form.php';


$contextid = optional_param('id', SYSCONTEXTID, PARAM_INT);

if (!$context = get_context_instance_by_id($contextid)) {
    error('Incorrect context id');
}

if ($context->contextlevel == CONTEXT_SYSTEM or $context->contextlevel == CONTEXT_COURSECAT) {
    require_once $CFG->libdir.'/adminlib.php';
    require_login();
    admin_externalpage_setup('letters');
    $admin = true;
    $returnurl = "$CFG->wwwroot/grade/edit/letter/edit.php"; // stay in the same page


} else if ($context->contextlevel == CONTEXT_COURSE) {
    require_login($context->instanceid);
    $admin = false;
    $returnurl = $CFG->wwwroot.'/grade/edit/letter/index.php?id='.$context->instanceid;

} else {
    error('Incorrect context level');
}

require_capability('moodle/grade:manageletters', $context);

$strgrades = get_string('grades');
$pagename  = get_string('letters', 'grades');

$letters = grade_get_letters($context);
$num = count($letters) + 3;

$data = new object();
$data->id = $context->id;

$i = 1;
foreach ($letters as $boundary=>$letter) {
    $gradelettername = 'gradeletter'.$i;
    $gradeboundaryname = 'gradeboundary'.$i;

    $data->$gradelettername   = $letter;
    $data->$gradeboundaryname = $boundary;
    $i++;
}
$data->override = record_exists('grade_letters', 'contextid', $contextid);

$mform = new edit_letter_form(null, array('num'=>$num, 'admin'=>$admin));
$mform->set_data($data);

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    if (!$admin and empty($data->override)) {
        delete_records('grade_letters', 'contextid', $context->id);
        redirect($returnurl);
    }

    $letters = array();
    for($i=1; $i<$num+1; $i++) {
        $gradelettername = 'gradeletter'.$i;
        $gradeboundaryname = 'gradeboundary'.$i;

        if (array_key_exists($gradeboundaryname, $data) and $data->$gradeboundaryname != -1) {
            $letter = trim($data->$gradelettername);
            if ($letter == '') {
                continue;
            }
            $letters[$data->$gradeboundaryname] = $letter;
        }
    }
    krsort($letters, SORT_NUMERIC);

    $old_ids = array();
    if ($records = get_records('grade_letters', 'contextid', $context->id, 'lowerboundary ASC', 'id')) {
        $old_ids = array_keys($records);
    }

    foreach($letters as $boundary=>$letter) {
        $record = new object();
        $record->letter        = $letter;
        $record->lowerboundary = $boundary;
        $record->contextid     = $context->id;

        if ($old_id = array_pop($old_ids)) {
            $record->id = $old_id;
            update_record('grade_letters', $record);
        } else {
            insert_record('grade_letters', $record);
        }
    }

    foreach($old_ids as $old_id) {
        delete_records('grade_letters', 'id', $old_id);
    }

    redirect($returnurl);
}


//page header
if ($admin) {
    admin_externalpage_print_header();

} else {
    print_grade_page_head($COURSE->id, 'letter', 'edit', get_string('editgradeletters', 'grades'));
}

$mform->display();

print_footer($COURSE);
?>

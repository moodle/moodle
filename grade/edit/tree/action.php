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
require_once $CFG->dirroot.'/grade/lib.php';

$courseid = required_param('id', PARAM_INT);
$action   = required_param('action', PARAM_ALPHA);
$eid      = required_param('eid', PARAM_ALPHANUM);

$PAGE->set_url('/grade/edit/tree/action.php', array('id'=>$courseid, 'action'=>$action, 'eid'=>$eid));

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url($CFG->wwwroot.'/grade/edit/tree/index.php?id='.$course->id);

// get the grading tree object
$gtree = new grade_tree($courseid, false, false);

// what are we working with?
if (!$element = $gtree->locate_element($eid)) {
    print_error('invalidelementid', '', $returnurl);
}
$object = $element['object'];
$type   = $element['type'];


switch ($action) {
    case 'hide':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:hide', $context)) {
                print_error('nopermissiontohide', '', $returnurl);
            }
            if ($type == 'grade' and empty($object->id)) {
                $object->insert();
            }
            $object->set_hidden(1, true);
        }
        break;

    case 'show':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:hide', $context)) {
                print_error('nopermissiontoshow', '', $returnurl);
            }
            if ($type == 'grade' and empty($object->id)) {
                $object->insert();
            }
            $object->set_hidden(0, true);
        }
        break;

    case 'lock':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:lock', $context)) {
                print_error('nopermissiontolock', '', $returnurl);
            }
            if ($type == 'grade' and empty($object->id)) {
                $object->insert();
            }
            $object->set_locked(1, true, true);
        }
        break;

    case 'unlock':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:unlock', $context)) {
                print_error('nopermissiontounlock', '', $returnurl);
            }
            if ($type == 'grade' and empty($object->id)) {
                $object->insert();
            }
            $object->set_locked(0, true, true);
        }
        break;
}

redirect($returnurl);
//redirect($returnurl, 'debug delay', 5);



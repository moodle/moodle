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

define('NO_MOODLE_COOKIES', true); // session not used here
require_once '../../../config.php';
require_once($CFG->dirroot.'/grade/export/ods/grade_export_ods.php');

$id                 = required_param('id', PARAM_INT);
$groupid            = optional_param('groupid', 0, PARAM_INT);
$itemids            = required_param('itemids', PARAM_RAW);
$exportfeedback     = optional_param('export_feedback', 0, PARAM_BOOL);
$displaytype        = optional_param('displaytype', $CFG->grade_export_displaytype, PARAM_RAW);
$decimalpoints      = optional_param('decimalpoints', $CFG->grade_export_decimalpoints, PARAM_INT);
$onlyactive         = optional_param('export_onlyactive', 0, PARAM_BOOL);

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('invalidcourseid');
}

require_user_key_login('grade/export', $id); // we want different keys for each course

if (empty($CFG->gradepublishing)) {
    print_error('gradepubdisable');
}

$context = context_course::instance($id);
require_capability('moodle/grade:export', $context);
require_capability('gradeexport/ods:view', $context);
require_capability('gradeexport/ods:publish', $context);

if (!groups_group_visible($groupid, $COURSE)) {
    print_error('cannotaccessgroup', 'grades');
}

// Get all url parameters and create an object to simulate a form submission.
$formdata = grade_export::export_bulk_export_data($id, $itemids, $exportfeedback, $onlyactive, $displaytype,
        $decimalpoints);

$export = new grade_export_ods($course, $groupid, $formdata);
$export->print_grades();



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

$id                = required_param('id', PARAM_INT); // course id
$groupid           = optional_param('groupid', 0, PARAM_INT);
$itemids           = required_param('itemids', PARAM_RAW);
$export_feedback   = optional_param('export_feedback', 0, PARAM_BOOL);
$updatedgradesonly = optional_param('updatedgradesonly', false, PARAM_BOOL);
$displaytype       = optional_param('displaytype', $CFG->grade_export_displaytype, PARAM_INT);
$decimalpoints     = optional_param('decimalpoints', $CFG->grade_export_decimalpoints, PARAM_INT);

if (!$course = get_record('course', 'id', $id)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);

require_capability('moodle/grade:export', $context);
require_capability('gradeexport/xml:view', $context);

if (groups_get_course_groupmode($COURSE) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
    if (!groups_is_member($groupid, $USER->id)) {
        print_error('cannotaccessgroup', 'grades');
    }
}

// print all the exported data here
$export = new grade_export_xml($course, $groupid, $itemids, $export_feedback, $updatedgradesonly, $displaytype, $decimalpoints);
$export->print_grades();

?>

<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'assign', language 'en_us', version '4.1'.
 *
 * @package     assign
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allocatedmarker'] = 'Allocated Grader';
$string['allocatedmarker_help'] = 'Grader allocated to this submission';
$string['assign:manageallocations'] = 'Manage graders allocated to submissions';
$string['assign:viewblinddetails'] = 'View student identities when blind grading is enabled';
$string['batchoperationconfirmsetmarkingallocation'] = 'Set grading allocation for all selected submissions?';
$string['batchoperationconfirmsetmarkingworkflowstate'] = 'Set grading workflow state for all selected submissions?';
$string['batchsetallocatedmarker'] = 'Set allocated grader for {$a} selected user(s).';
$string['batchsetmarkingworkflowstateforusers'] = 'Set allocated grader for {$a} selected user(s).';
$string['blindmarking'] = 'Blind grading';
$string['blindmarking_help'] = 'Blind grading hides the identity of students to graders. Blind grading settings will be locked once a submission or grade has been made in relation to this assignment.';
$string['eventbatchsetmarkerallocationviewed'] = 'Batch set grader allocation viewed';
$string['eventmarkerupdated'] = 'The allocated grader has been updated.';
$string['gradingduedate_help'] = 'The expected date that marking of the submissions should be completed by. This date is used to prioritize dashboard notifications for teachers.';
$string['marker'] = 'Grader';
$string['markerfilter'] = 'Grader filter';
$string['markerfilternomarker'] = 'No grader';
$string['markingallocation'] = 'Use grading allocation';
$string['markingallocation_help'] = 'If enabled together with grading workflow, graders can be allocated to particular students.';
$string['markingworkflow'] = 'Use grading workflow';
$string['markingworkflow_help'] = 'If enabled, grades will go through a series of workflow stages before being released to students. This allows for multiple rounds of grading and allows grades to be released to all students at the same time.';
$string['markingworkflowstate'] = 'Grading workflow state';
$string['markingworkflowstate_help'] = 'Possible workflow states may include (depending on your permissions):

* Not graded - the grader has not yet started
* In grading - the grader has started but not yet finished
* Grading completed - the grader has finished but might need to go back for checking/corrections
* In review - the grading is now with the teacher in charge for quality checking
* Ready for release - the teacher in charge is satisfied with the grading but wait before giving students access to the grading
* Released - the student can access the grades/feedback';
$string['markingworkflowstateinmarking'] = 'In grading';
$string['markingworkflowstatenotmarked'] = 'Not graded';
$string['markingworkflowstatereadyforreview'] = 'Grading completed';
$string['maxperpage_help'] = 'The maximum number of assignments a grader can show in the assignment grading page. Useful to prevent timeouts on courses with very large enrollments.';
$string['quickgrading_help'] = 'Quick grading allows you to assign grades (and outcomes) directly in the submissions table. Quick grading is not compatible with advanced grading and is not recommended when there are multiple graders.';
$string['reopenuntilpassincompatiblewithblindmarking'] = 'Reopen until pass option is incompatible with blind grading, because the grades are not released to the gradebook until the student identities are revealed.';
$string['setmarkerallocationforlog'] = 'Set grading allocation : (id={$a->id}, fullname={$a->fullname}, grader={$a->marker}).';
$string['setmarkingallocation'] = 'Set allocated grader';
$string['setmarkingworkflowstate'] = 'Set grading workflow state';
$string['setmarkingworkflowstateforlog'] = 'Set grading workflow state : (id={$a->id}, fullname={$a->fullname}, state={$a->state}).';
$string['validmarkingworkflowstates'] = 'Valid grading workflow states';
$string['viewbatchmarkingallocation'] = 'View batch set grading allocation page.';
$string['viewbatchsetmarkingworkflowstate'] = 'View batch set grading workflow state page.';

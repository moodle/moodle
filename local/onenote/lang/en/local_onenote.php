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
 * Language file definitions for OneNote local plugin
 * @package    local_onenote
 * @author Vinayak (Vin) Bhalerao (v-vibhal@microsoft.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Microsoft, Inc.
 */

$string['pluginname'] = 'Microsoft OneNote';
$string['submissiontitle'] = 'Submission: {$a->assign_name} [{$a->student_firstname} {$a->student_lastname}]';
$string['feedbacktitle'] = 'Feedback: {$a->assign_name} [{$a->student_firstname} {$a->student_lastname}]';
$string['connction_error'] = 'Cannot connect to OneNote. Please wait a few moments and retry.';
$string['onenote_page_error'] = 'Could not open the OneNote page for this submission or feedback.';
$string['error_noapiavailable'] = 'No OneNote API is available. Connections to OneNote can only be used by users connected to Microsoft 365.';
$string['notebookname'] = 'Moodle Notebook';
$string['erroronenoteapibadcall'] = 'Error in API call.';
$string['erroronenoteapibadcall_message'] = 'Error in API call: {$a}';
$string['errornosection'] = 'Could not get or create a section in your OneNote Notebook';
$string['errornopostdata'] = 'Could not create page data to send to OneNote.';
$string['errorsubmissioninteachercontext'] = 'Attempted to create a submission from teacher grading context.';
$string['errorfeedbackinstudentcontext'] = 'Attempted to create feedback in student submission context.';

$string['privacy:metadata:local_onenote'] = 'Microsoft OneNote Local Plugin';
$string['privacy:metadata:local_onenote_user_sections'] = 'OneNote sections for Moodle courses';
$string['privacy:metadata:local_onenote_user_sections:user_id'] = 'The ID of the Moodle user';
$string['privacy:metadata:local_onenote_user_sections:course_id'] = 'The ID of the Moodle course';
$string['privacy:metadata:local_onenote_user_sections:section_id'] = 'The ID of the OneNote section.';
$string['privacy:metadata:local_onenote_assign_pages'] = 'OneNote pages for assignment submission and feedback';
$string['privacy:metadata:local_onenote_assign_pages:user_id'] = 'The ID of the Moodle user';
$string['privacy:metadata:local_onenote_assign_pages:assign_id'] = 'The ID of the assignment';
$string['privacy:metadata:local_onenote_assign_pages:submission_student_page_id'] = 'The page ID for the student submission';
$string['privacy:metadata:local_onenote_assign_pages:feedback_student_page_id'] = 'The page ID for the student feedback';
$string['privacy:metadata:local_onenote_assign_pages:submission_teacher_page_id'] = 'The page ID for the teacher submission';
$string['privacy:metadata:local_onenote_assign_pages:feedback_teacher_page_id'] = 'The page ID for the teacher feedback';
$string['privacy:metadata:local_onenote_assign_pages:teacher_lastviewed'] = 'The time the teacher last viewed the submission';
$string['privacy:metadata:local_onenote_assign_pages:student_lastmodified'] = 'The time the student last modified the submission';

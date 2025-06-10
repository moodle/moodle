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

$string['pluginname'] = 'Post Grades';

$string['posting_periods'] = 'Posting Periods';
$string['confirm'] = 'Confirm';

$string['notactive'] = 'You are trying to post grades in a an inactive posting period.';
$string['notvalidgroup'] = '{$a} is not a valid posting group';

$string['post_grades:canpost'] = 'User can post grades to the external web service.';
$string['post_grades:canconfigure'] = 'User can create posting periods.';
$string['post_grades:addinstance'] = 'Add block to page';
$string['post_grades:myaddinstance'] = 'Add block to page';
$string['domino_application_url'] = 'Domino post grade application URL';
$string['mylsu_gradesheet_url'] = 'myLSU Gradesheet URL';

$string['https_protocol'] = 'HTTPS?';
$string['https_protocol_desc'] = 'Post grades over `https`. Leave unchecked for `http` posting.';

$string['header_help'] = 'Below are system wide settings for the external web service.

- You can [edit posting periods]({$a->period_url}) on this page.
- You can [reset posting flags]({$a->reset_url}) on this page.';

$string['new_posting'] = 'New posting';
$string['no_posting'] = 'There are no posting periods. Continue to create one.';

$string['message'] = 'You are about to post {$a->post_type} grades for {$a->name} in {$a->fullname}. Please note: you can only post <strong>once</strong> from Moodle for each section in each posting period.';
$string['post_type_grades'] = 'Post {$a->post_type} Grades';
$string['make_changes'] = 'Return to Gradebook';

$string['nopublishing'] = 'Grade publishing has not been actived. Please contact the Moodle admin about this error.';
$string['alreadyposted'] = 'You have already posted {$a->post_type} grades for {$a->name} from Moodle. If you have confirmed
that there was a problem with the transport, please contact the Moodle Administrator to allow you to post
from Moodle once more.';

$string['nopostings'] = 'No postings were found. Refine your search.';

$string['posted'] = 'Already posted';
$string['not_posted'] = 'Have not posted';

$string['finalgrade_item'] = 'Final grade';

// UES People.
$string['student_audit'] = 'Auditing';

// Quick Edit Strings.
$string['student_incomplete'] = 'Incomplete';

// Form strings.
$string['posting_for'] = '{$a->post_type} grades for {$a->fullname} {$a->course_name} Section {$a->sec_number}';
$string['view_gradsheet'] = 'View Gradesheet';
$string['reset_posting'] = 'Reset Postings';
$string['find_postings'] = 'Find Postings';
$string['semester'] = 'Semester';
$string['posting_period'] = 'Posting Period';
$string['start_time'] = 'Start time';
$string['end_time'] = 'End time';
$string['export_number'] = 'Export Number';
$string['are_you_sure'] = 'Are you sure you want to delete the posting period for {$a}? This action cannot be reversed.';
$string['post_type'] = 'Posting Type';

$string['no_students'] = 'No students for {$a}';

$string['midterm'] = 'Midterm';
$string['onlinemidterm'] = 'Online Midterm';
$string['final'] = 'Final';
$string['onlinefinal'] = 'Online Final';
$string['degree'] = 'Degree Candidate';
$string['test'] = 'PG Test';

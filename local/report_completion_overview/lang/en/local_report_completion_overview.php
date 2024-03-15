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
 * @package   local_report_license_usage
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Completion Overview Report';
$string['privacy:metadata:local_report_user_lic_allocs:id'] = 'Local report user license allocation record ID';
$string['privacy:metadata:local_report_user_lic_allocs:courseid'] = 'Course ID';
$string['privacy:metadata:local_report_user_lic_allocs:action'] = 'Allocation action';
$string['privacy:metadata:local_report_user_lic_allocs:userid'] = 'User ID';
$string['privacy:metadata:local_report_user_lic_allocs:licenseid'] = 'License ID';
$string['privacy:metadata:local_report_user_lic_allocs:issuedate'] = 'License issue Unix timestamp';
$string['privacy:metadata:local_report_user_lic_allocs'] = 'Local report user license allocation user information';
$string['hideexpiry'] = 'Highlight expire';
$string['report_completion_overview:view'] = 'View course completion overview report';
$string['showexpiry'] = 'Highlight all';
$string['showexpiryonly'] = 'Highlight course with valid length only';
$string['showexpiryonly_help'] = 'If this option is checked, then courses which do not have a valid length will not be displayed in colour in the graphical overview by default.';
$string['showfulldetail'] = 'Show full completion detail';
$string['showfulldetail_help'] = 'If this option is checked, then all of the completion information is displayed, otherwise it\'s just the completion and expiry dates.';
$string['warningduration'] = 'Expired warning limit';
$string['warningduration_help'] = 'This is the value of time before a course expires where the report will show the expiry warning colours instead of the OK colours.';
$string['coursesummary'] = 'Enroled: {$a->enrolled}
Started: {$a->timestarted}
Completed: {$a->timecompleted}
Expires: {$a->timeexpires}
Grade: {$a->finalscore}';
$string['coursesummary_noexpiry'] = 'Enroled: {$a->enrolled}
Started: {$a->timestarted}
Completed: {$a->timecompleted}
Grade: {$a->finalscore}';
$string['coursesummary_nograde'] = 'Enroled: {$a->enrolled}
Started: {$a->timestarted}
Completed: {$a->timecompleted}
Expires: {$a->timeexpires}
Result: Passed';
$string['coursesummary_nograde_noexpiry'] = 'Enroled: {$a->enrolled}
Started: {$a->timestarted}
Completed: {$a->timecompleted}
Result: Passed';
$string['coursesummary_partial'] = 'Completed: {$a->timecompleted}
Expires: {$a->timeexpires}';
$string['report_completion_overview_title'] = 'Completion overview report';
$string['notcompleted'] = 'In progress';
$string['notenrolled']  = 'Not enroled';
$string['indate'] = 'OK';
$string['expiring'] = 'Due';
$string['expired'] = 'Expired';
$string['coursestatus'] = '{$a} status';
$string['coursecompletion'] = '{$a} completion';
$string['courseexpiry'] = '{$a} expiry';

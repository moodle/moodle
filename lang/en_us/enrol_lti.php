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
 * Strings for component 'enrol_lti', language 'en_us', version '4.1'.
 *
 * @package     enrol_lti
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['enrolenddateerror'] = 'Enrollment end date cannot be earlier than start date';
$string['enrolmentfinished'] = 'Enrollment finished.';
$string['enrolmentnotstarted'] = 'Enrollment has not started.';
$string['enrolperiod'] = 'Enrollment duration';
$string['enrolperiod_help'] = 'Length of time that the enrollment is valid, starting with the moment the user enrolls themselves from the remote system. If disabled, the enrollment duration will be unlimited.';
$string['gradesync'] = 'Grade synchronization';
$string['lti:unenrol'] = 'Unenroll users from the course';
$string['maxenrolled'] = 'Maximum enrolled users';
$string['maxenrolled_help'] = 'The maximum number of remote users who can access the tool. If set to zero, the number of enrolled users is unlimited.';
$string['membersync'] = 'User synchronization';
$string['membersync_help'] = 'Whether a scheduled task synchronizes enrolled users in the remote system with enrollments in this course, creating an account for each remote user as necessary, and enrolling or unenrolling them as required.

If set to no, at the moment when a remote user accesses the tool, an account will be created for them and they will be automatically enrolled.';
$string['membersyncmode'] = 'User synchronization mode';
$string['membersyncmode_help'] = 'Whether remote users should be enrolled and/or unenrolled from this course.';
$string['membersyncmodeenrolandunenrol'] = 'Enroll new and unenroll missing users';
$string['membersyncmodeenrolnew'] = 'Enroll new users';
$string['membersyncmodeunenrolmissing'] = 'Unenroll missing users';
$string['privacy:metadata:enrol_lti_users'] = 'The list of users enrolled via an LTI provider';
$string['privacy:metadata:enrol_lti_users:lastaccess'] = 'The date at which the user was enrolled';
$string['privacy:metadata:enrol_lti_users:timecreated'] = 'The date at which the user was enrolled';
$string['requirecompletion'] = 'Require course or activity completion prior to grade synchronization';

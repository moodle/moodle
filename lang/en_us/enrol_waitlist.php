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
 * Strings for component 'enrol_waitlist', language 'en_us', version '4.1'.
 *
 * @package     enrol_waitlist
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['confirmation'] = 'If you proceed you will be enrolled in this course.<br><br>Are you absolutely sure you want to do this?';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during waitlist enrollment';
$string['enrolenddate_help'] = 'If enabled, users can enroll themselves until this date only.';
$string['enrolenddaterror'] = 'Enrollment end date cannot be earlier than start date';
$string['enrolme'] = 'Enroll me';
$string['enrolperiod'] = 'Enrollment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrollment is valid (in seconds). If set to zero, the enrollment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrollment is valid, starting with the moment the user enrolls themselves. If disabled, the enrollment duration will be unlimited.';
$string['enrolstartdate_help'] = 'If enabled, users can enroll themselves from this date onward only.';
$string['enrolusers'] = 'Enroll users';
$string['groupkey'] = 'Use group enrollment keys';
$string['groupkey_desc'] = 'Use group enrollment keys by default.';
$string['groupkey_help'] = 'In addition to restricting access to the course to only those who know the key, use of a group enrollment key means users are automatically added to the group when they enroll in the course.

To use a group enrollment key, an enrollment key must be specified in the course settings as well as the group enrollment key in the group settings.';
$string['longtimenosee'] = 'Unenroll inactive after';
$string['maxenrolled_help'] = 'Specifies the maximum number of users that can waitlist enroll. 0 means no limit.';
$string['maxenrolledreached'] = 'Maximum number of users allowed to waitlist-enroll was already reached.';
$string['password'] = 'Enrollment key';
$string['password_help'] = 'An enrollment key enables access to the course to be restricted to only those who know the key.

If the field is left blank, any user may enroll in the course.

If an enrollment key is specified, any user attempting to enroll in the course will be required to supply the key. Note that a user only needs to supply the enrollment key ONCE, when they enroll in the course.';
$string['passwordinvalid'] = 'Incorrect enrollment key, please try again';
$string['passwordinvalidhint'] = 'That enrollment key was incorrect, please try again<br />
(Here\'s a hint - it starts with \'{$a}\')';
$string['pluginname'] = 'Waitlist enrollment';
$string['pluginname_desc'] = 'The waitlist enrollment plugin allows users to choose which courses they want to participate in. The courses may be protected by an enrollment key. Internally the enrolment is done via the manual enrollment plugin which has to be enabled in the same course.';
$string['requirepassword'] = 'Require enrollment key';
$string['requirepassword_desc'] = 'Require enrollment key in new courses and prevent removing of enrollment key from existing courses.';
$string['sendcoursewelcomemessage_help'] = 'If enabled, users receive a welcome message via email when they waitlist-enroll in a course.';
$string['status'] = 'Allow waitlist enrollments';
$string['status_desc'] = 'Allow users to waitlist enroll into course by default.';
$string['status_help'] = 'This setting determines whether a user can enroll (and also unenroll if they have the appropriate permission) themselves from the course.';
$string['unenrolwaitlistconfirm'] = 'Do you really want to unenroll "{$a}"?';
$string['usepasswordpolicy_desc'] = 'Use standard password policy for enrollment keys.';
$string['waitlist:config'] = 'Configure waitlist enroll instances';
$string['waitlist:unenrol'] = 'Unenroll users from course';
$string['waitlist:unenrolself'] = 'Unenroll self from the course';
$string['waitlist:unenrolwaitlist'] = 'Unenroll waitlist from the course';

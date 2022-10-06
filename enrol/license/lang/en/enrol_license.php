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
 * @package   enrol_license
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['canntenrol'] = 'Enrolment is disabled or inactive';
$string['customwelcomemessage'] = 'Custom welcome message';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during license enrolment';
$string['enrolenddate'] = 'End date';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolme'] = 'Click here to start this course';
$string['enrolperiod'] = 'Enrolment period';
$string['enrolperiod_desc'] = 'Default length of the enrolment period (in seconds).'; // TODO: fixme!
$string['enrolstartdate'] = 'Start date';
$string['groupkey'] = 'Use group enrolment keys';
$string['groupkey_desc'] = 'Use group enrolment keys by default.';
$string['groupkey_help'] = 'In addition to restricting access to the course to only those who know the key, use of a group enrolment key means users are automatically added to the group when they enrol in the course.

To use a group enrolment key, an enrolment key must be specified in the course settings as well as the group enrolment key in the group settings.';
$string['licensecrontask'] = 'Enrol license scheduled task';
$string['licensenolongervalid'] = 'Your license for this course is no longer valid';
$string['licensenotyetvalid'] = 'Your access to this course will be available on {$a}';
$string['license:unenrolself'] = 'User can unenrol themselves';
$string['longtimenosee'] = 'Unenrol inactive after';
$string['longtimenosee_help'] = 'If users haven\'t accessed a course for a long time, then they are automatically unenrolled. This parameter specifies that time limit.  This is sepearate to the enrolement time which is set by license itself.';
$string['maxenrolled'] = 'Max enrolled users';
$string['maxenrolled_help'] = 'Specifies the maximum number of users that can license enrol. 0 means no limit.';
$string['maxenrolledreached'] = 'Maximum number of users allowed to license-enrol was already reached.';
$string['nolicenseinformationfound'] = 'Your account does not have a valid license to access this course.  If you require access contact your company manager to arrange a license.';
$string['password'] = 'Enrolment key';
$string['password_help'] = 'An enrolment key enables access to the course to be restricted to only those who know the key.

If the field is left blank, any user may enrol in the course.

If an enrolment key is specified, any user attempting to enrol in the course will be required to supply the key. Note that a user only needs to supply the enrolment key ONCE, when they enrol in the course.';
$string['passwordinvalid'] = 'Incorrect enrolment key, please try again';
$string['passwordinvalidhint'] = 'That enrolment key was incorrect, please try again<br />
(Here\'s a hint - it starts with \'{$a}\')';
$string['pluginname'] = 'License enrolment';
$string['pluginname_desc'] = 'The license enrolment plugin allows users to get access to courses after being assigned a license for them. Internally the enrolment is done via the manual enrolment plugin which has to be enabled in the same course.';
$string['privacy:metadata'] = 'The LIcense enrolment plugin only shows data stored in other locations.';
$string['requirepassword'] = 'Require enrolment key';
$string['requirepassword_desc'] = 'Require enrolment key in new courses and prevent removing of enrolment key from existing courses.';
$string['role'] = 'Assign role';
$string['license:config'] = 'Configure license enrol instances';
$string['license:manage'] = 'Manage enrolled users';
$string['license:unenrol'] = 'Unenrol users from course';
$string['license:unenrollicense'] = 'Unenrol license from the course';
$string['sendcoursewelcomemessage'] = 'Send course welcome message';
$string['sendcoursewelcomemessage_help'] = 'If enabled, users receive a welcome message via email when they license-enrol in a course.';
$string['showhint'] = 'Show hint';
$string['showhint_desc'] = 'Show first letter of the guest access key.';
$string['status'] = 'Allow license enrolments';
$string['status_desc'] = 'Allow users to license enrol into course by default.';
$string['status_help'] = 'This setting determines whether a user can enrol (and also unenrol if they have the appropriate permission) themselves from the course.';
$string['unenrollicenseconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['usepasswordpolicy'] = 'Use password policy';
$string['usepasswordpolicy_desc'] = 'Use standard password policy for enrolment keys.';
$string['welcometocourse'] = 'Welcome to {$a}';
$string['welcometocoursetext'] = 'Welcome to {$a->coursename}!

If you have not done so already, you should edit your profile page so that we can learn more about you:

  {$a->profileurl}';

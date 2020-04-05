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
 * Strings for component 'enrol_credit', language 'en'.
 *
 * @package    enrol_credit
 * @copyright  2018 bdecent gmbh <https://bdecent.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['canntenrol'] = 'Enrolment is disabled or inactive';
$string['canntenrolearly'] = 'You cannot enrol yet; enrolment starts on {$a}.';
$string['canntenrollate'] = 'You cannot enrol any more, since enrolment ended on {$a}.';
$string['checkout'] = '{$a->credit_cost} course credits will be deducted from your balance of {$a->user_credits}.';
$string['cohortnonmemberinfo'] = 'Only members of cohort \'{$a}\' can enrol.';
$string['cohortonly'] = 'Only cohort members';
$string['cohortonly_help'] = 'Enrolment may be restricted to members of a specified cohort only. Note that changing this setting has no effect on existing enrolments.';
$string['confirmbulkdeleteenrolment'] = 'Are you sure you want to delete these user enrolments?';
$string['credit_cost'] = 'Credit cost';
$string['credit_cost_help'] = 'The number of credits users will be deducted when enrolling.';
$string['credit:config'] = 'Configure course credit enrol instances';
$string['credit:holdkey'] = 'Appear as the course credit enrolment key holder';
$string['credit:manage'] = 'Manage enrolled users';
$string['credit:unenrol'] = 'Unenrol users from course';
$string['credit:unenrolself'] = 'Unenrol self from the course';
$string['customwelcomemessage'] = 'Custom welcome message';
$string['customwelcomemessage_help'] = 'A custom welcome message may be added as plain text or Moodle-auto format, including HTML tags and multi-lang tags.

The following placeholders may be included in the message:

* Course name {$a->coursename}
* Link to user\'s profile page {$a->profileurl}
* User email {$a->email}
* User fullname {$a->fullname}';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during enrolment';
$string['deleteselectedusers'] = 'Delete selected user enrolments';
$string['editselectedusers'] = 'Edit selected user enrolments';
$string['enrol_credit'] = 'Enrol with course credits';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can enrol themselves until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolme'] = 'Enrol me';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user enrols themselves. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can enrol themselves from this date onward only.';
$string['expiredaction'] = 'Enrolment expiry action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['expirymessageenrollersubject'] = 'Enrolment expiry notification';
$string['expirymessageenrollerbody'] = 'Enrolment in the course \'{$a->course}\' will expire within the next {$a->threshold} for the following users:

{$a->users}

To extend their enrolment, go to {$a->extendurl}';
$string['expirymessageenrolledsubject'] = 'Enrolment expiry notification';
$string['expirymessageenrolledbody'] = 'Dear {$a->user},

This is a notification that your enrolment in the course \'{$a->course}\' is due to expire on {$a->timeend}.

If you need help, please contact {$a->enroller}.';
$string['groupkey'] = 'Use group enrolment keys';
$string['groupkey_desc'] = 'Use group enrolment keys by default.';
$string['groupkey_help'] = 'In addition to restricting access to the course to only those who know the key, use of group enrolment keys means users are automatically added to groups when they enrol in the course.

Note: An enrolment key for the course must be specified in the enrolment settings as well as group enrolment keys in the group settings.';
$string['insufficient_credits'] = 'You have insufficient course credits to enroll. {$a->credit_cost} credits are required, your balance is {$a->user_credits}.';
$string['keyholder'] = 'You should have received this enrolment key from:';
$string['longtimenosee'] = 'Unenrol inactive after';
$string['longtimenosee_help'] = 'If users haven\'t accessed a course for a long time, then they are automatically unenrolled. This parameter specifies that time limit.';
$string['maxenrolled'] = 'Max enrolled users';
$string['maxenrolled_help'] = 'Specifies the maximum number of users that can enrol. 0 means no limit.';
$string['maxenrolledreached'] = 'Maximum number of users allowed to enrol was already reached.';
$string['messageprovider:expiry_notification'] = 'Course credit enrolment expiry notifications';
$string['newenrols'] = 'Allow new enrolments';
$string['newenrols_desc'] = 'Allow users to enrol into new courses by default.';
$string['newenrols_help'] = 'This setting determines whether a user can enrol into this course.';
$string['not_set'] = 'Not set';
$string['pluginname'] = 'Course credit enrolment';
$string['pluginname_desc'] = '';
$string['profile_field_map'] = 'Profile field mapping';
$string['profile_field_map_help'] = 'Select the profile field that stores course credits on user profiles.';
$string['purchase'] = 'Purchase';
$string['role'] = 'Default assigned role';
$string['sendcoursewelcomemessage'] = 'Send course welcome message';
$string['sendcoursewelcomemessage_help'] = 'When a user enrols in the course, they may be sent a welcome message email. If sent from the course contact (by default the teacher), and more than one user has this role, the email is sent from the first user to be assigned the role.';
$string['sendexpirynotificationstask'] = "Course credit enrolment send expiry notifications task";
$string['showhint'] = 'Show hint';
$string['showhint_desc'] = 'Show first letter of the guest access key.';
$string['status'] = 'Allow existing enrolments';
$string['status_desc'] = 'Enable course credit enrolment method in new courses.';
$string['status_help'] = 'If enabled together with \'Allow new enrolments\' disabled, only users who enrolled previously can access the course. If disabled, this enrolment method is effectively disabled, since all existing enrolments are suspended and new users cannot enrol.';
$string['syncenrolmentstask'] = 'Course credit enrolment synchronise enrolments task';
$string['unenrol'] = 'Unenrol user';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['unenroluser'] = 'Do you really want to unenrol "{$a->user}" from course "{$a->course}"?';
$string['unenrolusers'] = 'Unenrol users';
$string['welcometocourse'] = 'Welcome to {$a}';
$string['welcometocoursetext'] = 'Welcome to {$a->coursename}!

If you have not done so already, you should edit your profile page so that we can learn more about you:

  {$a->profileurl}';
$string['privacy:metadata'] = 'The Course Credit enrolment plugin does not store any personal data.';
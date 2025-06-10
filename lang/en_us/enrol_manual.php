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
 * Strings for component 'enrol_manual', language 'en_us', version '4.1'.
 *
 * @package     enrol_manual
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['confirmbulkdeleteenrolment'] = 'Are you sure you want to delete these users enrollments?
';
$string['defaultperiod'] = 'Default enrollment duration';
$string['defaultperiod_desc'] = 'Default length of time that the enrollment is valid. If set to zero, the enrollment duration will be unlimited by default.';
$string['defaultperiod_help'] = 'Default length of time that the enrollment is valid, starting with the moment the user is enrolled. If disabled, the enrollment duration will be unlimited by default.';
$string['defaultstart'] = 'Default enrollment start';
$string['deleteselectedusers'] = 'Delete selected user enrollments';
$string['editselectedusers'] = 'Edit selected user enrollments';
$string['enrolledincourserole'] = 'Enrolled in "{$a->course}" as "{$a->role}"';
$string['enrolusers'] = 'Enroll users';
$string['enroluserscohorts'] = 'Enroll selected users and cohorts';
$string['expiredaction'] = 'Enrollment expiration action';
$string['expiredaction_help'] = 'Select action to carry out when user enrollment expires. Please note that some user data and settings are purged from course during course unenrollment.';
$string['expirymessageenrolledbody'] = 'Dear {$a->user},

This is a notification that your enrollment in the course \'{$a->course}\' is due to expire on {$a->timeend}.

If you need help, please contact {$a->enroller}.';
$string['expirymessageenrolledsubject'] = 'Enrollment expiry notification';
$string['expirymessageenrollerbody'] = 'Enrollment in the course \'{$a->course}\' will expire within the next {$a->threshold} for the following users:

{$a->users}

To extend their enrollment, go to {$a->extendurl}';
$string['expirymessageenrollersubject'] = 'Enrollment expiry notification';
$string['manual:config'] = 'Configure manual enroll instances';
$string['manual:enrol'] = 'Enroll users';
$string['manual:manage'] = 'Manage user enrollments';
$string['manual:unenrol'] = 'Unenroll users from the course';
$string['manual:unenrolself'] = 'Unenroll self from the course';
$string['messageprovider:expiry_notification'] = 'Manual enrollment expiry notifications';
$string['pluginname'] = 'Manual enrollments';
$string['pluginname_desc'] = 'The manual enrollments plugin allows users to be enrolled manually via a link in the course administration settings, by a user with appropriate permissions such as a teacher. The plugin should normally be enabled, since certain other enrollment plugins, such as self enrollment, require it.';
$string['privacy:metadata'] = 'The Manual enrollments plugin does not store any personal data.';
$string['sendexpirynotificationstask'] = 'Manual enrollment send expiration notifications task';
$string['status'] = 'Enable manual enrollments';
$string['status_desc'] = 'Allow course access of internally enrolled users. This should be kept enabled in most cases.
';
$string['status_help'] = 'This setting determines whether users can be enrolled manually, via a link in the course administration settings, by a user with appropriate permissions such as a teacher.
';
$string['syncenrolmentstask'] = 'Manual enrollment synchronise enrollments task';
$string['unenrol'] = 'Unenroll user
';
$string['unenrolselectedusers'] = 'Unenroll selected users
';
$string['unenrolselfconfirm'] = 'Do you really want to unenroll yourself from course "{$a}"?';
$string['unenroluser'] = 'Do you really want to unenroll "{$a->user}" from course "{$a->course}"?';
$string['unenrolusers'] = 'Unenroll users';
$string['wscannotenrol'] = 'Plugin instance cannot manually enroll a user in the course id = {$a->courseid}';
$string['wsnoinstance'] = 'Manual enrollment plugin instance doesn\'t exist or is disabled for the course (id = {$a->courseid})';

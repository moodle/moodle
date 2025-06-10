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
 * Strings for component 'enrol', language 'en_us', version '4.1'.
 *
 * @package     enrol
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actenrolshhdr'] = 'Available course enrollment plugins';
$string['deleteinstanceconfirm'] = 'You are about to delete the enrollment method "{$a->name}". All {$a->users} users currently enrolled using this method will be unenrolled and any course-related data such as users\' grades, group membership or forum subscriptions will be deleted.

Are you sure you want to continue?';
$string['deleteinstancenousersconfirm'] = 'You are about to delete the enrollment method "{$a->name}". Are you sure you want to continue?';
$string['editenrolment'] = 'Edit enrollment
';
$string['edituserenrolment'] = 'Edit {$a}\'s enrollment';
$string['enrol'] = 'Enroll';
$string['enrolcandidates'] = 'Not enrolled users
';
$string['enrolcandidatesmatching'] = 'Matching not enrolled users
';
$string['enrolcohort'] = 'Enroll cohort';
$string['enrolcohortusers'] = 'Enroll users';
$string['enroldetails'] = 'Enrollment details';
$string['enrollednewusers'] = 'Successfully enrolled {$a} new users';
$string['enrolledusers'] = 'Enrolled users';
$string['enrolledusersmatching'] = 'Matching enrolled users
';
$string['enrolme'] = 'Enroll me in this course';
$string['enrolmentinstances'] = 'Enrollment methods';
$string['enrolmentmethod'] = 'Enrollment method';
$string['enrolmentnew'] = 'New enrollment in {$a}';
$string['enrolmentnewuser'] = '{$a->user} has enrolled in course "{$a->course}"';
$string['enrolmentoptions'] = 'Enrollment options';
$string['enrolments'] = 'Enrollments';
$string['enrolnotpermitted'] = 'You do not have permission or are not allowed to enroll someone in this course';
$string['enrolperiod'] = 'Enrollment duration';
$string['enroltimecreated'] = 'Enrollment created';
$string['enroltimeend'] = 'Enrollment ends';
$string['enroltimeendinvalid'] = 'Enrollment end date must be after the enrollment start date';
$string['enroltimestart'] = 'Enrollment starts';
$string['enrolusage'] = 'Instances / enrollments';
$string['enrolusers'] = 'Enroll users';
$string['enrolxusers'] = 'Enroll {$a} users';
$string['errajaxfailedenrol'] = 'Failed to enroll user';
$string['erroreditenrolment'] = 'An error occurred while trying to edit a users enrollment
';
$string['errorenrolcohort'] = 'Error creating cohort sync enrollment instance in this course.';
$string['errorenrolcohortusers'] = 'Error enrolling cohort members in this course.
';
$string['errorwithbulkoperation'] = 'There was an error while processing your bulk enrollment change.
';
$string['eventenrolinstancecreated'] = 'Enrollment instance created';
$string['eventenrolinstancedeleted'] = 'Enrollment instance deleted';
$string['eventenrolinstanceupdated'] = 'Enrollment instance updated';
$string['eventuserenrolmentcreated'] = 'User enrolled in course';
$string['eventuserenrolmentdeleted'] = 'User unenrolled from course';
$string['eventuserenrolmentupdated'] = 'User unenrollment updated';
$string['expirynotify'] = 'Notify before enrollment expires';
$string['expirynotify_help'] = 'This setting determines whether enrollment expiry notification messages are sent.';
$string['expirynotifyall'] = 'Enroller and enrolled user';
$string['expirynotifyenroller'] = 'Enroller only';
$string['expirynotifyhour'] = 'Hour to send enrollment expiry notifications';
$string['expirythreshold_help'] = 'How long before enrollment expiry should users be notified?';
$string['extremovedaction'] = 'External unenroll action';
$string['extremovedaction_help'] = 'Select action to carry out when user enrollment disappears from external enrollment source. Please note that some user data and settings are purged from course during course unenrollment.';
$string['extremovedkeep'] = 'Keep user enrolled';
$string['extremovedsuspend'] = 'Disable course enrollment';
$string['extremovedsuspendnoroles'] = 'Disable course enrollment and remove roles';
$string['extremovedunenrol'] = 'Unenroll user from course';
$string['finishenrollingusers'] = 'Finish enrolling users
';
$string['instanceeditselfwarningtext'] = 'You are enrolled into this course through this enrollment method, changes may affect your access to this course.';
$string['invalidenrolinstance'] = 'Invalid enrollment instance';
$string['manageenrols'] = 'Manage enroll plugins';
$string['migratetomanual'] = 'Migrate to manual enrollments';
$string['notenrollable'] = 'You can not enroll yourself in this course.';
$string['otheruserdesc'] = 'The following users are not enrolled in this course but do have roles, inherited or assigned within it.
';
$string['periodnone'] = 'enrolled {$a}';
$string['privacy:metadata:user_enrolments'] = 'Enrollments';
$string['privacy:metadata:user_enrolments:enrolid'] = 'The instance of the enroll plugin.';
$string['privacy:metadata:user_enrolments:modifierid'] = 'The ID of the user who last modified the user enrollment.';
$string['privacy:metadata:user_enrolments:status'] = 'The status of the user enrollment in a course.';
$string['privacy:metadata:user_enrolments:tableexplanation'] = 'This is where Enroll management stores enrolled users.';
$string['privacy:metadata:user_enrolments:timecreated'] = 'The date/time of when the user enrollment was created.';
$string['privacy:metadata:user_enrolments:timeend'] = 'The date/time of when the user enrollment ends.';
$string['privacy:metadata:user_enrolments:timemodified'] = 'The date/time of when the user enrollment was modified.';
$string['privacy:metadata:user_enrolments:timestart'] = 'The date/time of when the user enrollment starts.';
$string['testsettingsheading'] = 'Test enroll settings - {$a}';
$string['totalenrolledusers'] = '{$a} enrolled users';
$string['unenrol'] = 'Unenroll';
$string['unenrolconfirm'] = 'Do you really want to unenroll "{$a->user}" (previously enrolled via "{$a->enrolinstancename}") from "{$a->course}"?';
$string['unenrolme'] = 'Unenroll me from {$a}';
$string['unenrolnotpermitted'] = 'You do not have permission or can not unenroll this user from this course.';
$string['unenrolroleusers'] = 'Unenroll users';
$string['uninstallmigrating'] = 'Migrating "{$a}" enrollments';

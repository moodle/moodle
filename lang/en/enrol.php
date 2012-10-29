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
 * Strings for component 'core_enrol', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actenrolshhdr'] = 'Available course enrolment plugins';
$string['addinstance'] = 'Add method';
$string['ajaxoneuserfound'] = '1 user found';
$string['ajaxxusersfound'] = '{$a} users found';
$string['ajaxnext25'] = 'Next 25...';
$string['assignnotpermitted'] = 'You do not have permission or can not assign roles in this course.';
$string['bulkuseroperation'] = 'Bulk user operation';
$string['configenrolplugins'] = 'Please select all required plugins and arrange then in appropriate order.';
$string['custominstancename'] = 'Custom instance name';
$string['defaultenrol'] = 'Add instance to new courses';
$string['defaultenrol_desc'] = 'It is possible to add this plugin to all new courses by default.';
$string['deleteinstanceconfirm'] = 'You are about to delete the enrolment method "{$a->name}". All {$a->users} users currently enrolled using this method will be unenrolled and any course-related data such as users\' grades, group membership or forum subscriptions will be deleted.

Are you sure you want to continue?';
$string['deleteinstancenousersconfirm'] = 'You are about to delete the enrolment method "{$a->name}". Are you sure you want to continue?';
$string['durationdays'] = '{$a} days';
$string['enrol'] = 'Enrol';
$string['enrolcandidates'] = 'Not enrolled users';
$string['enrolcandidatesmatching'] = 'Matching not enrolled users';
$string['enrolcohort'] = 'Enrol cohort';
$string['enrolcohortusers'] = 'Enrol users';
$string['enrollednewusers'] = 'Successfully enrolled {$a} new users';
$string['enrolledusers'] = 'Enrolled users';
$string['enrolledusersmatching'] = 'Matching enrolled users';
$string['enrolme'] = 'Enrol me in this course';
$string['enrolmentinstances'] = 'Enrolment methods';
$string['enrolmentnew'] = 'New enrolment in {$a}';
$string['enrolmentnewuser'] = '{$a->user} has enrolled in course "{$a->course}"';
$string['enrolments'] = 'Enrolments';
$string['enrolmentoptions'] = 'Enrolment options';
$string['enrolnotpermitted'] = 'You do not have permission or are not allowed to enrol someone in this course';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolusage'] = 'Instances / enrolments';
$string['enrolusers'] = 'Enrol users';
$string['enroltimecreated'] = 'Enrolment created';
$string['enroltimeend'] = 'Enrolment ends';
$string['enroltimestart'] = 'Enrolment starts';
$string['errajaxfailedenrol'] = 'Failed to enrol user';
$string['errajaxsearch'] = 'Error when searching users';
$string['erroreditenrolment'] = 'An error occurred while trying to edit a users enrolment';
$string['errorenrolcohort'] = 'Error creating cohort sync enrolment instance in this course.';
$string['errorenrolcohortusers'] = 'Error enrolling cohort members in this course.';
$string['errorthresholdlow'] = 'Notification threshold must be at least 1 day.';
$string['errorwithbulkoperation'] = 'There was an error while processing your bulk enrolment change.';
$string['expirynotify'] = 'Notify before enrolment expires';
$string['expirynotify_help'] = 'This setting determines whether enrolment expiry notification messages are sent.';
$string['expirynotifyall'] = 'Enroller and enrolled user';
$string['expirynotifyenroller'] = 'Enroller only';
$string['expirynotifyhour'] = 'Hour to send enrolment expiry notifications';
$string['expirythreshold'] = 'Notification threshold';
$string['expirythreshold_help'] = 'How long before expiration should be users notified?';
$string['finishenrollingusers'] = 'Finish enrolling users';
$string['invalidenrolinstance'] = 'Invalid enrolment instance';
$string['invalidrole'] = 'Invalid role';
$string['manageenrols'] = 'Manage enrol plugins';
$string['manageinstance'] = 'Manage';
$string['nochange'] = 'No change';
$string['noexistingparticipants'] = 'No existing participants';
$string['noguestaccess'] = 'Guests can not access this course, please try to log in.';
$string['none'] = 'None';
$string['notenrollable'] = 'You can not enrol yourself in this course.';
$string['notenrolledusers'] = 'Other users';
$string['otheruserdesc'] = 'The following users are not enrolled in this course but do have roles, inherited or assigned within it.';
$string['participationactive'] = 'Active';
$string['participationstatus'] = 'Status';
$string['participationsuspended'] = 'Suspended';
$string['periodend'] = 'until {$a}';
$string['periodnone'] = 'enrolled {$a}';
$string['periodstart'] = 'from {$a}';
$string['periodstartend'] = 'from {$a->start} until {$a->end}';
$string['recovergrades'] = 'Recover user\'s old grades if possible';
$string['rolefromthiscourse'] = '{$a->role} (Assigned in this course)';
$string['rolefrommetacourse'] = '{$a->role} (Inherited from parent course)';
$string['rolefromcategory'] = '{$a->role} (Inherited from course category)';
$string['rolefromsystem'] = '{$a->role} (Assigned at site level)';
$string['startdatetoday'] = 'Today';
$string['synced'] = 'Synced';
$string['totalenrolledusers'] = '{$a} enrolled users';
$string['totalotherusers'] = '{$a} other users';
$string['unassignnotpermitted'] = 'You do not have permission to unassign roles in this course';
$string['unenrol'] = 'Unenrol';
$string['unenrolconfirm'] = 'Do you really want to unenrol user "{$a->user}" from course "{$a->course}"?';
$string['unenrolme'] = 'Unenrol me from {$a}';
$string['unenrolnotpermitted'] = 'You do not have permission or can not unenrol this user from this course.';
$string['unenrolroleusers'] = 'Unenrol users';
$string['uninstallconfirm'] = 'You are about to uninstall the enrolment plugin \'{$a}\'. This will result in the deletion of all data associated with this enrolment type, including users\' grades, group membership, forum subscriptions and any other course-related data.

Are you SURE you want to continue?';
$string['uninstalldelete'] = 'Delete all enrolments and uninstall';
$string['uninstalldeletefiles'] = 'All data associated with the enrol plugin \'{$a->plugin}\' has been deleted from the database.  To complete the deletion (and prevent the plugin re-installing itself), you should now delete this directory from your server: {$a->directory}';
$string['uninstallmigrate'] = 'Uninstall but keep all enrolments';
$string['uninstallmigrating'] = 'Migrating "{$a}" enrolments';
$string['unknowajaxaction'] = 'Unknown action requested';
$string['unlimitedduration'] = 'Unlimited';
$string['usersearch'] = 'Search ';
$string['withselectedusers'] = 'With selected users';
$string['extremovedaction'] = 'External unenrol action';
$string['extremovedaction_help'] = 'Select action to carry out when user enrolment disappears from external enrolment source. Please note that some user data and settings are purged from course during course unenrolment.';
$string['extremovedsuspend'] = 'Disable course enrolment';
$string['extremovedsuspendnoroles'] = 'Disable course enrolment and remove roles';
$string['extremovedkeep'] = 'Keep user enrolled';
$string['extremovedunenrol'] = 'Unenrol user from course';

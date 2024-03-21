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
 * @package    core_enrol
 * @subpackage enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actenrolshhdr'] = 'Available course enrolment plugins';
$string['addinstance'] = 'Add method';
$string['addinstanceanother'] = 'Add method and create another';
$string['ajaxoneuserfound'] = '1 user found';
$string['ajaxxusersfound'] = '{$a} users found';
$string['ajaxxmoreusersfound'] = 'More than {$a} users found';
$string['ajaxnext25'] = 'Next 25...';
$string['assignnotpermitted'] = 'You do not have permission or can not assign roles in this course.';
$string['bulkuseroperation'] = 'Bulk user operation';
$string['configenrolplugins'] = 'Please select all required plugins and arrange then in appropriate order.';
$string['custominstancename'] = 'Custom instance name';
$string['defaultenrol'] = 'Add instance to new courses';
$string['defaultenrol_desc'] = 'It is possible to add this plugin to all new courses by default.';
$string['deleteinstanceconfirm'] = 'You are about to delete the enrolment method "{$a->name}". All {$a->users} users currently enrolled using this method will be unenrolled and any course-related data such as users\' grades, group membership or forum subscriptions will be deleted.

Are you sure you want to continue?';
$string['deleteinstanceconfirmself'] = 'Are you really sure you want to delete instance "{$a->name}" that gives you access to this course? It is possible that you will not be able to access this course if you continue.';
$string['deleteinstancenousersconfirm'] = 'You are about to delete the enrolment method "{$a->name}". Are you sure you want to continue?';
$string['disableinstanceconfirmself'] = 'Are you really sure you want to disable instance "{$a->name}" that gives you access to this course? It is possible that you will not be able to access this course if you continue.';
$string['durationdays'] = '{$a} days';
$string['editenrolment'] = 'Edit enrolment';
$string['edituserenrolment'] = 'Edit {$a}\'s enrolment';
$string['enrol'] = 'Enrol';
$string['enrolcandidates'] = 'Not enrolled users';
$string['enrolcandidatesmatching'] = 'Matching not enrolled users';
$string['enrolcohort'] = 'Enrol cohort';
$string['enrolcohortusers'] = 'Enrol users';
$string['enroldetails'] = 'Enrolment details';
$string['eventenrolinstancecreated'] = 'Enrolment instance created';
$string['eventenrolinstancedeleted'] = 'Enrolment instance deleted';
$string['eventenrolinstanceupdated'] = 'Enrolment instance updated';
$string['enrollednewusers'] = 'Successfully enrolled {$a} new users';
$string['enrolledusers'] = 'Enrolled users';
$string['enrolledusersmatching'] = 'Matching enrolled users';
$string['enrolme'] = 'Enrol me in this course';
$string['enrolment'] = 'Enrolment';
$string['enrolmentinstances'] = 'Enrolment methods';
$string['enrolmentnew'] = 'New enrolment in {$a}';
$string['enrolmentnewuser'] = '{$a->user} has enrolled in course "{$a->course}"';
$string['enrolmentmethod'] = 'Enrolment method';
$string['enrolments'] = 'Enrolments';
$string['enrolmentoptions'] = 'Enrolment options';
$string['enrolmentupdatedforuser'] = 'The enrolment for user "{$a->fullname}" has been updated';
$string['enrolnotpermitted'] = 'You do not have permission or are not allowed to enrol someone in this course';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolusage'] = 'Instances / enrolments';
$string['enrolusers'] = 'Enrol users';
$string['enrolxusers'] = 'Enrol {$a} users';
$string['enroltimecreated'] = 'Enrolment created';
$string['enroltimeend'] = 'Enrolment ends';
$string['enroltimeendinvalid'] = 'Enrolment end date must be after the enrolment start date';
$string['enroltimestart'] = 'Enrolment starts';
$string['errajaxfailedenrol'] = 'Failed to enrol user';
$string['errajaxsearch'] = 'Error when searching users';
$string['erroreditenrolment'] = 'An error occurred while trying to edit a users enrolment';
$string['errorenrolcohort'] = 'Error creating cohort sync enrolment instance in this course.';
$string['errorenrolcohortusers'] = 'Error enrolling cohort members in this course.';
$string['errorthresholdlow'] = 'Notification threshold must be at least 1 day.';
$string['errorwithbulkoperation'] = 'There was an error while processing your bulk enrolment change.';
$string['eventuserenrolmentcreated'] = 'User enrolled in course';
$string['eventuserenrolmentdeleted'] = 'User unenrolled from course';
$string['eventuserenrolmentupdated'] = 'User enrolment updated';
$string['expirynotify'] = 'Notify before enrolment expires';
$string['expirynotify_help'] = 'This setting determines whether enrolment expiry notification messages are sent.';
$string['expirynotifyall'] = 'Enroller and enrolled user';
$string['expirynotifyenroller'] = 'Enroller only';
$string['expirynotifyhour'] = 'Hour to send enrolment expiry notifications';
$string['expirythreshold'] = 'Notification threshold';
$string['expirythreshold_help'] = 'How long before enrolment expiry should users be notified?';
$string['finishenrollingusers'] = 'Finish enrolling users';
$string['foundxcohorts'] = 'Found {$a} cohorts';
$string['instanceadded'] = 'Method added';
$string['instanceeditselfwarning'] = 'Warning:';
$string['instanceeditselfwarningtext'] = 'You are enrolled into this course through this enrolment method, changes may affect your access to this course.';
$string['invalidenrolinstance'] = 'Invalid enrolment instance';
$string['invalidenrolduration'] = 'Invalid enrolment duration';
$string['invalidrole'] = 'Invalid role';
$string['invalidrequest'] = 'Invalid request';
$string['manageenrols'] = 'Manage enrol plugins';
$string['manageinstance'] = 'Manage';
$string['method'] = 'Method';
$string['migratetomanual'] = 'Migrate to manual enrolments';
$string['nochange'] = 'No change';
$string['noexistingparticipants'] = 'No existing participants';
$string['nogroup'] = 'No group';
$string['noguestaccess'] = 'Guests cannot access this course. Please log in.';
$string['none'] = 'None';
$string['notenrollable'] = 'You cannot enrol yourself in this course.';
$string['notenrolledusers'] = 'Other users';
$string['otheruserdesc'] = 'The following users are not enrolled in this course but do have roles, inherited or assigned within it.';
$string['participationactive'] = 'Active';
$string['participationnotcurrent'] = 'Not current';
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
$string['sendfromcoursecontact'] = 'From the course contact';
$string['sendfromkeyholder'] = 'From the key holder';
$string['sendfromnoreply'] = 'From the no-reply address';
$string['startdatetoday'] = 'Today';
$string['synced'] = 'Synced';
$string['testsettings'] = 'Test settings';
$string['testsettingsheading'] = 'Test enrol settings - {$a}';
$string['timeended'] = 'Time ended';
$string['timeenrolled'] = 'Time enrolled';
$string['timereaggregated'] = 'Time reaggregated';
$string['timestarted'] = 'Time started';
$string['totalenrolledusers'] = '{$a} enrolled users';
$string['totalunenrolledusers'] = '{$a} unenrolled users';
$string['totalotherusers'] = '{$a} other users';
$string['unassignnotpermitted'] = 'You do not have permission to unassign roles in this course';
$string['unenrol'] = 'Unenrol';
$string['unenrolleduser'] = 'The user "{$a->fullname}" was unenrolled from the course';
$string['unenrolconfirm'] = 'Do you really want to unenrol "{$a->user}" (previously enrolled via "{$a->enrolinstancename}") from "{$a->course}"?';
$string['unenrolme'] = 'Unenrol me from this course';
$string['unenrolnotpermitted'] = 'You do not have permission to unenrol this user from the course.';
$string['unenrolroleusers'] = 'Unenrol users';
$string['uninstallmigrating'] = 'Migrating "{$a}" enrolments';
$string['unknowajaxaction'] = 'Unknown action requested';
$string['unlimitedduration'] = 'Unlimited';
$string['userremovedfromselectiona'] = 'User "{$a}" was removed from the selection.';
$string['usersearch'] = 'Search ';
$string['withselectedusers'] = 'With selected users';
$string['extremovedaction'] = 'External unenrol action';
$string['extremovedaction_help'] = 'Select action to carry out when user enrolment disappears from external enrolment source. Please note that some user data and settings are purged from course during course unenrolment.';
$string['extremovedsuspend'] = 'Disable course enrolment';
$string['extremovedsuspendnoroles'] = 'Disable course enrolment and remove roles';
$string['extremovedkeep'] = 'Keep user enrolled';
$string['extremovedunenrol'] = 'Unenrol user from course';
$string['privacy:metadata:user_enrolments'] = 'Enrolments';
$string['privacy:metadata:user_enrolments:enrolid'] = 'The instance of the enrolment plugin';
$string['privacy:metadata:user_enrolments:modifierid'] = 'The ID of the user who last modified the user enrolment';
$string['privacy:metadata:user_enrolments:status'] = 'The status of the user enrolment in a course';
$string['privacy:metadata:user_enrolments:tableexplanation'] = 'The core enrol plugin stores enrolled users.';
$string['privacy:metadata:user_enrolments:timecreated'] = 'The time when the user enrolment was created';
$string['privacy:metadata:user_enrolments:timeend'] = 'The time when the user enrolment ends';
$string['privacy:metadata:user_enrolments:timestart'] = 'The time when the user enrolment starts';
$string['privacy:metadata:user_enrolments:timemodified'] = 'The time when the user enrolment was modified';
$string['privacy:metadata:user_enrolments:userid'] = 'The ID of the user';
$string['youenrolledincourse'] = 'You are enrolled in the course.';
$string['youunenrolledfromcourse'] = 'You are unenrolled from the course "{$a}".';

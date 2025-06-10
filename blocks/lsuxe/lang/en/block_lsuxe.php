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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

// Link to get back.
$string['lsuxe_link_back_title'] = '<a href="'.$CFG->wwwroot.'/blocks/lsuxe/lsuxe.php">Back to XE Dashboard</a>';

// Block.
$string['pluginname'] = 'Cross Enrollment Tool';
$string['foldername'] = 'Cross Enrollments';
$string['adminname'] = 'Manage Cross Enrollments';
$string['settings'] = 'Cross Enrollments';
$string['reprocess'] = 'Reprocess';

// Tasks.
$string['lsuxe_courses'] = 'Fetch Remote Courseids';
$string['lsuxe_groups'] = 'Fetch Remote Groupids';
$string['lsuxe_users'] = 'Verify and Create Remote Users';
$string['lsuxe_enroll'] = 'Basic LSU Cross Enrollment';
$string['lsuxe_full_enroll'] = 'FULL LSU Cross Enrollment';

// Capabilities.
$string['lsuxe:admin'] = 'Administer the Cross Enrollment system.';
$string['lsuxe:addinstance'] = 'Add a new Cross Enrollment block to a course page';
$string['lsuxe:myaddinstance'] = 'Add a new Cross Enrollment block to the /my page';

// General Terms.
$string['backtocourse'] = 'Back to course';
$string['backtohome'] = 'Back to home';

// Configuration.
$string['manage_overrides'] = 'Manage overrides';
$string['xe_roles_title'] = 'Default Remote Role IDs';
$string['xe_studentroleid'] = 'Remote student roleid';
$string['xe_studentroleid_help'] = 'The default student role id for the role you wish to assign "students" to in the remote Moodle instance.';
$string['xe_teacherroleid'] = 'Remote teacher roleid';
$string['xe_teacherroleid_help'] = 'The default teacher role id for the role you wish to assign "teachers" to in the remote Moodle instance.';

// Links.
$string['xedashboard'] = 'XE Dashboard';
$string['dashboard'] = 'Dashboard';
$string['mappings'] = 'Mappings';
$string['mappings_view'] = 'Mappings - View';
$string['mappings_create'] = 'Mappings - Create';

$string['token'] = 'Token';
$string['tokens'] = 'Tokens';
$string['tokenexpiration'] = 'Token Expiration Date';
$string['tokens_view'] = 'Tokens - View';
$string['tokens_create'] = 'Tokens - Create';
$string['manage_tokens'] = 'Manage Web Service Tokens';

$string['moodles'] = 'Moodles';
$string['moodlesurl'] = 'Moodle URL';
$string['moodles_view'] = 'Moodles - View';
$string['moodles_create'] = 'Moodles - Create';

// Archives Page.
$string['archives'] = 'Archives';
$string['deletearchive'] = 'This mapping has been removed.';
$string['recoverarchive'] = 'The mapping has been recovered.';
$string['recovernow'] = 'Recover Mapping';
$string['noarchives'] = 'Nothing has been archived yet.';

// Forms New Mappings.
$string['newmapping'] = 'Create New Mapping';
$string['newmoodle'] = 'Create New Instance';
$string['updatemapping'] = 'Update Mapping';
$string['updatemoodle'] = 'Update Instance';
$string['srccourseshortname'] = 'Source Course Shortname';
$string['srccoursegroupname'] = 'Source Group Shortname';
$string['coursestarttime'] = 'Course Start Time';
$string['courseendtime'] = 'Course End Time';
$string['destmoodleinstance'] = 'Destination Moodle Instance';
$string['destcourseshortname'] = 'Destination Course Shortname';
$string['destcoursegroupname'] = 'Destination Course Group Prefix';
$string['courseupdateinterval'] = 'Course Update Interval';
$string['defaultupdateinterval'] = 'Default Update Interval';
$string['authmethod'] = 'Authentication for this Moodle instance.';
$string['updateinterval'] = 'Update Interval';
$string['updatenow'] = 'Update Now';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['nomappings'] = 'No Mappings to view';
$string['manualgroupentry'] = 'Click manually enter a group name.';
$string['enrolenddate'] = 'Enrolment End Date.';

// Forms New Moodles.
$string['instanceurl'] = 'Moodle Instance URL';
$string['instancetoken'] = 'Moodle Instance Token';
$string['tokenenable'] = 'Enable';
$string['nomoodles'] = 'No Moodle instances to view';
$string['mappingslinked'] = 'Mappings Linked';

// Buttons.
$string['savechanges'] = 'Save Changes';
$string['cancel'] = 'Cancel';
$string['savemapping'] = 'Save Course Mapping';
$string['saveinstance'] = 'Save Moodle Instance';
$string['verifysrccourse'] = 'Verify Source Course';
$string['verifydestcourse'] = 'Verify Destination Course';
$string['verifyinstance'] = 'Verify Moodle Instance';
$string['addnewmapping'] = 'Add New XE Mapping';
$string['addnewmapping'] = 'Add New Moodle Instance';

// Notifications & Errors.
$string['notice'] = 'Notice!';
$string['noticesub'] = 'We are unable to run enrollment for an entire instance in a browser window, so it has been scheduled to run at the very next opportunity.';
$string['verificationfail'] = 'Verification Failure!';
$string['verificationfailsub'] = 'Make sure the url and token are correct. Please verify the remote Moodle instance token is correct and any restrictions are properly reflected above.';
$string['verificationsuccess'] = 'Verification Success!';
$string['verified'] = 'Verified';
$string['creatednewmapping'] = 'The new mapping has been created';
$string['creatednewmoodle'] = 'The new moodle instance has been created';
$string['updatedmapping'] = 'The mapping has been updated';
$string['updatedmoodle'] = 'The moodle instance has been updated';
$string['deletemapping'] = 'The mapping has been removed';
$string['deletemoodle'] = 'The moodle instance has been removed';
$string['mappingsformcourseerror'] = 'The course you have entered cannot be found, please try again.';
$string['mappingsformgrouperror'] = 'The group you have entered cannot be found, please try again.';

// Validation.
$string['srccourseshortnameverify'] = 'Please include a course shortname.';
$string['srccoursegroupnameverify'] = 'Please include a course group name.';
$string['destcourseshortnameverify'] = 'Please include a destination course shortname.';
$string['destcoursegroupnameverify'] = 'Please include a destination course group prefix.';
$string['instanceurlverify'] = 'A Moodle URL is required.';
$string['instancetokenverify'] = 'A Moodle token is required from the destination instance.';

// Settings.
$string['xe_interval_main_title'] = 'Update Interval Times.';
$string['xe_interval_list'] = 'List of intervals to choose from in a matching format.';
$string['xe_auth_method_title'] = 'List of authentication methods.';
$string['xe_auth_method_hint'] = '(each on it\'s own line)';
$string['xe_experimental_title'] = 'Experimental Settings.';
$string['xe_form_auto_enable'] = 'Enable Form Autocompletion.';
$string['xe_form_auto_enable_desc'] = 'This will use core ajax features and webservices to fetch data and auto populate the form fields to make the form easy to use while reducing potential typing errors. WARNING: ajax calls may fail.';
$string['xe_form_enable_dest_source_test'] = 'Enable Verify Destination.';
$string['xe_form_enable_dest_source_test_desc'] = 'This is currently in development and is designed to verify that the remote server is ready when creating new mappings or new moodle instances.';
$string['xe_form_enable_wide_view'] = 'Enable Wide View';
$string['xe_form_enable_wide_view_desc'] = 'This will expand the view of Mappings and Moodles to the full width of the screen.';

// Reprocessing.
$string['reprocess_course'] = 'Reprocess Course';
$string['reprocess_moodle'] = 'Reprocess Moodle Instance';
$string['xebacktocourse'] = 'Back to course';
$string['xebacktomoodle'] = 'Back to XE dashboard';

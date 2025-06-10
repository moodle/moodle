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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Course Preferences';
$string['pluginname_desc'] = 'The Course Preferences block allows instructors to
control course creation and enrollment behavior. These are system wide defaults for
those who do not actually set any data.';

$string['cps:myaddinstance'] = 'Add Course Preferences System Block to the My page';
$string['cps:addinstance'] = 'Add Course Preferences System Block';

$string['course_threshold'] = 'Course Number Threshold';
$string['course_threshold_desc'] = 'Sections belonging to a course number that
is greater than or equal to the specified number will not be initially created.
CPS will create unwanted entries for these sections so the instructor can opted
in teaching online.';

$string['course_severed'] = 'Delete severed Courses';
$string['course_severed_desc'] = 'A course is severed if the Moodle course will
no longer be handled by the enrollment module, or if enrollment equals zero.';

$string['enabled'] = 'Enabled';
$string['enabled_desc'] = 'If disabled, the setting will be hidden from the
instructor. A Moodle admin who is logged in as the instructor will still be able to
see and manipulate the disabled setting.';

$string['nonprimary'] = 'Allow Non-Primaries';
$string['nonprimary_desc'] = 'If checked, then Non-Primaries will be able to
configure the CPS settings.';

$string['department'] = 'Department';
$string['cou_number'] = 'Course Number';

$string['material_shortname'] = 'Blueprint Course {department} {course_number} for {fullname}';

$string['setting'] = 'User preferences';
$string['setting_help'] = 'Faculty are allowed to change their first name to a preferred name. This change will be permanent until otherwise specified.';
$string['notice'] = 'Users are allowed to change their first name to a preferred name <strong>only once</strong>.<br />Your preferred name can then only be reset or edited by a Moodle administrator.<br />Please contact the help desk for additional assistance with preferred name.';
$string['user_firstname'] = 'Preferred firstname';
$string['settings_changessaved'] = 'Your changes have been saved.<br />Please log out and back in to see any name changes.';
$string['grade_restore'] = 'Reset restored grade items';
$string['grade_restore_help'] = 'If checked, grade items restored from a backup
will reset offset and curve-to values, rather than pulling these values from
the archive.';

$string['no_filters'] = 'Provide at least one field to query.';
$string['no_results'] = 'No results.';

$string['team_request_limit'] = 'Number of Requests';
$string['team_request_limit_desc'] = 'This is the the maximum number of requests a primary instructor can make (minimum of 1).';

// Error Strings.
$string['not_enabled'] = 'CPS Setting <strong>{$a}</strong> is not enabled.';
$string['not_teacher'] = 'You are not enrolled or set to be enrolled in any course.
If you believe that you should be, please contact the Moodle administrator for
immediate assistance.';
$string['no_section'] = 'You do not own any section in the capable role. If you
 believe that you do, please contact the Moodle administrator for immediate assistance.';

$string['no_courses'] = 'You do not have any courses with at least two active
sections.';

$string['err_enrol_days'] = 'Enrollments days cannot be >= Create Days.';
$string['err_number'] = 'Days entered must be greater than 0.';
$string['err_numeric'] = 'Days entered must be numeric.';
$string['err_both_empty'] = 'Both fields are required.';

$string['err_manage_one'] = 'You must select at least one request.';

$string['err_select'] = 'The selected course does not exist.';
$string['err_split_number'] = 'The selected course does not have two sections.';
$string['err_select_one'] = 'You must select a course to continue.';

$string['err_same_semester'] = 'You must select courses in the same semester.
You first selected {$a->year} {$a->name}';
$string['err_not_enough'] = 'You must select at least two courses.';
$string['err_one_shell'] = 'Each shell must have two sections.';

$string['err_team_query'] = 'Please provide both fields.';
$string['err_team_query_course'] = 'Course does not exists:
{$a->department} {$a->cou_number}';

$string['err_team_query_sections'] = '{$a->year} {$a->name} {$a->department}
{$a->cou_number} does not have sections';

$string['err_select_teacher'] = 'You must select at least one Instructor';

// Setting names.
$string['default_settings'] = 'Default Settings';
$string['creation'] = 'Creation / Enrollment';
$string['creation_help'] = 'Creation and Enrollment settings allow instructors
to specify when an online course is created and enrolled in Moodle. In addition
to when the course is created, the instructor can also decide _how_ the course
is created:

- Whether it is available to students or not
- What course format to use
- How many topics or weeks a course should be created with
- Whether or not to include activity completion
';

$string['creation_settings'] = 'Course Creation Settings';

$string['create_days'] = 'Days before Creation';
$string['create_days_desc'] = 'The number of days before sections are created.';

$string['enroll_days'] = 'Days before Enrollment';
$string['enroll_days_desc'] = 'The number of days before **created** sections
are enrolled.';

$string['default_create_days'] = 'Days before classes to create courses';
$string['default_enroll_days'] = 'Days before classes to enroll students';

$string['use_defaults'] = 'Use system defaults';

$string['unwant'] = 'Unwanted';
$string['unwant_help'] = 'Unwanted sections will be removed from Moodle. Undoing
an unwanted selection will re-enroll and/or re-create the sections in Moodle.';

$string['material'] = 'Blueprint Course';
$string['material_help'] = 'A _Blueprint Course_ is a Moodle course designated to store
course materials for selected courses. These created courses will __not__
contain student enrollment.';

$string['creating_materials'] = 'Create blueprint courses';

$string['split'] = 'Splitting';
$string['split_help'] = 'Splitting allows an instructor to separate online courses
with two or more sections into multiple online courses. This is especially useful
for separating the gradebook and other activities.';

$string['select'] = 'Select a course';
$string['shells'] = 'Course Shells';
$string['decide'] = 'Separate Sections';
$string['confirm'] = 'Review';
$string['update'] = 'Update';
$string['loading'] = 'Applying';

$string['split_how_many'] = 'How many separate course shells would you like to have created?';
$string['split_how_many_help'] = 'A _course shell_ is a Moodle course that encapsulates one or more sections.
For example: If you were splitting a course with three sections, you may decide to make
two _course shells_, one containing one section, and the other containing two. In most cases,
the number of _course shells_ is limited to the number of sections within a course.';
$string['split_autopop'] = 'Do you want to automatically assign sections to course shells using generic shell names?';
$string['split_autopop_help'] = 'When you have the same number of sections and available course shells,
you may choose to automatically assign sections to course shells.  If you do, each section will be assigned
to the next available course shell in turn, and a generic name will be given to each course shell by inserting
\'Course #\' into the original course\'s full name for each section #.  If you don\'t, each course shell will have
a customizable course shell name, and you will get a screen with a box for each course shell which you must use to
choose one section for each shell.';
$string['next'] = 'Next';
$string['back'] = 'Back';

$string['split_processed'] = 'Split Courses Processed';
$string['split_thank_you'] = 'Your split selections have been processed. Continue
to head back to the split home screen.';

$string['chosen'] = 'Please review your selections.';
$string['available_sections'] = 'Your Sections:';
$string['move_left'] = '<';
$string['move_right'] = '>';
$string['split_option_taken'] = 'Split option taken';
$string['split_updating'] = 'Updating your split selections';
$string['split_undo'] = 'Undo these courses?';
$string['split_reshell'] = 'Reassign the number of shells?';
$string['split_rearrange'] = 'Rearrange sections?';

$string['customize_name'] = 'Customize name';

$string['shortname_desc'] = 'Split course creation uses these defaults.';
$string['split_shortname'] = '{year} {name}{session} {department} {course_number} {shell_name} for {fullname}';

$string['crosslist'] = 'Cross-listing';
$string['crosslist_help'] = 'Cross-listing enables an instructor to combine
sections from different multiple courses into a __single online course__.';

$string['crosslist_shortname'] = '{year} {name}{session} {shell_name} for {fullname}';
$string['crosslist_you_have'] = 'You have selected to cross-list';

$string['crosslist_option_taken'] = 'Cross-list option taken';
$string['crosslist_no_option'] = '(No option taken)';

$string['crosslist_updating'] = 'Updating your cross-list selections';
$string['crosslisted'] = 'is cross-listed into <strong>{$a->shell_name}</strong>';

$string['crosslist_processed'] = 'Cross-list Courses Processed';

$string['crosslist_thank_you'] = 'Your cross-list courses have been processed. Continue
to head back to the cross-list home screen.';

$string['crosslist_select'] = 'Select courses to Cross-list';

// Team Requests.
$string['team_request'] = 'Team Teach Requests';
$string['team_request_help'] = '
Team teach requests are made by an instructor who wants to _Team teach_ with
one or multiple instructors. There is one request for each department and course number desired.
For example: to teach teach with one or more instructors in BIOL 1201, then only one request is
required.';

$string['team_query_for'] = 'Query course: {$a->year} {$a->name} {$a->session_key}';

$string['team_teachers'] = 'Select one or more Instructors';
$string['team_teachers_help'] = 'Sometimes multiple instructors teach in the
same department and course number. Based on your previous entries, the dropdown
contains a list of instructors that teach sections in those courses. You are
not limited to the number of instructors you invite, though an invited instructor
is free to reject your invitation.';

$string['review_selection'] = 'Please reivew your selections';

$string['team_note'] = '<strong>Note</strong>';
$string['team_going_email'] = 'The instructors you have selected will receive
an email from you, inviting them to team teach. You can revoke team teach
privileges at any time.';

$string['team_how_many'] = 'How many courses will you combine with this course?';
$string['team_how_many_help'] = 'This number represents how many different
course departments and numbers you are combining. In the next screen you will
decide which courses.';

$string['team_request_option'] = 'Team Teach option taken';

$string['team_section'] = 'Course Sections';

$string['team_continue_build'] = 'Continue to configure sections.';

$string['team_section_note'] = 'You must wait until the owner of this request
has created shells to work in.';

$string['team_section_no_permission'] = 'You do not have permission to change
this section. You can only move the sections you own.';

$string['team_section_finished'] = 'Team Sections Processed';
$string['team_section_processed'] = 'The Team Teach Sections have been processed.
Continue to head back to the Team Section home.';

$string['team_section_option'] = '(Team Section option taken)';
$string['team_section_yours'] = '(Yours)';

$string['team_request_shells'] = 'Course Requests';
$string['query'] = 'Query a Course';
$string['request'] = 'Select Instructor';
$string['review'] = 'Review Requests';
$string['team_request_finish'] = 'Request Sent';
$string['team_request_update'] = 'Updating';
$string['team_request_confirm'] = 'Confirm Actions';

$string['manage'] = 'Manage Requests';

$string['team_following'] = 'The current requests';
$string['team_approved'] = 'Approved';
$string['team_not_approved'] = 'Not Approved';
$string['team_current'] = 'Manage invites to current courses';
$string['team_add_course'] = 'Make additional requests';
$string['team_manage_requests'] = 'Manage Requests';
$string['team_manage_sections'] = 'Manage Sections';
$string['team_manage_sections_help'] = 'Once a _Team teach_ request has been
successfully approved by an invited instructor, the inviting instructor can
now specify how the online course is built. The inviting instructor has full
control over the number of course shells that is represented by the _Team teach_ request.


__Note__: All invited instructors must wait to participate until the inviting instructor has determined how the course is built.';

$string['team_to_approve'] = 'Requests to Approve';
$string['team_to_revoke'] = 'Requests to Cancel';

$string['team_revoke'] = 'Revoke';
$string['team_approve'] = 'Approve';
$string['team_do_nothing'] = 'Do Nothing';
$string['team_deny'] = 'Deny';
$string['team_cancel'] = 'Leave Team Teach';
$string['team_actions'] = 'Actions';
$string['team_requested_courses'] = 'Requested Courses';

$string['team_reshell'] = 'How many courses to add?';

$string['team_request_thank_you'] = 'The Team Teach requests have been processed
and sent. Continue to head back to the team teach home.';

$string['team_with'] = 'to be team taught with...';

$string['team_request_shortname'] = '{year} {name}{session} {shell_name}';

$string['team_request_approved_subject'] = 'Moodle Team-Teaching Request Accepted';
$string['team_request_approved_body'] = '
{$a->requester},

{$a->requestee} has accepted your invitation to team-teach your {$a->course}
with his/her {$a->other_course} course.  All instructors and students of
{$a->other_course} will be enrolled within your {$a->course} course.';

$string['team_request_invite_subject'] = 'Moodle Team-Teaching Request';
$string['team_request_invite_body'] = '
{$a->requestee},

{$a->requester} has invited you and your students from your {$a->other_course}
course to participate in a team-taught course with his/her {$a->course}
course. If you accept this invitation, you and your students will be added
and you will be made a primary instructor.

Please click the following link to accept or reject {$a->requester}\'s request:
{$a->link}';

$string['team_request_reject_subject'] = 'Moodle Team-Teaching Request Rejected';
$string['team_request_reject_body'] = '
{$a->requester},

{$a->requestee} has rejected your invitation to team-teach your {$a->course}
course with his/her {$a->other_course} course.';

$string['team_request_revoke_subject'] = 'Moodle Team-Teaching Request Revoked';
$string['team_request_revoke_body'] = '
{$a->requestee},

{$a->requester} has revoked the invitation to team-teach your {$a->other_course}
course with his/her {$a->course} course. All instructors and students from
your {$a->other_course} course will be unenrolled from {$a->course}.';

$string['settings_loading'] = '{$a} - Applying Changes';
$string['please_wait'] = 'Your settings are being applied. Please be patient as the process completes.';

$string['application_errors'] = 'The following error occurred while applying the settings: {$a}';

$string['user_field_category'] = 'Profile Category';
$string['user_field_category_desc'] = 'CPS will attempt to create Moodle user profile fields associated with the user meta information from UES.';
$string['auto_field_desc'] = 'This field was automatically generated through CPS. Do not change the field settings unless you are absolutely certain of what you are doing.';

// Meta Strings.
$string['username'] = 'Username';
$string['user_year'] = 'Year';
$string['user_ferpa'] = 'Ferpa';
$string['user_reg_status'] = 'Registration';
$string['user_degree'] = 'Degree';
$string['user_college'] = 'College';
$string['user_major'] = 'Major';
$string['user_keypadid'] = 'Keypad ID';
$string['user_sport1'] = 'Sport';
$string['user_anonymous_number'] = 'Anonymous';

$string['network_failure'] = 'There was a network error that caused the process to fail. You can either refresh this page or go back to re-apply the settings.';
$string['sec_number'] = 'Section';
$string['credit_hours'] = 'Credit Hours';
$string['student_audit'] = 'Auditing';

// Events.
$string['eventues_course_created'] = 'UES started creating a course';

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
 * Local plugin "bulkenrol" - Language pack
 *
 * @package   local_bulkenrol
 * @copyright 2017 Soon Systems GmbH on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['bulkenrol:enrolusers'] = 'Use user bulk enrolment';
$string['bulkenrol_form_intro'] = 'Here, you can bulk enrol users to your course. A user to be enrolled is identified by his e-mail address stored in his Moodle account.';
$string['enrol_users'] = 'Enrol users';
$string['enrol_users_successful'] = 'User bulk enrolment successful';
$string['enrolinfo_headline'] = 'Enrolment details';
$string['enrolplugin'] = 'Enrolment plugin';
$string['enrolplugin_desc'] = 'The enrolment method to be used to bulk enrol the users. If the configured enrolment method is not active / added in the course when the users are bulk-enrolled, it is automatically added / activated.';
$string['error_check_is_already_member'] = 'Error checking if the user (<em>{$a->email}</em>) is already a member of group (<em>{$a->groupname}</em>). {$a->error}';
$string['error_empty_line'] = 'Line {$a->line} is empty and will be ignored.';
$string['error_enrol_user'] = 'There was a problem when enrolling the user with e-mail <em>{$a->email}</em> to the course.';
$string['error_enrol_users'] = 'There was a problem when enrolling the users to the course.';
$string['error_exception_info'] = 'Exception information';
$string['error_getting_user_for_email'] = 'There was a problem when getting the user record for e-mail address <em>{$a}</em> from the database.';
$string['error_group_add_member'] = 'There was a problem when adding the user with e-mail <em>{$a->email}</em> to the course group <em>{$a->group}</em>.';
$string['error_group_add_members'] = 'There was a problem when adding the users to the course group(s).';
$string['error_invalid_email'] = 'Invalid e-mail address found in line {$a->row} (<em>{$a->email}</em>). This line will be ignored.';
$string['error_more_than_one_record_for_email'] = 'More than one existing Moodle user account with e-mail address <em>{$a}</em> found.<br />This line will be ignored, none of the existing Moodle users will be enrolled.';
$string['error_no_email'] = 'No e-mail address found in line {$a->line} (<em>{$a->content}</em>). This line will be ignored.';
$string['error_no_record_found_for_email'] = 'No existing Moodle user account with e-mail address <em>{$a}</em>.<br />This line will be ignored, there won\'t be a Moodle user account created on-the-fly.';
$string['error_no_valid_email_in_list'] = 'No valid e-mail address was found in the given list.<br />Please <a href=\'{$a->url}\'>go back and check your input</a>.';
$string['error_usermails_empty'] = 'List of e-mail addresses is empty. Please add at least one e-mail address.';
$string['group_name_headline'] = 'Group name';
$string['group_status_create'] = 'Group will be created';
$string['group_status_exists'] = 'Group already exists';
$string['group_status_headline'] = 'Group status';
$string['groupinfos_headline'] = 'Groups included in the list';
$string['hints'] = 'Hints';
$string['nav_both'] = 'Navigation node both in participants page jump menu and in course navigation';
$string['nav_course'] = 'Navigation node in course navigation';
$string['nav_participants'] = 'Navigation node in participants page jump menu';
$string['navigation'] = 'Navigation node placement';
$string['navigation_desc'] = 'The location where the navigation node for user bulk enrolment will be added within a course.';
$string['parameter_empty'] = 'Parameter empty';
$string['pluginname'] = 'User bulk enrolment';
$string['privacy:metadata'] = 'The user bulk enrolment plugin acts as a tool to enrol users into courses, but does not store any personal data.';
$string['role'] = 'Role';
$string['role_assigned'] = 'Assigned role';
$string['role_description'] = 'The role to be used to bulk enrol the users.';
$string['row'] = 'Row';
$string['type_enrol'] = 'Enrolment method';
$string['user_enroled'] = 'User enrolment';
$string['user_enroled_already'] = 'User is already enrolled';
$string['user_enroled_yes'] = 'User will be enrolled';
$string['user_groups'] = 'Group membership';
$string['user_groups_already'] = 'User is already group member';
$string['user_groups_yes'] = 'User will be added to group';
$string['usermails'] = 'List of e-mail addresses';
$string['usermails_help'] = 'To enrol an existing Moodle user into this course, add his e-mail address to this form, one user / e-mail address per line.<br /><br />Example:<br />alice@example.com<br />bob@example.com<br /><br />Optionally, you are able to create groups and add the enrolled users to the groups. All you have to do is to add a heading line with a hash sign and the group\'s name, separating the list of users.<br /><br />Example:<br /># Group 1<br />alice@example.com<br />bob@example.com<br /># Group 2<br />carol@example.com<br />dave@example.com';
$string['users_to_enrol_in_course'] = 'Users to be enrolled into the course';

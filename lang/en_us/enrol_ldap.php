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
 * Strings for component 'enrol_ldap', language 'en_us', version '4.1'.
 *
 * @package     enrol_ldap
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['autocreate'] = '<p>Courses can be created automatically if there are enrollments to a course that doesn\'t yet exist in Moodle</p><p>If you are using automatic course creation, it is recommended that you remove the following capabilities: moodle/course:changeidnumber, moodle/course:changeshortname, moodle/course:changefullname and moodle/course:changesummary, from the relevant roles to prevent modifications of the four course fields specified above (ID number, shortname, fullname and summary).</p>';
$string['autoupdate_settings_desc'] = '<p>Select fields to update when synchronization script is running (enrol/ldap/cli/sync.php).</p><p>When at least one field is selected an update will occur.</p>';
$string['course_settings'] = 'Course enrollment settings';
$string['createcourseextid'] = 'CREATE User enrolled to a nonexistant course \'{$a->courseextid}\'';
$string['createnotcourseextid'] = 'User enrolled to a nonexistant course \'{$a->courseextid}\'';
$string['emptyenrolment'] = 'Empty enrollment for role \'{$a->role_shortname}\' in course \'{$a->course_shortname}\'';
$string['enroluser'] = 'Enroll user \'{$a->user_username}\' into course \'{$a->course_shortname}\' (id {$a->course_id})';
$string['enroluserenable'] = 'Enabled enrollment for user \'{$a->user_username}\' in course \'{$a->course_shortname}\' (id {$a->course_id})';
$string['extremovedsuspend'] = 'Disabled enrollment for user \'{$a->user_username}\' in course \'{$a->course_shortname}\' (id {$a->course_id})';
$string['extremovedsuspendnoroles'] = 'Disabled enrollment and removed roles for user \'{$a->user_username}\' in course \'{$a->course_shortname}\' (id {$a->course_id})';
$string['extremovedunenrol'] = 'Unenroll user \'{$a->user_username}\' from course \'{$a->course_shortname}\' (id {$a->course_id})';
$string['ldap:manage'] = 'Manage LDAP enroll instances';
$string['nested_groups'] = 'Do you want to use nested groups (groups of groups) for enrollment?';
$string['phpldap_noextension'] = '<em>The PHP LDAP module does not seem to be present. Please ensure it is installed and enabled if you want to use this enrollment plugin.</em>';
$string['pluginname'] = 'LDAP enrollments';
$string['pluginname_desc'] = '<p>You can use an LDAP server to control your enrollments. It is assumed your LDAP tree contains groups that map to the courses, and that each of those groups/courses will have membership entries to map to students.</p><p>It is assumed that courses are defined as groups in LDAP, with each group having multiple membership fields (<em>member</em> or <em>memberUid</em>) that contain a unique identification of the user.</p><p>To use LDAP enrollment, your users <strong>must</strong> to have a valid idnumber field. The LDAP groups must have that idnumber in the member fields for a user to be enrolled in the course. This will usually work well if you are already using LDAP Authentication.</p><p>Enrollments will be updated when the user logs in. You can also run a script to keep enrollments in synch. Look in <em>enrol/ldap/cli/sync.php</em>.</p><p>This plugin can also be set to automatically create new courses when new groups appear in LDAP.</p>';
$string['privacy:metadata'] = 'The LDAP enrollments plugin does not store any personal data.';
$string['syncenrolmentstask'] = 'Synchronise enrollments task';

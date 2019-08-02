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
 * Strings for component 'enrol_ldap', language 'en'.
 *
 * @package    enrol_ldap
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @copyright  2010 Iñaki Arenaza <iarenaza@eps.mondragon.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['assignrole']  = "Assigning role '{\$a->role_shortname}' to user '{\$a->user_username}' into course '{\$a->course_shortname}' (id {\$a->course_id})";
$string['assignrolefailed'] = "Failed to assign role '{\$a->role_shortname}' to user '{\$a->user_username}' into course '{\$a->course_shortname}' (id {\$a->course_id})\n";
$string['autocreate'] = '<p>Courses can be created automatically if there are enrolments to a course that doesn\'t yet exist in Moodle</p><p>If you are using automatic course creation, it is recommended that you remove the following capabilities: moodle/course:changeidnumber, moodle/course:changeshortname, moodle/course:changefullname and moodle/course:changesummary, from the relevant roles to prevent modifications of the four course fields specified above (ID number, shortname, fullname and summary).</p>';
$string['autocreate_key'] = 'Auto create';
$string['autocreation_settings'] = 'Automatic course creation settings';
$string['autoupdate_settings'] = 'Automatic course update settings';
$string['autoupdate_settings_desc'] = '<p>Select fields to update when the \'Synchronise LDAP enrolments\' scheduled task is running.</p><p>When at least one field is selected an update will occur.</p>';
$string['bind_dn'] = 'If you want to use a bind user to search users, specify it here. Someting like \'cn=ldapuser,ou=public,o=org\'';
$string['bind_dn_key'] = 'Bind user distinguished name';
$string['bind_pw'] = 'Password for the bind user';
$string['bind_pw_key'] = 'Password';
$string['bind_settings'] = 'Bind settings';
$string['cannotcreatecourse'] = 'Cannot create course: missing required data from the LDAP record!';
$string['cannotupdatecourse'] = "Cannot update course: missing required data from the LDAP record! Course idnumber: '{\$a->idnumber}'";
$string['cannotupdatecourse_duplicateshortname'] = "Cannot update course: Duplicate short name. Skipping course with idnumber '{\$a->idnumber}'...";
$string['courseupdated'] = "Course with idnumber '{\$a->idnumber}' was successfully updated.";
$string['courseupdateskipped'] = "Course with idnumber '{\$a->idnumber}' does not require updating. Skipping...";
$string['category'] = 'The category for auto-created courses';
$string['category_key'] = 'Category';
$string['contexts'] = 'LDAP contexts';
$string['couldnotfinduser'] = "Could not find user '{\$a}', skipping\n";
$string['coursenotexistskip'] = "Course '{\$a}' does not exist and autocreation disabled, skipping\n";
$string['course_fullname'] = 'Optional: LDAP attribute to get the full name from';
$string['course_fullname_key'] = 'Full name';
$string['course_fullname_updateonsync'] = 'Update full name during synchronisation script';
$string['course_fullname_updateonsync_key'] = 'Update full name';
$string['course_idnumber'] = 'LDAP attribute to get the course ID number from. Usually \'cn\' or \'uid\'.';
$string['course_idnumber_key'] = 'ID number';
$string['course_search_sub'] = 'Search group memberships from subcontexts';
$string['course_search_sub_key'] = 'Search subcontexts';
$string['course_settings'] = 'Course enrolment settings';
$string['course_shortname'] = 'Optional: LDAP attribute to get the shortname from';
$string['course_shortname_key'] = 'Short name';
$string['course_shortname_updateonsync'] = 'Update short name during synchronisation script';
$string['course_shortname_updateonsync_key'] = 'Update short name';
$string['course_summary'] = 'Optional: LDAP attribute to get the summary from';
$string['course_summary_key'] = 'Summary';
$string['course_summary_updateonsync'] = 'Update summary during synchronisation script';
$string['course_summary_updateonsync_key'] = 'Update summary';
$string['createcourseextid'] = 'CREATE User enrolled to a non-existing course \'{$a->courseextid}\'';
$string['createnotcourseextid'] = 'User enrolled to a non-existing course \'{$a->courseextid}\'';
$string['creatingcourse'] =  'Creating course \'{$a}\'...';
$string['duplicateshortname'] = "Course creation failed. Duplicate short name. Skipping course with idnumber '{\$a->idnumber}'...";
$string['editlock'] = 'Lock value';
$string['emptyenrolment'] = "Empty enrolment for role '{\$a->role_shortname}' in course '{\$a->course_shortname}'\n";
$string['enrolname'] = 'LDAP';
$string['enroluser'] =  "Enrol user '{\$a->user_username}' into course '{\$a->course_shortname}' (id {\$a->course_id})";
$string['enroluserenable'] =  "Enabled enrolment for user '{\$a->user_username}' in course '{\$a->course_shortname}' (id {\$a->course_id})";
$string['explodegroupusertypenotsupported'] = "ldap_explode_group() does not support selected user type: {\$a}\n";
$string['extcourseidinvalid'] = 'The course external id is invalid!';
$string['extremovedsuspend'] =  "Disabled enrolment for user '{\$a->user_username}' in course '{\$a->course_shortname}' (id {\$a->course_id})";
$string['extremovedsuspendnoroles'] =  "Disabled enrolment and removed roles for user '{\$a->user_username}' in course '{\$a->course_shortname}' (id {\$a->course_id})";
$string['extremovedunenrol'] =  "Unenrol user '{\$a->user_username}' from course '{\$a->course_shortname}' (id {\$a->course_id})";
$string['failed'] = "Failed!\n";
$string['general_options'] = 'General options';
$string['group_memberofattribute'] = 'Name of the attribute that specifies which groups a given user or group belongs to (e.g., memberOf, groupMembership, etc.)';
$string['group_memberofattribute_key'] = '\'Member of\' attribute';
$string['host_url'] = 'Specify LDAP host in URL-form like \'ldap://ldap.myorg.com/\' or \'ldaps://ldap.myorg.com/\'';
$string['host_url_key'] = 'Host URL';
$string['idnumber_attribute'] = 'If the group membership contains distinguished names, specify the same attribute you have used for the user \'ID Number\' mapping in the LDAP authentication settings.';
$string['idnumber_attribute_key'] = 'ID number attribute';
$string['ldap_encoding'] = 'Specify encoding used by LDAP server. Most probably utf-8, MS AD v2 uses default platform encoding such as cp1252, cp1250, etc.';
$string['ldap_encoding_key'] = 'LDAP encoding';
$string['ldap:manage'] = 'Manage LDAP enrol instances';
$string['memberattribute'] = 'LDAP member attribute';
$string['memberattribute_isdn'] = 'If the group membership contains distinguished names, you need to specify them here. If so, you also need to configure the remaining settings in this section.';
$string['memberattribute_isdn_key'] = 'Member attribute uses dn';
$string['nested_groups'] = 'Do you want to use nested groups (groups of groups) for enrolment?';
$string['nested_groups_key'] = 'Nested groups';
$string['nested_groups_settings'] = 'Nested groups settings';
$string['nosuchrole'] = "No such role: '{\$a}'\n";
$string['objectclass'] = 'objectClass used to search courses. Usually \'group\' or \'posixGroup\'';
$string['objectclass_key'] = 'Object class';
$string['ok'] = "OK!\n";
$string['opt_deref'] = 'If the group membership contains distinguished names, specify how aliases are handled during a search. Select one of the following values: \'No\' (LDAP_DEREF_NEVER) or \'Yes\' (LDAP_DEREF_ALWAYS).';
$string['opt_deref_key'] = 'Dereference aliases';
$string['phpldap_noextension'] = '<em>The PHP LDAP module does not seem to be present. Please ensure it is installed and enabled if you want to use this enrolment plugin.</em>';
$string['pluginname'] = 'LDAP enrolments';
$string['pluginname_desc'] = '<p>You can use an LDAP server to control your enrolments. It is assumed your LDAP tree contains groups that map to the courses, and that each of those groups/courses will have membership entries to map to students.</p><p>It is assumed that courses are defined as groups in LDAP, with each group having multiple membership fields (<em>member</em> or <em>memberUid</em>) that contain a uniqueidentification of the user.</p><p>To use LDAP enrolment, your users <strong>must</strong> to have a valid  idnumber field. The LDAP groups must have that idnumber in the member fields for a user to be enrolled in the course. This will usually work well if you are already using LDAP Authentication.</p><p>Enrolments will be updated when the user logs in. You can also run a script to keep enrolments in synch. Look in <em>enrol/ldap/cli/sync.php</em>.</p><p>This plugin can also be set to automatically create new courses when new groups appear in LDAP.</p>';
$string['pluginnotenabled'] = 'Plugin not enabled!';
$string['role_mapping'] = '<p>For each role, you need to specify all LDAP contexts where the groups that represent the courses are located. Separate different contexts with a semicolon (;).</p><p>You also need to specify the attribute your LDAP server uses to hold the members of a group. This is usually \'member\' or \'memberUid\'.</p>';
$string['role_mapping_attribute'] = 'LDAP member attribute for {$a}';
$string['role_mapping_context'] = 'LDAP contexts for {$a}';
$string['role_mapping_key'] = 'Map roles from LDAP ';
$string['roles'] = 'Role mapping';
$string['server_settings'] = 'LDAP server settings';
$string['syncenrolmentstask'] = 'Synchronise LDAP enrolments task';
$string['synccourserole'] = "== Synching course '{\$a->idnumber}' for role '{\$a->role_shortname}'\n";
$string['template'] = 'Optional: auto-created courses can copy their settings from a template course';
$string['template_key'] = 'Template';
$string['unassignrole']  = "Unassigning role '{\$a->role_shortname}' to user '{\$a->user_username}' from course '{\$a->course_shortname}' (id {\$a->course_id})\n";
$string['unassignroleid']  = "Unassigning role id '{\$a->role_id}' to user id '{\$a->user_id}'\n";
$string['unassignrolefailed'] = "Failed to unassign role '{\$a->role_shortname}' to user '{\$a->user_username}' from course '{\$a->course_shortname}' (id {\$a->course_id})\n";
$string['updatelocal'] = 'Update local data';
$string['user_attribute'] = 'If the group membership contains distinguished names, specify the attribute used to name/search for users. If you are using LDAP authentication, this value should match the attribute specified in the \'ID Number\' mapping in the LDAP authentication plugin.';
$string['user_attribute_key'] = 'ID number attribute';
$string['user_contexts'] = 'If the group membership contains distinguished names, specify the list of contexts where users are located. Separate different contexts with a semi-colon (;). For example: \'ou=users,o=org; ou=others,o=org\'.';
$string['user_contexts_key'] = 'Contexts';
$string['user_search_sub'] = 'If the group membership contains distinguished names, specify if the search for users is done in sub-contexts too.';
$string['user_search_sub_key'] = 'Search subcontexts';
$string['user_settings'] = 'User lookup settings';
$string['user_type'] = 'If the group membership contains distinguished names, specify how users are stored in LDAP';
$string['user_type_key'] = 'User type';
$string['version'] = 'The version of the LDAP protocol your server is using';
$string['version_key'] = 'Version';
$string['privacy:metadata'] = 'The LDAP enrolments plugin does not store any personal data.';

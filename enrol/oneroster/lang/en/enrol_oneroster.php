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
 * Strings for component 'enrol_oneroster', language 'en'.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['configurationcorrect'] = 'Connection successfully tested.';
$string['missingrequiredconfig'] = 'One or more required configuration values were not found. Please ensure that you have correctly configured the \'{$a}\' setting.';
$string['pluginname'] = 'One Roster';
$string['pluginname_desc'] = 'Moodle supports the One Roster v1.1 API for Rostering.';
$string['privacy:metadata'] = 'The OneRoster enrolment plugin does not store any personal data.';
$string['settings_connection_clientid'] = 'Authentication Client ID';
$string['settings_connection_clientid_desc'] = 'The client id key used for authentication';
$string['settings_connection_oauth_1'] = 'OAuth 1.0';
$string['settings_connection_oauth_2'] = 'OAuth 2.0';
$string['settings_connection_oauth_version'] = 'OAuth version';
$string['settings_connection_oauth_version_desc'] = 'The version of the OAuth authentication system that the remote server supports.';
$string['settings_connection_oneroster_version'] = 'One Roster version';
$string['settings_connection_oneroster_version_desc'] = 'The version of the One Roster specification that the remote server meets.';
$string['settings_connection_pagesize'] = 'Page size';
$string['settings_connection_pagesize_desc'] = 'The number of records to fetch at once from the OneRoster server.';
$string['settings_connection_root_url'] = 'One Roster Root URL';
$string['settings_connection_root_url_desc'] = 'The Root URL of the One Roster server.';
$string['settings_connection_secret'] = 'Authentication Secret';
$string['settings_connection_secret_desc'] = 'The secret key used for authentication';
$string['settings_connection_settings'] = 'Connections settings';
$string['settings_connection_token_url'] = 'Token URL';
$string['settings_connection_token_url_desc'] = 'The OAuth URL used to fetch an authentication token.';
$string['settings_connection_v1p1'] = 'Version 1.1';
$string['settings_datasync'] = 'Data synchronisation';
$string['settings_datasync_schools'] = 'Schools to sync';
$string['settings_datasync_schools_desc'] = 'One Roster implementation often cover a number of Organisations, Districts, and Schools. You can choose which schools you choose to synchronise.';
$string['settings_newuser'] = 'New user creation';
$string['settings_newuser_desc'] = 'Defaults to use when creating new users from a OneRoster source.';
$string['settings_newuser_auth'] = 'Authentication';
$string['settings_newuser_auth_desc'] = 'The type of authentication to use for new users.';
$string['settings_notmapped'] = 'Not mapped';
$string['settings_rolemapping'] = 'Role mapping';
$string['settings_rolemapping_aide'] = 'One Roster Aide';
$string['settings_rolemapping_aide_desc'] = 'Someone who provides appropriate aide to the user but NOT also one of the other roles.';
$string['settings_rolemapping_generic_desc'] = 'You can choose which role to use when enrolling different types of users. If you do not choose a mapping, that user will not be enrolled in the course at all.';
$string['settings_rolemapping_guardian'] = 'One Roster Guardian';
$string['settings_rolemapping_guardian_desc'] = 'Guardian of the user and NOT the Mother or Father. May also be a Relative.';
$string['settings_rolemapping_parent'] = 'One Roster Parent';
$string['settings_rolemapping_parent_desc'] = 'Mother or father of the user.';
$string['settings_rolemapping_proctor'] = 'Proctor';
$string['settings_rolemapping_proctor_desc'] = 'Exam proctor.';
$string['settings_rolemapping_relative'] = 'One Roster Relative';
$string['settings_rolemapping_relative_desc'] = 'A relative of the user and NOT the mother or Father. May also be a Guardian.';
$string['settings_rolemapping_student'] = 'One Roster Student';
$string['settings_rolemapping_student_desc'] = 'A student at a organization.';
$string['settings_rolemapping_teacher'] = 'One Roster Teacher';
$string['settings_rolemapping_teacher_desc'] = 'A Teacher at organization.';
$string['settings_testconnection'] = 'Test connection';
$string['settings_testconnection_detail'] = 'You should test your settings to ensure that the connection to the server works as expected.
This is also used to fetch the list of available schools that should be synchronised.';
$string['settings_testconnection_link'] = 'Test connection';
$string['test_oneroster_connection'] = 'Test One Roster Connection';
$string['fullsync'] = 'Full sync of One Roster';

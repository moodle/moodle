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
 * Strings for component 'admin', language 'en_us', version '4.1'.
 *
 * @package     admin
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allowframembedding_help'] = 'If enabled, this site may be embedded in a frame in a remote system, as recommended when using the \'Publish as LTI tool\' enrollment plugin. Otherwise, it is recommended to leave frame embedding disabled for security reasons.';
$string['backgroundcolour'] = 'Transparent color';
$string['cliunknowoption'] = 'Unrecognized options:
  {$a}
Please use --help option.';
$string['confignavcourselimit'] = 'Limits the number of courses shown to the user when they are either not logged in or are not enrolled in any courses.
';
$string['confignavshowallcourses'] = 'This setting determines whether users who are enrolled in courses can see Courses (listing all courses) in the navigation, in addition to My Courses (listing courses in which they are enrolled).';
$string['confignavshowcategories'] = 'Show course categories in the navigation bar and navigation blocks. This does not occur with courses the user is currently enrolled in, they will still be listed under mycourses without categories.
';
$string['configprofilesforenrolledusersonly'] = 'To prevent misuse by spammers, profile descriptions of users who are not yet enrolled in any course are hidden. New users must enroll in at least one course before they can add a profile description.';
$string['configsessioncookie'] = 'This setting customizes the name of the cookie used for Moodle sessions. This is optional, and only useful to avoid cookies being confused when there is more than one copy of Moodle running within the same web site.';
$string['configsessioncookiedomain'] = 'This allows you to change the domain that the Moodle cookies are available from. This is useful for Moodle customizations (e.g. authentication or enrollment plugins) that need to share Moodle session information with a web application on another subdomain. <strong>WARNING: it is strongly recommended to leave this setting at the default (empty) - an incorrect value will prevent all logins to the site.</strong>';
$string['configsitedefaultlicense'] = 'Default site license';
$string['configsitedefaultlicensehelp'] = 'The default license for publishing content on this site';
$string['configstatsuserthreshold'] = 'Show course categories in the navigation bar and navigation blocks. This does not occur with courses the user is currently enrolled in, they will still be listed under mycourses without categories.
';
$string['courselistshortnames_desc'] = 'When showing lists of courses, or when referring to courses on administration screens, show the course short name as well as the full name. In fact, when you turn this setting on, the display uses the \'courseextendednamedisplay\' language string, so you can changewhat is displayed using Language customization.';
$string['creatornewroleid_help'] = '';
$string['datarootsecurityerror'] = '<p><strong>SECURITY WARNING!</strong></p><p>Your dataroot directory is in the wrong location and is exposed to the web. This means that all your private files are available to anyone in the world, and some of them could be used by a cracker to obtain unauthorized administrative access to your site!</p>
<p>You <em>must</em> move dataroot directory ({$a}) to a new location that is not within your public web directory, and update the <code>$CFG->dataroot</code> setting in your config.php accordingly.</p>';
$string['editorbackgroundcolor'] = 'Background color';
$string['enableglobalsearch_desc'] = 'If enabled, data will be indexed and synchronized by a scheduled task.';
$string['enrolinstancedefaults'] = 'Enrollment instance defaults';
$string['enrolinstancedefaults_desc'] = 'Default enrollment settings in new courses.';
$string['enrolmultipleusers'] = 'Enroll the users';
$string['groupenrolmentkeypolicy'] = 'Group enrollment key policy';
$string['groupenrolmentkeypolicy_desc'] = 'Turning this on will make Moodle check group enrollment keys against a valid password policy.';
$string['guestroleid_help'] = 'This role is automatically assigned to the guest user. It is also temporarily assigned to not enrolled users that enter the course via guest enrollment plugin.';
$string['helpweekenddays'] = 'Which days of the week are treated as "weekend" and shown with a different color?';
$string['licensesettings'] = 'License settings';
$string['manageqbehaviours'] = 'Manage question behaviors';
$string['profilesforenrolledusersonly'] = 'Profiles for enrolled users only
';
$string['questionbehaviours'] = 'Question behaviors';
$string['rememberuserlicensepref'] = 'Remember user license preference';
$string['rememberuserlicensepref_help'] = 'If enabled, the last license selected by the user is preselected when uploading a file in the file picker. Otherwise, the default site license is preselected.';
$string['requiredentrieschanged'] = '<strong>IMPORTANT - PLEASE READ<br/>(This warning message will only be displayed during this upgrade)</strong><br/>Due to a bug fix, the behavior of database activities using the \'Required entries\' and \'Required entries before viewing settings\' settings will change. A more detailed explanation of the changes can be read on <a href="http://moodle.org/mod/forum/discuss.php?d=110928" target="_blank">the database module forum</a>. The expected behavior of these settings can also be read on <a href="http://docs.moodle.org/en/Adding/editing_a_database#Required_entries" target="_blank">Moodle Docs</a>.';
$string['restorernewroleid_help'] = 'If the user does not already have the permission to manage the newly restored course, the user is automatically assigned this role and enrolled if necessary. Select "None" if you do not want restorers to be able to manage every restored course.';
$string['riskconfig'] = 'Users could change site configuration and behavior';

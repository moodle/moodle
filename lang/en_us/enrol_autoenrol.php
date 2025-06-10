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
 * Strings for component 'enrol_autoenrol', language 'en_us', version '4.1'.
 *
 * @package     enrol_autoenrol
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['alwaysenrol'] = 'Always Enroll';
$string['alwaysenrol_help'] = 'When set to Yes the plugins will always enroll users, even if they already have access to the course through another method.';
$string['auto_desc'] = 'This group has been automatically created by the Auto Enroll plugin. It will be deleted if you remove the Auto Enroll plugin from the course.';
$string['autoenrol:config'] = 'Configure automatic enrollments';
$string['autoenrol:method'] = 'User can enroll users onto a course at login';
$string['autoenrol:unenrol'] = 'User can unenroll autoenrolled users';
$string['autoenrol:unenrolself'] = 'User can unenroll themselves if they are being enrolled on access';
$string['countlimit_help'] = 'This instance will count the number of enrollments it makes on a course and can stop enrolling users once it reaches a certain level. The default setting of 0 means unlimited.';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during automatic enrollments';
$string['filter_help'] = 'When a group focus is selected you can use this field to filter which type of user you wish to enroll onto the course. For example, if you grouped by authentication and filtered with "manual" only users who have registered directly with your site would be enrolled.';
$string['groupon_help'] = 'AutoEnroll can automatically add users to a group when they are enrolled based upon one of these user fields.';
$string['instancename_help'] = 'You can add a custom label to make it clear what this enrollment method does. This option is most useful when there are multiple instances of AutoEnroll on one course.';
$string['method'] = 'Enroll When';
$string['method_help'] = 'Power users can use this setting to change the plugin\'s behavior so that users are enrolled to the course upon logging in rather than waiting for them to access the course. This is helpful for courses which should be visible on a users "my courses" list by default.';
$string['pluginname'] = 'Auto Enroll';
$string['pluginname_desc'] = 'The automatic enrollment module allows an option for logged in users to be automatically granted entry to a course and enrolled. This is similar to allowing guest access but the students will be permanently enrolled and therefore able to participate in forum and activities within the area.';
$string['removegroups_desc'] = 'When an enrollment instance is deleted, should it attempt to remove the groups it has created?';
$string['role_help'] = 'Power users can use this setting to change the permission level at which users are enrolled.';
$string['softmatch_help'] = 'When enabled AutoEnroll will enroll a user when they partially match the "Allow Only" value instead of requiring an exact match. Soft matches are also case-insensitive. The value of "Filter By" will be used for the group name.';
$string['unenrolselfconfirm'] = 'Do you really want to unenroll yourself from course "{$a}"? You can revisit the course to be reenrolled but information such as grades and assignment submissions may be lost.';

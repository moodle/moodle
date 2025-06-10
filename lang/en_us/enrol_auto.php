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
 * Strings for component 'enrol_auto', language 'en_us', version '4.1'.
 *
 * @package     enrol_auto
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['auto:config'] = 'Configure auto enroll instances';
$string['auto:manage'] = 'Manage enrolled users';
$string['auto:unenrol'] = 'Unenroll users from course';
$string['auto:unenrolself'] = 'Unenroll self from the course';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during auto enrollment.';
$string['editenrolment'] = 'Edit enrollment';
$string['enrolon'] = 'Enroll on';
$string['enrolon_desc'] = 'Event which will trigger an auto enrollment.';
$string['enrolon_help'] = 'Choose the event that should trigger auto enrollment.

**Course view** - Enroll a user upon course view.<br>

**User login** - Enroll users as soon as they log in.<br>

**Course activity/resource view** - Enroll a user when one of the selected activities/resources is viewed.<br>
*NOTE:* this option requires a Guest access enroll instance.';
$string['modview'] = 'Course activity/resource view';
$string['modviewmods_desc'] = 'Viewing any of the selected resources/activities will trigger an auto enrollment.';
$string['pluginname'] = 'Auto enrollment';
$string['pluginname_desc'] = 'The auto enrollment plugin automatically enrolls users upon course/activity/resource view.';
$string['requirepassword'] = 'Require enrollment key';
$string['requirepassword_desc'] = 'Require enrollment key in new courses and prevent removing of enrollment key from existing courses.';
$string['sendcoursewelcomemessage_help'] = 'If enabled, users receive a welcome message via email when they get auto enrolled.';
$string['status'] = 'Allow auto enrollments';
$string['status_desc'] = 'Allow auto enrollments of users into course by default.';
$string['status_help'] = 'This setting determines whether this auto enroll plugin is enabled for this course.';
$string['unenrol'] = 'Unenroll user';
$string['unenrolselfconfirm'] = 'Do you really want to unenroll yourself from course "{$a}"?';
$string['unenroluser'] = 'Do you really want to unenroll "{$a->user}" from course "{$a->course}"?';

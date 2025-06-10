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
 * Strings for component 'mnetservice_enrol', language 'en_us', version '4.1'.
 *
 * @package     mnetservice_enrol
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['clientname'] = 'Remote enrollments client';
$string['clientname_help'] = 'This tool allows you to enroll and unenroll your local users on remote hosts that allow you to do so via the \'MNet remote enrollments\' plugin.';
$string['editenrolments'] = 'Edit enrollments';
$string['noroamingusers'] = 'Users require the capability \'{$a}\' in the system context to be enrolled to remote courses, however there are currently no users with this capability. Click the continue button to assign the required capability to one or more roles on your site.';
$string['otherenrolledusers'] = 'Other enrolled users';
$string['pluginname'] = 'Remote enrollment service';
$string['privacy:metadata:mnetservice_enrol_enrolments'] = 'Remote enrollment service';
$string['privacy:metadata:mnetservice_enrol_enrolments:enroltime'] = 'The date/time of when the enrollment was modified.';
$string['privacy:metadata:mnetservice_enrol_enrolments:enroltype'] = 'The name of the enroll plugin at the remote server that was used to enroll our student into their course.';
$string['privacy:metadata:mnetservice_enrol_enrolments:tableexplanation'] = 'This table stores the information about enrollments of our local users in courses on remote hosts.';

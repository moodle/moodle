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
 * Strings for component 'enrol_meta', language 'en_us', version '4.1'.
 *
 * @package     enrol_meta
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['enrolmetasynctask'] = 'Meta enrollment sync task';
$string['meta:config'] = 'Configure meta enroll instances';
$string['meta:unenrol'] = 'Unenroll suspended users';
$string['nosyncroleids'] = 'Roles that are not synchronized';
$string['nosyncroleids_desc'] = 'By default all course level role assignments are synchronized from parent to child courses. Roles that are selected here will not be included in the synchronization process. The roles available for synchronization will be updated in the next cron execution.';
$string['pluginname_desc'] = 'Course meta link enrollment plugin synchronizes enrollments and roles in two different courses.';
$string['privacy:metadata:core_group'] = 'Enroll meta plugin can create a new group or use an existing group to add all the participants of the course linked.';
$string['syncall'] = 'Synchronize all enrolled users';
$string['syncall_desc'] = 'If enabled all enrolled users are synchronized even if they have no role in parent course, if disabled only users that have at least one synchronized role are enrolled in child course.';

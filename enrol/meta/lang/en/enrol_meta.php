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
 * Strings for component 'enrol_meta', language 'en'.
 *
 * @package    enrol_meta
 * @copyright  2010 onwards Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addgroup'] = 'Add to group';
$string['coursesort'] = 'Sort course list';
$string['coursesort_help'] = 'This determines whether the list of courses that can be linked are sorted by sort order (i.e. the order set in Site administration > Courses > Manage courses and categories) or alphabetically by course setting.';
$string['creategroup'] = 'Create new group';
$string['defaultgroupnametext'] = '{$a->name} course {$a->increment}';
$string['linkedcourse'] = 'Link course';
$string['meta:config'] = 'Configure meta enrol instances';
$string['meta:selectaslinked'] = 'Select course as meta linked';
$string['meta:unenrol'] = 'Unenrol suspended users';
$string['nosyncroleids'] = 'Roles that are not synchronised';
$string['nosyncroleids_desc'] = 'By default all course level role assignments are synchronised from parent to child courses. Roles that are selected here will not be included in the synchronisation process. The roles available for synchronisation will be updated in the next cron execution.';
$string['pluginname'] = 'Course meta link';
$string['pluginname_desc'] = 'Course meta link enrolment plugin synchronises enrolments and roles in two different courses.';
$string['syncall'] = 'Synchronise all enrolled users';
$string['syncall_desc'] = 'If enabled all enrolled users are synchronised even if they have no role in parent course, if disabled only users that have at least one synchronised role are enrolled in child course.';

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
$string['coursesort'] = 'Source course list order';
$string['coursesort_help'] = 'When linking the source course to the target course, how should source courses be ordered?';
$string['creategroup'] = 'Create new group';
$string['defaultgroupnametext'] = '{$a->name} course {$a->increment}';
$string['enrolmetasynctask'] = 'Meta enrolment sync task';
$string['linkedcourse'] = 'Link course';
$string['meta:config'] = 'Configure meta enrol instances';
$string['meta:selectaslinked'] = 'Select course as meta linked';
$string['meta:unenrol'] = 'Unenrol suspended users';
$string['nosyncroleids'] = 'Roles that are not synchronised';
$string['nosyncroleids_desc'] = 'Select any roles that should not be synchronised between the source course to the target course.';
$string['pluginname'] = 'Course meta link';
$string['pluginname_desc'] = 'The course meta link synchronises enrolments and roles from the source course to the target course.';
$string['syncall'] = 'Synchronise all enrolled users';
$string['samemetacourse'] = 'You can\'t add a meta link to the same course.';
$string['syncall_desc'] = 'If enabled, all enrolled users are synchronised from the source course even if they have no role in it. Otherwise, only users that have at least one role are enrolled in the target course.';
$string['privacy:metadata:core_group'] = 'The course meta link enrolment plugin can create a new group or use an existing group to add participants from the source course.';
$string['unknownmetacourse'] = 'Unknown meta course shortname';
$string['wscannotcreategroup'] = 'No permission to create group in linked course id = {$a}.';
$string['wsinvalidcourse'] = 'Course ID = {$a} doesn\'t exist or you don\'t have permission to add a course meta link.';
$string['wsinvalidmetacourse'] = 'Meta course ID = {$a} doesn\'t exist or you don\'t have permission to add an enrolment instance.';
$string['wsnoinstancesspecified'] = 'No instances specified';

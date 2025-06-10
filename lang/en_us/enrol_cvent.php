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
 * Strings for component 'enrol_cvent', language 'en_us', version '4.1'.
 *
 * @package     enrol_cvent
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['autocreate_courses_desc'] = 'Courses can be created automatically (with the correct ID numbers) if there are enrollments to a course that doesn\'t yet exist in Moodle';
$string['defaultrole_desc'] = 'This plugin currently only handles Cvent registrants and guests; teachers still need to be enrolled separately in Moodle.';
$string['enrol_cvent_cron_now'] = 'Cvent enrollments: Synchronizing now.n';
$string['enrol_cvent_nocron'] = 'Cvent enrollments: Not configured to synchronize during Moodle cron.n';
$string['enrol_cvent_nocron_now'] = 'Cvent enrollments: Too early for next synchronization during Moodle cron. Will synchronize again in {$a} minute(s).n';
$string['enrol_daysbefore_desc'] = 'Enroll students in their courses this many days before the Cvent event is scheduled to begin.';
$string['pluginname_desc'] = 'You can use Cvent (cvent.com) to manage your enrollments. You must be a customer of Cvent and have API access in order to use this plugin.';
$string['search_location_desc'] = 'The beginning of the name of the location(s) you wish to include.

For example: If you have three locations in Cvent (Town, Town Hall, Courthouse) and you want this moodle to get enrollments for events in Town and Town Hall, enter \'Town\' here.';
$string['set_up_enrolments'] = 'Setting up enrollments for {$a}...<br />n';
$string['youmustsetdatetimezone'] = 'Error: date.timezone is not set in your php.ini. You must set this parameter before the Cvent enrollment plugin can work.';

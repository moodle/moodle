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
 * Strings for component 'enrol_database', language 'en_us', version '4.1'.
 *
 * @package     enrol_database
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['database:config'] = 'Configure database enroll instances';
$string['database:unenrol'] = 'Unenroll suspended users';
$string['ignorehiddencourses_desc'] = 'If enabled users will not be enrolled on courses that are set to be unavailable to students.
';
$string['pluginname_desc'] = 'You can use an external database (of nearly any kind) to control your enrollments. It is assumed your external database contains at least a field containing a course ID, and a field containing a user ID. These are compared against fields that you choose in the local course and user tables.';
$string['privacy:metadata'] = 'The External database enrollment plugin does not store any personal data.';
$string['remoteenroltable'] = 'Remote user enrollment table';
$string['remoteenroltable_desc'] = 'Specify the name of the table that contains list of user enrollments. Empty means no user enrollment sync.';
$string['settingsheaderremote'] = 'Remote enrollment sync';

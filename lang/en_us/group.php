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
 * Strings for component 'group', language 'en_us', version '4.1'.
 *
 * @package     group
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['enrolmentkey'] = 'Enrollment key';
$string['enrolmentkey_help'] = 'An enrollment key enables access to the course to be restricted to only those who know the key. If a group enrollment key is specified, then not only will entering that key let the user into the course, but it will also automatically make them a member of this group.

Note: Group enrollment keys must be enabled in the self enrollment settings and an enrollment key for the course must also be specified.';
$string['enrolmentkeyalreadyinuse'] = 'This enrollment key is already used for another group.';
$string['importgroups_help'] = 'Groups may be imported via text file. The format of the file should be as follows:

* Each line of the file contains one record
* Each record is a series of data separated by commas
* The first record contains a list of fieldnames defining the format of the rest of the file
* Required fieldname is groupname
* Optional fieldnames are description, enrollmentkey, picture, hidepicture';
$string['includeonlyactiveenrol'] = 'Include only active enrollments';

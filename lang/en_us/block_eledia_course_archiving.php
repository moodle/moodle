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
 * Strings for component 'block_eledia_course_archiving', language 'en_us', version '4.1'.
 *
 * @package     block_eledia_course_archiving
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['configure_description'] = 'Here you can configure the archiving process. All courses which are located directly in the source categories
    will be checked against their course start date. If the date is within the timespan of now and the chosen days in the past, the course will be archived.
    This means the course will be set invisible, moved to the configured archive category and all student users will be unenrolled.
    In a second step all courses in archive category are checked against their course start date.
    If it is more than the chosen number of days in the past the course will be deleted.<br /><br />
    The process can be initiated through a form which is linked in the block. The block can be added to the main page only.';

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
 * Strings for component 'assignfeedback_offline', language 'en_us', version '4.1'.
 *
 * @package     assignfeedback_offline
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['enabled_help'] = 'If enabled, the teacher will be able to download and upload a worksheet with student grades when grading the assignments.';
$string['ignoremodified_help'] = 'When the grading worksheet is downloaded from Moodle it contains the last modified date for each of the grades. If any of the grades are updated in Moodle after this worksheet is downloaded, by default Moodle will refuse to overwrite this updated information when importing the grades. By selecting this option Moodle will disable this safety check and it may be possible for multiple graders to overwrite each others grades.';

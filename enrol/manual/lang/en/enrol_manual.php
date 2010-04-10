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
 * Strings for component 'enrol_manual', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   enrol_manual
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['description'] = 'This is the default form of enrolment. There are two main ways a student can be enrolled in a particular course.
<ul>
<li>A teacher or admin can enrol them manually using the link in the Course Administration menu 
    within the course.</li>
<li>A course can have a password defined, known as an "enrolment key".  Anyone who knows this key is 
    able to add themselves to a course.</li>
</ul>';
$string['enrol_manual_requirekey'] = 'Require course enrolment keys in new courses and prevent removing of existing keys.';
$string['enrol_manual_showhint'] = 'Enable this setting to reveal the first character of the enrolment key as a hint if one enters an incorrect key.';
$string['enrol_manual_usepasswordpolicy'] = 'Use current user password policy for course enrolment keys.';
$string['enrolmentkeyerror'] = 'That enrolment key was incorrect, please try again.';
$string['enrolname'] = 'Internal Enrolment';
$string['keyholderrole'] = 'The role of the user that holds the enrolment key for a course. Displayed to students attempting to enrol on the course.';

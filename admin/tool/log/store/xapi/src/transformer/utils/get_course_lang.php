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
 * Transformer utility to retrieve the course language.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

/**
 * Transformer utility to retrieve the course language.
 *
 * @param \stdClass $course The course object.
 * @return string
 */
function get_course_lang($course) {
    $haslang = is_null($course->lang) || $course->lang == '';

    // Ensure en_US and the like get corrected to the standard en-US.
    $preppedlang = mb_ereg_replace('_', '-', $haslang ? 'en' : $course->lang);

    // Ensure valid language format.
    return mb_ereg_match('^[a-zA-Z]{2}(-[a-zA-Z]{2})?$', $preppedlang) ? $preppedlang : 'en';
}

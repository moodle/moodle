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
 * This file contains functions used by the outline report
 *
 * @since 2.1
 * @package course-report
 * @copyright 2011 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function coursereport_pagetypelist($pagetype, $parentcontext, $currentcontext) {
    $array = array('*'=>get_string('page-x', 'pagetype'),
            'course-report-*'=>get_string('page-course-report-x', 'pagetype')
        );

    //extract course-report-outline from course-report-outline-index
    $bits = explode('-', $pagetype);
    if (count($bits >= 3)) {
        $report = array_slice($bits, 2, 1);
        $array['course-report-'.$report[0].'-*'] = get_string('pluginpagetype',  'coursereport_'.$report);
    }

    return $array;
}
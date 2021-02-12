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
 * @package   local_iomad
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Hook called by delete_course to remove iomad table references before course is deleted
 *
 * @param object course record
 */
function local_iomad_pre_course_delete($course) {
    global $DB, $OUTPUT;

    // Clear everything from the iomad_courses table.
    $DB->delete_records('iomad_courses', array('courseid' => $course->id));

    // Remove the course from company allocation tables.
    $DB->delete_records('company_course', array('courseid' => $course->id));

    // Remove the course from company created course tables.
    $DB->delete_records('company_created_courses', array('courseid' => $course->id));

    // Remove the course from company shared courses tables.
    $DB->delete_records('company_shared_courses', array('courseid' => $course->id));

    // Deal with licenses allocations.
    $DB->delete_records('companylicense_users', array('licensecourseid' => $course->id));

    $courselicenses = $DB->get_records('companylicense_courses', array('courseid' => $course->id));

    foreach ($courselicenses as $courselicense) {
        // Delete the course from the license.
        $DB->delete_records('companylicense_courses', array('id' => $courselicense->id));
        // Does the license have any courses left?
        if ($DB->get_records('companylicense_courses', array('licenseid' => $courselicense->licenseid))) {
            company::update_license_usage($courselicense->licenseid);
        } else {
            // Delete the license.  It no longer is valid.
            $DB->delete_records('companylicense', array('id' => $courselicense->licenseid));
        }
    }
    echo $OUTPUT->notification(get_string('removelicenses', 'local_iomad'), 'notifysuccess');

    return true;
}

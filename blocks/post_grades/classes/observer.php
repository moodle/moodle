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

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @package    block_post_grades
 * @copyright  2015 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class block_post_grades_observer {

    /**
     * UES event: ues_semester_dropped
     *
     * When a semester is dropped/deleted, remove its grade periods from post grades
     *
     * @param  \enrol_ues\event\ues_semester_dropped  $event
     * @param  (data)  objectid
     */
    public static function ues_semester_dropped(\enrol_ues\event\ues_semester_dropped $event) {

        global $DB;

        // Remove any of this semester's periods from post grades.
        $params = array('semesterid' => $event->objectid);
        $DB->delete_records('block_post_grades_periods', $params);

        return true;
    }

    /**
     * UES event: ues_section_dropped
     *
     * When a section is dropped/deleted, remove its grade postings from post grades
     *
     * @param  \enrol_ues\event\ues_section_dropped  $event
     * @param  (data)  objectid
     */
    public static function ues_section_dropped(\enrol_ues\event\ues_section_dropped $event) {

        global $DB;

        // Remove any of this section's postings from post grades.
        $params = array('sectionid' => $event->objectid);
        $DB->delete_records('block_post_grades_postings', $params);

        return true;
    }

}

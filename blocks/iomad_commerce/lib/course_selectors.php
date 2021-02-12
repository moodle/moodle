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
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../local/course_selector/lib.php');

/**
 * Selector for any course
 */
class nonshopcourse_selector extends course_selector_base {
    const MAX_COURSES_PER_PAGE = 100;

    public function __construct($name, $options) {
        $this->selectedid  = $options['selectedid'];

        parent::__construct($name, $options);
    }

    /**
     * Any courses
     * @param <type> $search
     * @return array
     */
    public function find_courses($search) {
        global $DB;
        // By default wherecondition retrieves all courses except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'c');

        $fields      = 'SELECT ' . $this->required_fields_sql('c').',c.shortname';
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {course} c
                WHERE
                    c.id NOT IN
                    (
                      SELECT courseid FROM {course_shopsettings}
                    )
                    AND c.id!=1 AND $wherecondition";
        $order = ' ORDER BY c.sortorder, c.fullname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > self::MAX_COURSES_PER_PAGE) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availablecourses = $DB->get_records_sql($fields . $sql . $order, $params);
        // Add the shortname to the course identifier.
        foreach ($availablecourses as $key => $availablecourse) {
                $availablecourses[$key]->fullname = $availablecourse->fullname.' ('.$availablecourse->shortname.')';
        }

        if (empty($availablecourses)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('coursesmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('courses', 'block_iomad_company_admin');
        }

        return array($groupname => $availablecourses);
    }
}


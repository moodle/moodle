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
 * Front-end class.
 *
 * @package availability_company
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_company;

use company;

defined('MOODLE_INTERNAL') || die();

/**
 * Front-end class.
 *
 * @package availability_company
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    /** @var array Array of company info for course */
    protected $allcompanys;
    /** @var int Course id that $allcompanys is for */
    protected $allcompanyscourseid;

    protected function get_javascript_strings() {
        return array('anycompany');
    }

    protected function get_javascript_init_params($course, \cm_info $cm = null,
            \section_info $section = null) {
        // Get all companys for course.
        $companys = $this->get_all_companys($course->id);

        // Change to JS array format and return.
        $jsarray = array();
        $context = \context_course::instance($course->id);
        foreach ($companys as $id => $name) {
            $jsarray[] = (object)array('id' => $id, 'name' =>
                    format_string($name, true, array('context' => $context)));
        }
        return array($jsarray);
    }

    /**
     * Gets all companys for the given course.
     *
     * @param int $courseid Course id
     * @return array Array of all the company objects
     */
    protected function get_all_companys($courseid) {
        global $CFG;
        require_once($CFG->dirroot . '/local/iomad/lib/company.php');

        if ($courseid != $this->allcompanyscourseid) {
            $this->allcompanys = company::get_companies_select();
            $this->allcompanyscourseid = $courseid;
        }
        return $this->allcompanys;
    }

    protected function allow_add($course, \cm_info $cm = null,
            \section_info $section = null) {
        global $CFG;

        // Only show this option if there are some companys.
        return count($this->get_all_companys($course->id)) > 0;
    }
}

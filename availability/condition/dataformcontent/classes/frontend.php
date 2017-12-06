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
 * @package availability_dataformcontent
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_dataformcontent;

defined('MOODLE_INTERNAL') || die();

/**
 * Front-end class.
 *
 * @package availability_dataformcontent
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    /** @var array Array of applicable dataforms names for course */
    protected $dataforms;
    /** @var int Course id that $dataforms is for */
    protected $dataformscourseid;

    /**
     * @return array
     */
    protected function get_javascript_strings() {
        return array();
    }

    /**
     * Use cached result if available. The cache is just because we call it
     * twice (once from allow_add) so it's nice to avoid doing all the
     * print_string calls twice.
     *
     * @return array
     */
    protected function get_javascript_init_params($course, \cm_info $cm = null,
            \section_info $section = null) {

        $jsarray = $this->get_dataforms($course->id);

        return array($jsarray);
    }

    /**
     * Gets all the dataforms and their select fields.
     *
     * @param int $courseid Course id
     * @return array Array of grouping objects
     */
    protected function get_dataforms($courseid) {
        global $DB;

        if ($courseid != $this->dataformscourseid) {
            $this->dataforms = array();

            // Get applicable dataforms.
            if (!$dataforms = condition::get_dataforms($courseid)) {
                return array();
            }

            // Now get their select fields.
            $context = \context_course::instance($courseid);
            foreach ($dataforms as $dataformid => $name) {
                $name = format_string($name, true, array('context' => $context));
                $this->dataforms[] = (object) array(
                    'id' => $dataformid,
                    'name' => $name
                );
            }
            $this->dataformcourseid = $courseid;
        }
        return $this->dataforms;
    }

    /**
     * @return bool
     */
    protected function allow_add($course, \cm_info $cm = null,
            \section_info $section = null) {
        // Check if there are applicable dataforms in the course.
        $dataforms = condition::get_dataforms($course->id);
        return !empty($dataforms);
    }
}

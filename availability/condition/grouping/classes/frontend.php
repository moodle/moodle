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
 * @package availability_grouping
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_grouping;

defined('MOODLE_INTERNAL') || die();

/**
 * Front-end class.
 *
 * @package availability_grouping
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    /** @var array Array of grouping info for course */
    protected $allgroupings;
    /** @var int Course id that $allgroupings is for */
    protected $allgroupingscourseid;

    protected function get_javascript_init_params($course, \cm_info $cm = null,
            \section_info $section = null) {
        // Get all groups for course.
        $groupings = $this->get_all_groupings($course->id);

        // Change to JS array format and return.
        $jsarray = array();
        $context = \context_course::instance($course->id);
        foreach ($groupings as $rec) {
            $jsarray[] = (object)array('id' => $rec->id, 'name' =>
                    format_string($rec->name, true, array('context' => $context)));
        }
        return array($jsarray);
    }

    /**
     * Gets all the groupings on the course.
     *
     * @param int $courseid Course id
     * @return array Array of grouping objects
     */
    protected function get_all_groupings($courseid) {
        global $DB;
        if ($courseid != $this->allgroupingscourseid) {
            $this->allgroupings = $DB->get_records('groupings',
                    array('courseid' => $courseid), 'id, name');
            $this->allgroupingscourseid = $courseid;
        }
        return $this->allgroupings;
    }

    protected function allow_add($course, \cm_info $cm = null,
            \section_info $section = null) {
        global $CFG, $DB;

        // If groupmembersonly is turned on, then you can only add group
        // restrictions on sections (which don't use groupmembersonly) and
        // not on modules. This is to avoid confusion - otherwise
        // there would be two ways to add restrictions based on groups.
        if (is_null($section) && $CFG->enablegroupmembersonly) {
            return false;
        }

        // Check if groupings are in use for the course. (Unlike the 'group'
        // condition there is no case where you might want to set up the
        // condition before you set a grouping - there is no 'any grouping'
        // option.)
        return count($this->get_all_groupings($course->id)) > 0;
    }
}

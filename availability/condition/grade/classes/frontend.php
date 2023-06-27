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
 * @package availability_grade
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_grade;

defined('MOODLE_INTERNAL') || die();

/**
 * Front-end class.
 *
 * @package availability_grade
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    protected function get_javascript_strings() {
        return array('option_min', 'option_max', 'label_min', 'label_max');
    }

    protected function get_javascript_init_params($course, \cm_info $cm = null,
            \section_info $section = null) {
        global $DB, $CFG;
        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->dirroot . '/course/lib.php');

        // Get grades as basic associative array.
        $gradeoptions = array();
        $items = \grade_item::fetch_all(array('courseid' => $course->id));
        // For some reason the fetch_all things return null if none.
        $items = $items ? $items : array();
        foreach ($items as $id => $item) {
            // Don't include the grade item if it's linked with a module that is being deleted.
            if (course_module_instance_pending_deletion($item->courseid, $item->itemmodule, $item->iteminstance)) {
                continue;
            }
            // Do not include grades for current item.
            if ($cm && $cm->instance == $item->iteminstance
                    && $cm->modname == $item->itemmodule
                    && $item->itemtype == 'mod') {
                continue;
            }
            $gradeoptions[$id] = $item->get_name(true);
        }
        \core_collator::asort($gradeoptions);

        // Change to JS array format and return.
        $jsarray = array();
        foreach ($gradeoptions as $id => $name) {
            $jsarray[] = (object)array('id' => $id, 'name' => $name);
        }
        return array($jsarray);
    }
}

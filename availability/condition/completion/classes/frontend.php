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
 * @package availability_completion
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_completion;

defined('MOODLE_INTERNAL') || die();

/**
 * Front-end class.
 *
 * @package availability_completion
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    /**
     * @var array Cached init parameters
     */
    protected $cacheparams = array();

    /**
     * @var string IDs of course, cm, and section for cache (if any)
     */
    protected $cachekey = '';

    protected function get_javascript_strings() {
        return array('option_complete', 'option_fail', 'option_incomplete', 'option_pass',
                'label_cm', 'label_completion');
    }

    protected function get_javascript_init_params($course, \cm_info $cm = null,
            \section_info $section = null) {
        // Use cached result if available. The cache is just because we call it
        // twice (once from allow_add) so it's nice to avoid doing all the
        // print_string calls twice.
        $cachekey = $course->id . ',' . ($cm ? $cm->id : '') . ($section ? $section->id : '');
        if ($cachekey !== $this->cachekey) {
            // Get list of activities on course which have completion values,
            // to fill the dropdown.
            $context = \context_course::instance($course->id);
            $cms = array();
            $modinfo = get_fast_modinfo($course);
            foreach ($modinfo->cms as $id => $othercm) {
                // Add each course-module if it has completion turned on and is not
                // the one currently being edited.
                if ($othercm->completion && (empty($cm) || $cm->id != $id) && !$othercm->deletioninprogress) {
                    $cms[] = (object)array('id' => $id,
                        'name' => format_string($othercm->name, true, array('context' => $context)),
                        'completiongradeitemnumber' => $othercm->completiongradeitemnumber);
                }
            }

            $this->cachekey = $cachekey;
            $this->cacheinitparams = array($cms);
        }
        return $this->cacheinitparams;
    }

    protected function allow_add($course, \cm_info $cm = null,
            \section_info $section = null) {
        global $CFG;

        // Check if completion is enabled for the course.
        require_once($CFG->libdir . '/completionlib.php');
        $info = new \completion_info($course);
        if (!$info->is_enabled()) {
            return false;
        }

        // Check if there's at least one other module with completion info.
        $params = $this->get_javascript_init_params($course, $cm, $section);
        return ((array)$params[0]) != false;
    }
}

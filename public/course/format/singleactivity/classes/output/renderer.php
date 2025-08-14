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

namespace format_singleactivity\output;

use core_courseformat\output\section_renderer;

/**
 * Renderer for outputting the singleactivity course format.
 *
 * @package    format_singleactivity
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends section_renderer {

    /**
     * Displays the activities list in cases when course view page is not
     * redirected to the activity page.
     *
     * @param \stdClass $course record from table course
     * @param bool $orphaned if false displays the main activity (if present)
     *     if true displays all other activities
     */
    public function display($course, $orphaned) {

        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();
        $cmlistclass = $format->get_output_classname('content\\section\\cmlist');

        $output = '';

        if ($orphaned) {
            if (!empty($modinfo->sections[1])) {
                $output .= $this->output->heading(get_string('orphaned', 'format_singleactivity'), 3, 'sectionname');
                $output .= $this->output->box(get_string('orphanedwarning', 'format_singleactivity'));

                $section = $modinfo->get_section_info(1);
                $output .= $this->render(new $cmlistclass($format, $section));
            }
        } else {
            $section = $modinfo->get_section_info(0);
            $output .= $this->render(new $cmlistclass($format, $section));

            if (empty($modinfo->sections[0]) && course_get_format($course)->activity_has_subtypes()) {
                // Course format was unable to automatically redirect to add module page.
                $output .= $this->section_add_cm_controls($format, $section);
            }
        }
        return $output;
    }
}

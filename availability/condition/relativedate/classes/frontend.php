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
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_relativedate;

use cm_info;
use section_info;
use stdClass;

/**
 * Front-end class.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    /**
     * Gets additional parameters for the plugin's initInner function.
     *
     * Default returns no parameters.
     *
     * @param stdClass $course Course object
     * @param cm_info|null $cm Course-module currently being edited (null if none)
     * @param section_info|null $section Section currently being edited (null if none)
     * @return array Array of parameters for the JavaScript function
     */
    protected function get_javascript_init_params($course, ?cm_info $cm = null, ?section_info $section = null) {
        global $DB;
        $optionsdwm = self::convert_associative_array_for_js(condition::options_dwm(2), 'field', 'display');
        $optionsstart = [
            ['field' => 1, 'display' => condition::options_start(1)],
            ['field' => 6, 'display' => condition::options_start(6)],
        ];
        if ($course->enddate != 0) {
            $optionsstart[] = ['field' => 5, 'display' => condition::options_start(5)];
            $optionsstart[] = ['field' => 2, 'display' => condition::options_start(2)];
        }
        $optionsstart[] = ['field' => 3, 'display' => condition::options_start(3)];
        if ($DB->count_records_select('enrol', 'courseid = :courseid AND enrolenddate > 0', ['courseid' => $course->id]) > 0) {
            $optionsstart[] = ['field' => 4, 'display' => condition::options_start(4)];
        }
        $activitysel = [];
        if ($course->enablecompletion) {
            $modinfo = get_fast_modinfo($course);
            $str = get_string('section');
            $s = [];
            $enabled = false;
            // Gets only sections with content.
            foreach ($modinfo->get_sections() as $sectionnum => $cursection) {
                $name = $modinfo->get_section_info($sectionnum)->name;
                $s['name'] = empty($name) ? "$str $sectionnum" : format_string($name);
                $s['coursemodules'] = [];
                foreach ($cursection as $cmid) {
                    if ($cm && $cm->id === $cmid) {
                        continue;
                    }
                    $module = $modinfo->get_cm($cmid);
                    // Get only course modules which are not being deleted.
                    if ($module->deletioninprogress == 0) {
                        $compused = $module->completion > 0;
                        $s['coursemodules'][] = [
                            'id' => $cmid,
                            'name' => format_string($module->name),
                            'completionenabled' => $compused,
                        ];
                        $enabled = $compused ? true : $enabled;
                    }
                }
                $activitysel[] = $s;
            }
            if ($enabled) {
                $optionsstart[] = ['field' => 7, 'display' => condition::options_start(7)];
            }
        }
        return [$optionsdwm, $optionsstart, is_null($section), [], $activitysel];
    }
}

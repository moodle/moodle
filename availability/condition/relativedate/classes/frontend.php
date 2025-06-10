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
 * @copyright 2022 eWallah.net
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
 * @copyright 2022 eWallah.net
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
     * @param cm_info $cm Course-module currently being edited (null if none)
     * @param section_info $section Section currently being edited (null if none)
     * @return array Array of parameters for the JavaScript function
     */
    protected function get_javascript_init_params($course, cm_info $cm = null, section_info $section = null) {
        global $DB;
        $optionsdwm = self::convert_associative_array_for_js(condition::options_dwm(), 'field', 'display');
        $optionsstart = [
            (object)['field' => 1, 'display' => condition::options_start(1)],
            (object)['field' => 6, 'display' => condition::options_start(6)],
        ];
        if ($course->enddate != 0) {
            $optionsstart[] = (object)['field' => 5, 'display' => condition::options_start(5)];
            $optionsstart[] = (object)['field' => 2, 'display' => condition::options_start(2)];
        }
        $optionsstart[] = (object)['field' => 3, 'display' => condition::options_start(3)];
        if ($DB->count_records_select('enrol', 'courseid = :courseid AND enrolenddate > 0', ['courseid' => $course->id]) > 0) {
            $optionsstart[] = (object)['field' => 4, 'display' => condition::options_start(4)];
        }
        $activitysel = [];
        if ($course->enablecompletion != 0) {
            $currentcmid = $cm ? $cm->id : 0;
            $modinfo = get_fast_modinfo($course);

            $s = [];
            // Gets only sections with content.
            foreach ($modinfo->get_sections() as $sectionnum => $section) {
                $sectioninfo = $modinfo->get_section_info($sectionnum);
                $s['name'] = $sectioninfo->name;
                if (empty($s['name'])) {
                    $s['name'] = get_string('section') . ' ' . $sectionnum;
                }
                $s['coursemodules'] = [];
                foreach ($section as $cmid) {
                    if ($currentcmid == $cmid) {
                        continue;
                    }
                    $module = $modinfo->get_cm($cmid);
                    // Get only course modules which are not deleted.
                    if ($module->deletioninprogress == 0) {
                        $s['coursemodules'][] = [
                            'id' => $cmid,
                            'name' => $module->name,
                            'completionenabled' => $module->completion > 0,
                        ];
                    }
                }
                $activitysel[] = $s;
            }
            $optionsstart[] = (object)['field' => 7, 'display' => condition::options_start(7)];
        }
        return [$optionsdwm, $optionsstart, is_null($section), [], $activitysel];
    }
}

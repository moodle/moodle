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
 * Provides format_remuiformat\external\course_progress_data trait.
 *
 * @package     format_remuiformat
 * @category    external
 * @copyright   2018 Wisdmlabs
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_remuiformat\external;
defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use core_completion\progress;

require_once($CFG->libdir.'/externallib.php');

/**
 * Trait implementing the external function format_remuiformat_course_progress_data
 */
trait course_progress_data {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function course_progress_data_parameters() {
        return new external_function_parameters(
            array (
                'courseid' => new external_value(PARAM_INT, 'Course Id')
            )
        );
    }

    /**
     * Fetch course progress data
     * @param  int   $courseid Course id
     * @return array           response
     */
    public static function course_progress_data($courseid) {
        $output = [];
        $course = get_course($courseid);
        $percentage = progress::get_course_progress_percentage($course);
        if (!is_null($percentage)) {
            $percentage = floor($percentage);
            $output['percentage'] = $percentage;
        } else {
            $output['percentage'] = 0;
        }

        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();

        // Get the all activities count from the all sections.
        $sectionmods = array();
        for ($i = 0; $i < count($sections); $i++) {
            if (isset($modinfo->sections[$i])) {
                foreach ($modinfo->sections[$i] as $cmid) {
                    $thismod = $modinfo->cms[$cmid];
                    if (!$thismod->is_visible_on_course_page()) {
                        continue;
                    }
                    if (isset($sectionmods[$thismod->modname])) {
                        $sectionmods[$thismod->modname]['name'] = $thismod->modplural;
                        $sectionmods[$thismod->modname]['count']++;
                    } else {
                        $sectionmods[$thismod->modname]['name'] = $thismod->modfullname;
                        $sectionmods[$thismod->modname]['count'] = 1;
                    }
                }
            }
        }
        $activitylist = [];
        foreach ($sectionmods as $mod) {
            $activitylist[] = $mod['count'].' '.$mod['name'];
        }
        $output['activitylist'] = $activitylist;
        return $output;
    }

    /**
     * Describes the parameters for course progress data returns
     * @return external_single_structure
     */
    public static function course_progress_data_returns() {
        return new external_single_structure (
            array(
                'percentage' => new external_value(PARAM_FLOAT, 'Course completion percentage'),
                'activitylist' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'Activity count details'),
                    'Acitvity count list'
                )
            )
        );
    }
}

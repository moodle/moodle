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
 * Provides format_remuiformat\external\move_activity_to_section trait.
 *
 * @package     format_remuiformat
 * @category    external
 * @copyright   2018 Wisdmlabs
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_remuiformat\external;
defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;

require_once($CFG->libdir.'/externallib.php');
require_once($CFG->dirroot.'/course/lib.php');

/**
 * Trait implementing the external function format_remuiformat_move_activity_to_section
 */
trait move_activity_to_section {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function move_activity_to_section_parameters() {
        return new external_function_parameters(
            array (
                'courseid' => new external_value(PARAM_INT, 'Course Id'),
                'newsectionid' => new external_value(PARAM_INT, 'Section Id'),
                'oldsectionid' => new external_value(PARAM_RAW, 'Old Section Id'),
                'activityidtomove' => new external_value(PARAM_RAW, 'New Section Id'),
            )
        );
    }

    /**
     * Move activity to section
     * @param  int   $courseid         Course Id
     * @param  int   $newsectionid     Section Id
     * @param  int   $oldsectionid     Old Section Id
     * @param  int   $activityidtomove New Section Id
     * @return array                   response
     */
    public static function move_activity_to_section($courseid, $newsectionid, $oldsectionid, $activityidtomove) {
        global $DB, $CFG;

        // Get course object.
        $course = get_course($courseid);

        // Get activity object.
        $cm = get_coursemodule_from_id(null, $activityidtomove, $courseid, false, MUST_EXIST);
        require_login($course, false, $cm);

        // Get new section object.
        $section = $DB->get_record('course_sections', array('course' => $courseid, 'section' => $newsectionid));

        $output = array();
        // Move the activity to new section. Function moveto_module() define in /course/lib.php.
        if ( moveto_module($cm, $section, '') ) {
            // Generate new URL to redirect to same section with success message.
            $urltogo = $CFG->wwwroot.'/course/view.php?id=' . $courseid . '&section=' . $oldsectionid;
            $output['urltogo'] = $urltogo;
            $output['success'] = 1;
            $output['message'] = 'Activity moved successfully';
            return $output;
        } else {
            // Generate new URL to redirect to same section with fail message.
            $urltogo = $CFG->wwwroot.'/course/view.php?id=' . $courseid . '&section=' . $oldsectionid;
            $output['urltogo'] = $urltogo;
            $output['success'] = 0;
            $output['message'] = 'Activity does not moved successfully.';
            return $output;
        }
    }

    /**
     * Describes the parameters for move activity to section returns
     * @return external_single_structure
     */
    public static function move_activity_to_section_returns() {
        return new \external_single_structure (
            array(
                'urltogo' => new external_value(PARAM_RAW, 'Redirect URL.'),
                'success' => new external_value(PARAM_INT, 'If error occurs or not.'),
                'message' => new external_value(PARAM_RAW, 'Error message.')
            )
        );
    }
}

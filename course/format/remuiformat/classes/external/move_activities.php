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
 * Provides format_remuiformat\external\move_activities trait.
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

/**
 * Trait implementing the external function format_remuiformat_move_activities
 */
trait move_activities {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function move_activities_parameters() {
        return new external_function_parameters(
            array (
                'courseid' => new external_value(PARAM_INT, 'Course Id'),
                'sectionid' => new external_value(PARAM_INT, 'Section Id'),
                'sequence' => new external_value(PARAM_RAW, 'Sequence separated by ,'),
            )
        );
    }

    /**
     * Move activities between section
     * @param  int    $courseid  Course id
     * @param  int    $sectionid Section id
     * @param  string $sequence  Section sequence
     * @return array             Moving status
     */
    public static function move_activities($courseid, $sectionid, $sequence) {
        global $DB;
        $table = 'course_sections';
        $output = array();
        $record = $DB->get_record($table, array('course' => $courseid, 'section' => $sectionid));
        if ($sequence != "") {
            $record->sequence = $sequence;
        }
        if ($DB->update_record($table, $record, false)) {
            $output['success'] = true;
            $output['message'] = "Updated Successfully";
        } else {
            $output['success'] = false;
            $output['message'] = "Error in updation";
        }
        // Need to call after database update.
        rebuild_course_cache($courseid, true);

        return $output;
    }

    /**
     * Describes the parameters for move activities return
     * @return external_single_structure
     */
    public static function move_activities_returns() {
        return new \external_single_structure (
            array(
                'success' => new external_value(PARAM_BOOL, 'If error occurs or not.'),
                'message' => new external_value(PARAM_RAW, 'Error message.')
            )
        );
    }
}

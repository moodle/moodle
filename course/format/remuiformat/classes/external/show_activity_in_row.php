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
 * Provides format_remuiformat\external\show_activity_in_row trait.
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
 * Trait implementing the external function format_remuiformat_show_activity_in_row
 */
trait show_activity_in_row {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function show_activity_in_row_parameters() {
        return new external_function_parameters(
            array (
                'courseid' => new external_value(PARAM_INT, 'Course Id'),
                'sectionid' => new external_value(PARAM_INT, 'Section Id'),
                'activityid' => new external_value(PARAM_RAW, 'Activity Id'),
            )
        );
    }

    /**
     * Show activity in row format
     * @param  int   $courseid   Course Id
     * @param  int   $sectionid  Section Id
     * @param  int   $activityid Activity Id
     * @return array             response
     */
    public static function show_activity_in_row($courseid, $sectionid, $activityid) {
        global $DB, $OUTPUT;
        $table = 'format_remuiformat';
        $record = $DB->get_record($table,
            array('courseid' => $courseid, 'sectionid' => $sectionid, 'activityid' => $activityid),
            '*'
        );
        $output = array();
        if ( !empty($record) ) {
            if ($record->layouttype == 'row') {
                $DB->update_record($table, ['id' => $record->id, 'courseid' => $courseid, 'sectionid' => $sectionid,
                'activityid' => $activityid, 'layouttype' => 'col']);
                $output['type'] = 'col';
            } else {
                $DB->update_record($table, ['id' => $record->id, 'courseid' => $courseid, 'sectionid' => $sectionid,
                'activityid' => $activityid, 'layouttype' => 'row']);
                $output['type'] = 'row';
            }
            $output['success'] = true;
            $output['message'] = 'Record Updated';
        } else {
            $DB->insert_record($table, ['courseid' => $courseid, 'sectionid' => $sectionid,
            'activityid' => $activityid, 'layouttype' => 'row']);
            $output['type'] = 'row';
            $output['success'] = true;
            $output['message'] = 'Record Inserted';
        }
        return $output;
    }

    /**
     * Describes the parameters for show activity in row returns
     * @return external_single_structure
     */
    public static function show_activity_in_row_returns() {
        return new \external_single_structure (
            array(
                'success' => new external_value(PARAM_BOOL, 'If error occurs or not.'),
                'message' => new external_value(PARAM_RAW, 'Error message.'),
                'type' => new external_value(PARAM_RAW, 'Activity type.'),
            )
        );
    }
}

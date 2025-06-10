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
 * Web service for mod assign feedback onenote
 *
 * @package    assignfeedback_onenote
 * @since      Moodle 3.9
 * @copyright  Enovation Solutions Ltd. {@link https://enovation.ie}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_onenote\api\base;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/mod/assign/feedback/onenote/externallib.php");

/**
 * Assign functions
 *
 * @copyright 2012 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignfeedback_onenote_external extends external_api {

    /**
     * Utility function for validating an assign.
     *
     * @param int $contextid context id
     * @param int $gradeid grade id
     * @param int $userid user id
     * @return array array containing the assign, course, context and course module objects
     * @since  Moodle 3.2
     */
    protected static function validate_feedback_onenote_delete_foruser($contextid, $gradeid, $userid) {
        global $DB;

        // Request and permission validation.
        $assign = $DB->get_record('assignfeedback_onenote', ['id' => $gradeid], 'id', MUST_EXIST);
        $user = $DB->get_record('user', ['id' => $userid], 'id', MUST_EXIST);

        return [$contextid, $gradeid, $userid];
    }

    /**
     * Describes the parameters for view_assign.
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function feedback_onenote_delete_foruser_parameters() {
        return new external_function_parameters (['contextid' => new external_value(PARAM_INT, 'Context id'),
            'gradeid' => new external_value(PARAM_INT, 'Grade id'), 'userid' => new external_value(PARAM_INT, 'User id'),]);
    }

    /**
     * Update the module completion status.
     *
     * @param int $contextid context id
     * @param int $gradeid grade id
     * @param int $userid user id
     * @return array of warnings and status result
     * @since Moodle 3.2
     */
    public static function feedback_onenote_delete_foruser($contextid, $gradeid, $userid) {
        global $DB;
        $warnings = [];

        self::validate_parameters(self::feedback_onenote_delete_foruser_parameters(),
            ['contextid' => $contextid, 'gradeid' => $gradeid, 'userid' => $userid,]);

        // This code removes the entry.
        $fs = get_file_storage();
        // Delete any previous feedbacks.
        $fs->delete_area_files($contextid, 'assignfeedback_onenote', base::ASSIGNFEEDBACK_ONENOTE_FILEAREA, $gradeid);

        // Remove entry from local_onenote_assign_pages.
        $graderecord = $DB->get_record('assign_grades', ['id' => $gradeid], '*', MUST_EXIST);
        $record = $DB->get_record('local_onenote_assign_pages', ['assign_id' => $graderecord->assignment, 'user_id' => $userid],
            '*', MUST_EXIST);
        $record->feedback_teacher_page_id = '';
        $DB->update_record('local_onenote_assign_pages', $record);

        $result = [];
        $result['status'] = true;
        $result['warnings'] = $warnings;

        return $result;
    }

    /**
     * Describes the view_assign return value.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    public static function feedback_onenote_delete_foruser_returns() {
        return new external_single_structure(['status' => new external_value(PARAM_BOOL, 'status: true if success'),
            'warnings' => new external_warnings(),]);
    }
}

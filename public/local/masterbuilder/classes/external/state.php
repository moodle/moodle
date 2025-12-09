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
 * State and Progress External API for local_masterbuilder.
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_masterbuilder\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/lib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use context_course;

/**
 * External service class for state management.
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class state extends external_api {

    // --- BUILD STATE FUNCTIONS ---

    /**
     * Parameters for get_build_state.
     *
     * @return external_function_parameters
     */
    public static function get_build_state_parameters() {
        return new external_function_parameters([
            'shortname' => new external_value(PARAM_TEXT, 'Course shortname'),
        ]);
    }

    /**
     * Get the build state for a course.
     *
     * @param string $shortname
     * @return array
     */
    public static function get_build_state($shortname) {
        global $DB;
        $params = self::validate_parameters(self::get_build_state_parameters(), ['shortname' => $shortname]);

        $record = $DB->get_record('local_masterbuilder_state', ['course_shortname' => $params['shortname']]);

        return [
            'version' => $record ? $record->version : null,
            'found' => $record ? true : false,
        ];
    }

    /**
     * Returns description for get_build_state.
     *
     * @return external_single_structure
     */
    public static function get_build_state_returns() {
        return new external_single_structure([
            'version' => new external_value(PARAM_TEXT, 'Version string', VALUE_OPTIONAL),
            'found' => new external_value(PARAM_BOOL, 'Whether a record was found'),
        ]);
    }

    /**
     * Parameters for update_build_state.
     *
     * @return external_function_parameters
     */
    public static function update_build_state_parameters() {
        return new external_function_parameters([
            'shortname' => new external_value(PARAM_TEXT, 'Course shortname'),
            'version' => new external_value(PARAM_TEXT, 'Version string'),
        ]);
    }

    /**
     * Update the build state for a course.
     *
     * @param string $shortname
     * @param string $version
     * @return array
     */
    public static function update_build_state($shortname, $version) {
        global $DB;
        $params = self::validate_parameters(self::update_build_state_parameters(), [
            'shortname' => $shortname,
            'version' => $version,
        ]);

        $record = $DB->get_record('local_masterbuilder_state', ['course_shortname' => $params['shortname']]);

        if ($record) {
            $record->version = $params['version'];
            $record->timemodified = time();
            $DB->update_record('local_masterbuilder_state', $record);
        } else {
            $newrecord = new \stdClass();
            $newrecord->course_shortname = $params['shortname'];
            $newrecord->version = $params['version'];
            $newrecord->timemodified = time();
            $DB->insert_record('local_masterbuilder_state', $newrecord);
        }

        return ['success' => true];
    }

    /**
     * Returns description for update_build_state.
     *
     * @return external_single_structure
     */
    public static function update_build_state_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
        ]);
    }

    /**
     * Parameters for reset_build_state.
     *
     * @return external_function_parameters
     */
    public static function reset_build_state_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Reset the build state table.
     *
     * @return array
     */
    public static function reset_build_state() {
        global $DB;
        // Truncate the table.
        $DB->delete_records('local_masterbuilder_state');
        return ['success' => true, 'message' => 'Build state table reset.'];
    }

    /**
     * Returns description for reset_build_state.
     *
     * @return external_single_structure
     */
    public static function reset_build_state_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'message' => new external_value(PARAM_TEXT, 'Message'),
        ]);
    }

    // --- COURSE RESET ---

    /**
     * Parameters for reset_course_progress.
     *
     * @return external_function_parameters
     */
    public static function reset_course_progress_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'The ID of the course to reset'),
        ]);
    }

    /**
     * Reset course progress.
     *
     * @param int $courseid
     * @return array
     */
    public static function reset_course_progress($courseid) {
        global $DB;

        $params = self::validate_parameters(self::reset_course_progress_parameters(), [
            'courseid' => $courseid,
        ]);

        $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);
        $context = context_course::instance($course->id);
        self::validate_context($context);

        // Prepare reset data.
        $data = new \stdClass();
        $data->id = $course->id;
        $data->reset_gradebook_grades = true;
        $data->reset_completion = true;
        $data->reset_quiz_attempts = true;

        // Perform reset.
        $status = reset_course_userdata($data);

        // Check status.
        $success = true;
        $errors = [];
        foreach ($status as $item) {
            if (!empty($item['error'])) {
                $success = false;
                $errors[] = $item['component'] . ': ' . $item['item'];
            }
        }

        return [
            'success' => $success,
            'message' => $success ? 'Course progress reset successfully' : 'Errors: ' . implode(', ', $errors),
        ];
    }

    /**
     * Returns description for reset_course_progress.
     *
     * @return external_single_structure
     */
    public static function reset_course_progress_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'message' => new external_value(PARAM_TEXT, 'Result message'),
        ]);
    }
}

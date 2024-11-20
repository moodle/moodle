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

namespace core_grades\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/grade/lib.php');

/**
 * Web service to fetch students feedback for a grade item.
 *
 * @package    core_grades
 * @copyright  2023 Kevin Percy <kevin.percy@moodle.com>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_feedback extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
                'userid' => new external_value(PARAM_INT, 'User ID', VALUE_REQUIRED),
                'itemid' => new external_value(PARAM_INT, 'Grade Item ID', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * Given a user ID and grade item ID, return feedback and user details.
     *
     * @param int $courseid The course ID.
     * @param int $userid
     * @param int $itemid
     * @return array Feedback and user details
     */
    public static function execute(int $courseid, int $userid, int $itemid): array {
        global $OUTPUT, $CFG;

        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid,
                'userid' => $userid,
                'itemid' => $itemid
            ]
        );

        $context = \context_course::instance($courseid);
        parent::validate_context($context);

        require_capability('gradereport/grader:view', $context);

        $gtree = new \grade_tree($params['courseid'], false, false, null, !$CFG->enableoutcomes);
        $gradeitem = $gtree->get_item($params['itemid']);

        // If Item ID is not part of Course ID, $gradeitem will be set to false.
        if ($gradeitem === false) {
            throw new invalid_parameter_exception('Course ID and item ID mismatch');
        }

        $grade = $gradeitem->get_grade($params['userid'], false);
        $user = \core_user::get_user($params['userid']);
        $extrafields = \core_user\fields::get_identity_fields($context);

        return [
            'feedbacktext' => $grade->feedback,
            'title' => $gradeitem->get_name(true),
            'fullname' => fullname($user),
            'picture' => $OUTPUT->user_picture($user, ['size' => 50, 'link' => false]),
            'additionalfield' => empty($extrafields) ? '' : $user->{$extrafields[0]},
        ];
    }

    /**
     * Describes the return structure.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'feedbacktext' => new external_value(PARAM_RAW, 'The full feedback text'),
            'title' => new external_value(PARAM_TEXT, 'Title of the grade item that the feedback is for'),
            'fullname' => new external_value(PARAM_TEXT, 'Students name'),
            'picture' => new external_value(PARAM_RAW, 'Students picture'),
            'additionalfield' => new external_value(PARAM_RAW, 'Additional field for the user (email or ID number, for example)'),
        ]);
    }
}

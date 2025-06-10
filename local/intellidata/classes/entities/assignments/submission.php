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
 * Class for preparing data for Assignments Submissions.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\assignments;

/**
 * Class for preparing data for Assignments Submissions.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'assignmentsubmissions';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Submission ID.',
                'default' => 0,
            ],
            'assignment' => [
                'type' => PARAM_INT,
                'description' => 'Assignment ID.',
                'default' => 0,
            ],
            'userid' => [
                'type' => PARAM_INT,
                'description' => 'User ID.',
                'default' => 0,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when submission created or modified.',
                'default' => 0,
            ],
            'status' => [
                'type' => PARAM_TEXT,
                'description' => 'Submission status.',
                'default' => '',
            ],
            'attemptnumber' => [
                'type' => PARAM_INT,
                'description' => 'Submission attempt.',
                'default' => '',
            ],
            'grade' => [
                'type' => PARAM_TEXT,
                'description' => 'Submission grade.',
                'default' => '',
            ],
            'feedback' => [
                'type' => PARAM_RAW,
                'description' => 'Submission feedback.',
                'default' => '',
            ],
            'feedback_at' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when submission greaded.',
                'default' => 0,
            ],
            'feedback_by' => [
                'type' => PARAM_INT,
                'description' => 'Grader User Id.',
                'default' => 0,
            ],
            'submission_type' => [
                'type' => PARAM_TEXT,
                'description' => 'Submission Type.',
                'default' => '',
            ],
        ];
    }

    /**
     * Prepare entity data for export.
     *
     * @param \stdClass $object
     * @param array $fields
     * @return null
     * @throws invalid_persistent_exception
     */
    public static function prepare_export_data($object, $fields = [], $table = '') {
        global $DB;

        $object->submission_type = observer::get_submission_type($object->id);
        $gradedata = $DB->get_record('assign_grades', [
            'assignment' => $object->assignment,
            'userid' => $object->userid,
            'attemptnumber' => $object->attemptnumber,
        ]);

        if (!empty($gradedata->grade)) {
            $object->grade = $gradedata->grade;
            $object->feedback_at = $gradedata->timemodified;
            $object->feedback_by = $gradedata->grader;

            $feedback = $DB->get_record('assignfeedback_comments', [
                'assignment' => $gradedata->assignment,
                'grade' => $gradedata->id,
            ]);

            if (!empty($feedback->commenttext)) {
                $object->feedback = $feedback->commenttext;
            }
        }

        return $object;
    }
}

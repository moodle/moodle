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
 * Class for preparing data for Activities.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\quizquestionanswers;

/**
 * Class for preparing data for Activities.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizquestionattempts extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'quizquestionattempts';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Record ID.',
                'default' => 0,
            ],
            'attemptid' => [
                'type' => PARAM_INT,
                'description' => 'Quiz Attempt ID.',
                'default' => 0,
            ],
            'questionid' => [
                'type' => PARAM_INT,
                'description' => 'Question ID.',
                'default' => 0,
            ],
            'uniqueid' => [
                'type' => PARAM_INT,
                'description' => 'Question attempt Unique ID.',
                'default' => 0,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when record modified.',
                'default' => 0,
            ],
            'maxmark' => [
                'type' => PARAM_TEXT,
                'description' => 'The grade for this question,.',
                'default' => '',
            ],
            'slot' => [
                'type' => PARAM_INT,
                'description' => 'Slot number.',
                'default' => 0,
            ],
            'responsesummary' => [
                'type' => PARAM_TEXT,
                'description' => 'The grade for this question,.',
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

        if ($quizattempts = $DB->get_record('quiz_attempts', ['uniqueid' => $object->questionusageid])) {
            $object->attemptid = $quizattempts->id;
            $object->uniqueid = $quizattempts->uniqueid;
            $object->timemodified = $quizattempts->timemodified;
        }

        return $object;
    }
}

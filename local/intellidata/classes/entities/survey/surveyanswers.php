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
 * Class for preparing data for Survey Answers.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\survey;


/**
 * Class for preparing data for Survey Answers.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class surveyanswers extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'surveyanswers';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Survey answer ID.',
                'default' => 0,
            ],
            'userid' => [
                'type' => PARAM_INT,
                'description' => 'User ID.',
                'default' => 0,
            ],
            'survey' => [
                'type' => PARAM_INT,
                'description' => 'Survey ID.',
                'default' => 0,
            ],
            'questiontype' => [
                'type' => PARAM_INT,
                'description' => 'Question type.',
                'default' => 0,
            ],
            'questiontext' => [
                'type' => PARAM_TEXT,
                'description' => 'Question text.',
                'default' => '',
            ],
            'question' => [
                'type' => PARAM_INT,
                'description' => 'Question ID.',
                'default' => 0,
            ],
            'time' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when answer created.',
                'default' => 0,
            ],
            'answer1' => [
                'type' => PARAM_TEXT,
                'description' => 'Answer 1.',
                'default' => '',
            ],
            'answer2' => [
                'type' => PARAM_TEXT,
                'description' => 'Answer 2.',
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

        if (!isset($object->userid) || isset($object->survey) || isset($object->question)) {
            return $object;
        }

        $params = [
            'time' => $object->time,
            'userid' => $object->userid,
            'survey' => $object->survey,
            'question' => $object->question,
        ];

        $record = $DB->get_record_sql('SELECT sa.*, sq.text as questiontext, sq.type as questiontype
                                             FROM {survey_answers} sa
                                        LEFT JOIN {survey_questions} sq ON sq.id = sa.question
                                            WHERE `time`=:time AND userid=:userid AND
                                                   survey=:survey AND question=:question', $params);

        if ($record) {
            return $record;
        }

        return $object;
    }
}

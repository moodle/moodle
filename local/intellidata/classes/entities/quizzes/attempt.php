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
 * Class for preparing data for Quiz Attempt.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\quizzes;

/**
 * Class for preparing data for Quiz Attempt.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'quizattempts';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Discussion ID.',
                'default' => 0,
            ],
            'quiz' => [
                'type' => PARAM_INT,
                'description' => 'Quiz ID.',
                'default' => 0,
            ],
            'userid' => [
                'type' => PARAM_INT,
                'description' => 'User ID.',
                'default' => 0,
            ],
            'attempt' => [
                'type' => PARAM_INT,
                'description' => 'Attempt number.',
                'default' => 0,
            ],
            'timestart' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when attempt started.',
                'default' => 0,
            ],
            'timefinish' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when attempt finished.',
                'default' => 0,
            ],
            'state' => [
                'type' => PARAM_TEXT,
                'description' => 'Attempt status.',
                'default' => '',
            ],
            'score' => [
                'type' => PARAM_TEXT,
                'description' => 'Attempt grade in percent.',
                'default' => '',
            ],
            'points' => [
                'type' => PARAM_TEXT,
                'description' => 'Attempt grade.',
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

        $quiz = $DB->get_record('quiz', ['id' => $object->quiz]);

        $object->points = $quiz->score = 0;
        if ($object->sumgrades && $quiz->sumgrades) {
            $object->points = ($object->sumgrades / $quiz->sumgrades) * $quiz->grade;
            $object->score = ($object->sumgrades / $quiz->sumgrades) * 100;
        }
        return $object;
    }
}

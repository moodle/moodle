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
class quizquestionanswer extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'quizquestionanswers';

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
            'state' => [
                'type' => PARAM_TEXT,
                'description' => 'Question Attempt state ID.',
                'default' => 0,
            ],
            'value' => [
                'type' => PARAM_RAW,
                'description' => 'User answer.',
                'default' => '',
            ],
            'fraction' => [
                'type' => PARAM_FLOAT,
                'description' => 'Grade.',
                'default' => 1,
            ],
        ];
    }
}

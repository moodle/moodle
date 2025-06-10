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
 * Class for preparing data for survey.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\survey;

/**
 * Class for preparing data for survey.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class survey extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'survey';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Survey ID.',
                'default' => 0,
            ],
            'course' => [
                'type' => PARAM_INT,
                'description' => 'Course ID.',
                'default' => 0,
            ],
            'template' => [
                'type' => PARAM_INT,
                'description' => 'Template ID.',
                'default' => 0,
            ],
            'days' => [
                'type' => PARAM_INT,
                'description' => 'Days.',
                'default' => 0,
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when survey created.',
                'default' => 0,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when survey updated.',
                'default' => 0,
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'Name.',
                'default' => '',
            ],
            'intro' => [
                'type' => PARAM_TEXT,
                'description' => 'Intro text.',
                'default' => '',
            ],
            'infoformat' => [
                'type' => PARAM_INT,
                'description' => 'Info format.',
                'default' => 0,
            ],
            'questions' => [
                'type' => PARAM_TEXT,
                'description' => 'Questions.',
                'default' => '',
            ],
            'completionsubmit' => [
                'type' => PARAM_TEXT,
                'description' => 'Completion submit.',
                'default' => '',
            ],
        ];
    }
}

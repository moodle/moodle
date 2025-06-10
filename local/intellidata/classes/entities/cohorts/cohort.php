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
 * Class for preparing data for Course Completions.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\cohorts;

/**
 * Class for preparing data for Course Completions.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'cohorts';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Cohort ID.',
                'default' => 0,
            ],
            'contextid' => [
                'type' => PARAM_INT,
                'description' => 'Cohort Context ID.',
                'default' => 0,
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'Cohort Name.',
                'default' => '',
            ],
            'idnumber' => [
                'type' => PARAM_TEXT,
                'description' => 'Cohort ID Number.',
                'default' => '',
            ],
            'visible' => [
                'type' => PARAM_BOOL,
                'description' => 'Cohort Visible State.',
                'default' => 1,
            ],
            'component' => [
                'type' => PARAM_TEXT,
                'description' => 'Cohort Component.',
                'default' => '',
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when cohort was created.',
                'default' => 0,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when cohort was modefied.',
                'default' => 0,
            ],
        ];
    }

}

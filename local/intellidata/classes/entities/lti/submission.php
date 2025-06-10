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
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\lti;

/**
 * Class for preparing data for Assignments Submissions.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'ltisubmittion';

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
            'ltiid' => [
                'type' => PARAM_INT,
                'description' => 'Lti ID.',
                'default' => 0,
            ],
            'userid' => [
                'type' => PARAM_INT,
                'description' => 'User ID.',
                'default' => 0,
            ],
            'datesubmitted' => [
                'type' => PARAM_INT,
                'description' => 'Date submitted',
                'default' => 0,
            ],
            'dateupdated' => [
                'type' => PARAM_INT,
                'description' => 'Date updated',
                'default' => 0,
            ],
            'gradepercent' => [
                'type' => PARAM_FLOAT,
                'description' => 'Final Grade',
                'default' => 0,
            ],
            'originalgrade' => [
                'type' => PARAM_FLOAT,
                'description' => 'Original Grade',
                'default' => 0,
            ],
            'launchid' => [
                'type' => PARAM_INT,
                'description' => 'Launch ID',
                'default' => 0,
            ],
            'state' => [
                'type' => PARAM_INT,
                'description' => 'State',
                'default' => 0,
            ],
        ];
    }
}

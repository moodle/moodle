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
 * Class for preparing data for LTI Types.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\lti;


/**
 * Class for preparing data for LTI Types.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ltitypes extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'ltitypes';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Type ID.',
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'Name.',
                'default' => '',
            ],
            'baseurl' => [
                'type' => PARAM_TEXT,
                'description' => 'Base url.',
                'default' => '',
            ],
            'tooldomain' => [
                'type' => PARAM_TEXT,
                'description' => 'Tool domain.',
                'default' => '',
            ],
            'state' => [
                'type' => PARAM_INT,
                'description' => 'State.',
                'default' => 0,
            ],
            'course' => [
                'type' => PARAM_INT,
                'description' => 'Course id.',
                'default' => 0,
            ],
            'сoursevisible' => [
                'type' => PARAM_INT,
                'description' => 'Course visible status.',
                'default' => 0,
            ],
            'clientid' => [
                'type' => PARAM_TEXT,
                'description' => 'Client ID.',
                'default' => '',
            ],
            'toolproxyid' => [
                'type' => PARAM_INT,
                'description' => 'Tool proxy ID.',
                'default' => 0,
            ],
            'enabledcapability' => [
                'type' => PARAM_TEXT,
                'description' => 'Enabled capability.',
                'default' => '',
            ],
            'parameter' => [
                'type' => PARAM_TEXT,
                'description' => 'Parameter.',
                'default' => '',
            ],
            'icon' => [
                'type' => PARAM_TEXT,
                'description' => 'Icon',
                'default' => '',
            ],
            'secureicon' => [
                'type' => PARAM_TEXT,
                'description' => 'Secure icon',
                'default' => '',
            ],
            'createdby' => [
                'type' => PARAM_INT,
                'description' => 'Created by',
                'default' => 0,
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'description' => 'Time created',
                'default' => 0,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'Time modified',
                'default' => 0,
            ],
            'description' => [
                'type' => PARAM_TEXT,
                'description' => 'Description',
                'default' => '',
            ],
        ];
    }
}

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
 * Contains related class for displaying information of a tag area.
 *
 * @package   core_tag
 * @copyright 2019 Juan Leyva <juan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;

/**
 * Contains related class for displaying information of a tag area.
 *
 * @package   core_tag
 * @copyright 2019 Juan Leyva <juan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tag_area_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Area id.',
            ],
            'component' => [
                'type' => PARAM_COMPONENT,
                'description' => 'Component the area is related to.',
            ],
            'itemtype' => [
                'type' => PARAM_ALPHANUMEXT,
                'description' => 'Type of item in the component.',
            ],
            'enabled' => [
                'type' => PARAM_BOOL,
                'description' => 'Whether this area is enabled.',
                'default' => true,
            ],
            'tagcollid' => [
                'type' => PARAM_INT,
                'description' => 'The tag collection this are belongs to.',
            ],
            'callback' => [
                'type' => PARAM_RAW,
                'description' => 'Component callback for processing tags.',
                'null' => NULL_ALLOWED,
            ],
            'callbackfile' => [
                'type' => PARAM_RAW,
                'description' => 'Component callback file.',
                'null' => NULL_ALLOWED,
            ],
            'showstandard' => [
                'type' => PARAM_INT,
                'description' => 'Return whether to display only standard, only non-standard or both tags.',
                'default' => 0,
            ],
            'multiplecontexts' => [
                'type' => PARAM_BOOL,
                'description' => 'Whether the tag area allows tag instances to be created in multiple contexts. ',
                'default' => false,
            ],
        ];
    }

    protected static function define_related() {
        return array(
            'locked' => 'bool?'
        );
    }

    protected static function define_other_properties() {
        return array(
            'locked' => [
                'type' => PARAM_BOOL,
                'description' => 'Whether the area is locked.',
                'null' => NULL_ALLOWED,
                'default' => false,
                'optional' => true,
            ]
        );
    }

    protected function get_other_values(renderer_base $output) {

        $values['locked'] = $this->related['locked'] ? true : false;

        return $values;
    }
}

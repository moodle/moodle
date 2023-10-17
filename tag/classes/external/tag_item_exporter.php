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
 * Contains related class for displaying information of a tag item.
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
 * Contains related class for displaying information of a tag item.
 *
 * @package   core_tag
 * @copyright 2019 Juan Leyva <juan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tag_item_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Tag id.',
            ],
            'name' => [
                'type' => PARAM_TAG,
                'description' => 'Tag name.',
            ],
            'rawname' => [
                'type' => PARAM_RAW,
                'description' => 'The raw, unnormalised name for the tag as entered by users.',
            ],
            'isstandard' => [
                'type' => PARAM_BOOL,
                'description' => 'Whether this tag is standard.',
                'default' => false,
            ],
            'tagcollid' => [
                'type' => PARAM_INT,
                'description' => 'Tag collection id.',
            ],
            'taginstanceid' => [
                'type' => PARAM_INT,
                'description' => 'Tag instance id.',
            ],
            'taginstancecontextid' => [
                'type' => PARAM_INT,
                'description' => 'Context the tag instance belongs to.',
            ],
            'itemid' => [
                'type' => PARAM_INT,
                'description' => 'Id of the record tagged.',
            ],
            'ordering' => [
                'type' => PARAM_INT,
                'description' => 'Tag ordering.',
            ],
            'flag' => [
                'type' => PARAM_INT,
                'description' => 'Whether the tag is flagged as inappropriate.',
                'default' => 0,
                'null' => NULL_ALLOWED,
            ],
        ];
    }

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'viewurl' => [
                'type' => PARAM_URL,
                'description' => 'The url to view the tag.',
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        return [
            'viewurl' => \core_tag_tag::make_url($this->data->tagcollid, $this->data->rawname)->out(false),
        ];
    }
}

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
 * Contains related class for displaying information of a tag collection.
 *
 * @package   core_tag
 * @copyright 2019 Juan Leyva <juan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;

/**
 * Contains related class for displaying information of a tag collection.
 *
 * @package   core_tag
 * @copyright 2019 Juan Leyva <juan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tag_collection_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Collection id.',
            ],
            'name' => [
                'type' => PARAM_NOTAGS,
                'description' => 'Collection name.',
                'null' => NULL_ALLOWED,
            ],
            'isdefault' => [
                'type' => PARAM_BOOL,
                'description' => 'Whether is the default collection.',
                'default' => false,
            ],
            'component' => [
                'type' => PARAM_COMPONENT,
                'description' => 'Component the collection is related to.',
                'null' => NULL_ALLOWED,
            ],
            'sortorder' => [
                'type' => PARAM_INT,
                'description' => 'Collection ordering in the list.',
            ],
            'searchable' => [
                'type' => PARAM_BOOL,
                'description' => 'Whether the tag collection is searchable.',
                'default' => true,
            ],
            'customurl' => [
                'type' => PARAM_NOTAGS,
                'description' => 'Custom URL for the tag page instead of /tag/index.php.',
                'null' => NULL_ALLOWED,
            ],
        ];
    }
}

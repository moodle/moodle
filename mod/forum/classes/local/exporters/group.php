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
 * Course Group exporter.
 *
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\exporters;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use stdClass;

require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * Group exporter.
 *
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group extends exporter {
    /** @var stdClass $group Group */
    private $group;

    /**
     * Constructor.
     *
     * @param stdClass $group The group to export
     * @param array $related The related data for the export.
     */
    public function __construct(stdClass $group, array $related = []) {
        $this->group = $group;
        return parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'urls' => [
                'type' => [
                    'image' => [
                        'description' => 'The URL for the group image',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ]
                ],
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
            'id' => $group->id,
            'urls' => [
                'image' => $imageurl ? $imageurl->out(false) : null
            ]
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'urlmanager' => 'mod_forum\local\managers\url',
            'context' => 'context'
        ];
    }
}

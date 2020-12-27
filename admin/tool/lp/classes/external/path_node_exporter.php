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
 * Class for exporting path_node data.
 *
 * @package    tool_lp
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use context_system;

/**
 * Class for exporting path_node data.
 *
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class path_node_exporter extends \core\external\exporter {

    /**
     * Constructor - saves the persistent object, and the related objects.
     *
     * @param mixed $data The data.
     * @param array $related Array of relateds.
     */
    public function __construct($data, $related = array()) {
        if (!isset($related['context'])) {
            // Previous code was automatically using the system context which was not always correct.
            // We let developers know that they must fix their code without breaking anything,
            // and fallback on the previous behaviour. This should be removed at a later stage: Moodle 3.5.
            debugging('Missing related context in path_node_exporter.', DEBUG_DEVELOPER);
            $related['context'] = context_system::instance();
        }
        parent::__construct($data, $related);
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context'
        ];
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED
            ],
            'name' => [
                'type' => PARAM_TEXT
            ],
            'first' => [
                'type' => PARAM_BOOL
            ],
            'last' => [
                'type' => PARAM_BOOL
            ],
            'position' => [
                'type' => PARAM_INT
            ]
        ];
    }
}

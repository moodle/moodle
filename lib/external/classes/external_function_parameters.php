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

namespace core_external;

/**
 * Description of top level - PHP function parameters.
 *
 * @package    core_external
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_function_parameters extends external_single_structure {
    /**
     * Constructor - does extra checking to prevent top level optional parameters.
     *
     * @param array $keys
     * @param string $desc
     * @param int $required
     * @param array $default
     */
    public function __construct(
        array $keys,
        $desc = '',
        $required = VALUE_REQUIRED,
        $default = null
    ) {
        global $CFG;

        if ($CFG->debugdeveloper) {
            foreach (array_values($keys) as $value) {
                if ($value instanceof external_value) {
                    if ($value->required == VALUE_OPTIONAL) {
                        debugging('External function parameters: invalid OPTIONAL value specified.', DEBUG_DEVELOPER);
                        break;
                    }
                }
            }
        }
        parent::__construct($keys, $desc, $required, $default, NULL_NOT_ALLOWED);
    }
}

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

namespace core_competency;

/**
 * Abstract class for core_competency objects saved to the DB.
 *
 * This is a legacy class which all core_competency persistent classes created prior
 * to 3.3 must extend.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class persistent extends \core\persistent {
    /**
     * Magic method to capture getters and setters.
     * This is only available for competency persistents for backwards compatibility.
     * It is recommended to use get('propertyname') and set('propertyname', 'value') directly.
     *
     * @param  string $method Callee.
     * @param  array $arguments List of arguments.
     * @return mixed
     */
    final public function __call($method, $arguments) {
        debugging('Use of magic setters and getters is deprecated. Use get() and set().', DEBUG_DEVELOPER);
        if (strpos($method, 'get_') === 0) {
            return $this->get(substr($method, 4));
        } else if (strpos($method, 'set_') === 0) {
            return $this->set(substr($method, 4), $arguments[0]);
        }
        throw new \coding_exception('Unexpected method call: ' . $method);
    }

}

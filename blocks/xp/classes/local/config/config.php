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
 * Config interface.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

/**
 * Config interface.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface config {

    /**
     * Get a value.
     *
     * @param string $name The name.
     * @return mixed
     * @throws coding_exception When not found.
     */
    public function get($name);

    /**
     * Get all config.
     *
     * @return array
     */
    public function get_all();

    /**
     * Whether the config exists.
     *
     * @param string $name Name of the config.
     * @return bool
     */
    public function has($name);

    /**
     * Set a value.
     *
     * @param string $name Name of the config.
     * @param mixed $value The value.
     * @throws coding_exception When the value is not scalar.
     */
    public function set($name, $value);

    /**
     * Set many.
     *
     * @param array $values Keys are config names, and values are values.
     * @throws coding_exception When a value is not scalar.
     */
    public function set_many(array $values);

}

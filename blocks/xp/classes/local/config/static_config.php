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
 * Static config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

use coding_exception;

/**
 * Static config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class static_config implements config {

    /** @var array Data values. */
    private $data = [];

    /**
     * Constructor.
     *
     * @param array $initialdata The initial data.
     */
    public function __construct(array $initialdata = []) {
        $this->set_many($initialdata);
    }

    /**
     * Get a value.
     *
     * @param string $name The name.
     * @return mixed
     */
    public function get($name) {
        if (!$this->has($name)) {
            throw new coding_exception('Unknown config name.');
        }
        return $this->data[$name];
    }

    /**
     * Get all config.
     *
     * @return array
     */
    public function get_all() {
        return $this->data;
    }

    /**
     * Whether we have that config.
     *
     * @param string $name The config name.
     * @return bool
     */
    public function has($name) {
        return array_key_exists($name, $this->data);
    }

    /**
     * Set a value.
     *
     * @param string $name Name of the config.
     * @param mixed $value The value.
     */
    public function set($name, $value) {
        if (!is_scalar($value)) {
            throw new coding_exception('Value for config is not scalar: ' . $value);
        }
        $this->data[$name] = $value;
    }

    /**
     * Set many.
     *
     * @param array $values Keys are config names, and values are values.
     */
    public function set_many(array $values) {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

}

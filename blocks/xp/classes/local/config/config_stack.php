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
 * Config stack.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

use coding_exception;

/**
 * Config stack.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_stack implements config {

    /** @var config[] Configs. */
    protected $stack;

    /**
     * Constructor.
     *
     * @param config[] $stack Stack of config objects.
     */
    public function __construct(array $stack) {
        array_map(function($config) {
            if (!$config instanceof config) {
                throw new coding_exception('Invalid config object received.');
            }
        }, $stack);
        $this->stack = $stack;
    }

    /**
     * Get a value.
     *
     * @param string $name The name.
     * @return mixed
     */
    public function get($name) {
        foreach ($this->stack as $config) {
            if ($config->has($name)) {
                return $config->get($name);
            }
        }
        throw new coding_exception('Invalid config requested: ' . $name);
    }

    /**
     * Get all config.
     *
     * @return array
     */
    public function get_all() {
        $data = [];
        foreach ($this->stack as $config) {
            $data += $config->get_all();
        }
        return $data;
    }

    /**
     * Whether we have that config.
     *
     * @param string $name The config name.
     * @return bool
     */
    public function has($name) {
        foreach ($this->stack as $config) {
            if ($config->has($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set a value.
     *
     * Sets the value in all matching configs.
     *
     * @param string $name Name of the config.
     * @param mixed $value The value.
     */
    public function set($name, $value) {
        $wasset = false;
        foreach ($this->stack as $config) {
            if ($config->has($name)) {
                $config->set($name, $value);
                $wasset = true;
            }
        }
        if (!$wasset) {
            throw new coding_exception('Invalid config name.');
        }
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

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
 * Filtered config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

use coding_exception;

/**
 * Filtered config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filtered_config extends proxy_config {

    /** @var array Array where keys are allowed to be read. */
    private $allowedkeys = [];
    /** @var array Array where keys are keys to exclude. */
    private $excludedkeys = [];
    /** @var bool Whether or not to check the allowed keys. */
    private $checkallowed = false;

    /**
     * Constructor.
     *
     * @param config $config The config object.
     * @param array $allowedkeys Values are allowed keys, use null to allow everything.
     * @param array $excludedkeys Values are excluded keys.
     */
    public function __construct(config $config, array $allowedkeys = null, array $excludedkeys = null) {
        parent::__construct($config);
        if ($allowedkeys !== null) {
            $this->allowedkeys = array_flip($allowedkeys);
            $this->checkallowed = true;
        }
        $this->excludedkeys = $excludedkeys ? array_flip($excludedkeys) : [];
    }

    /**
     * Get a value.
     *
     * @param string $name The name.
     * @return mixed
     */
    public function get($name) {
        $this->validate($name);
        return parent::get($name);
    }

    /**
     * Get all config.
     *
     * @return array
     */
    public function get_all() {
        $values = parent::get_all();
        if ($this->checkallowed) {
            $values = array_intersect_key($values, $this->allowedkeys);
        }
        return array_diff_key($values, $this->excludedkeys);
    }

    /**
     * Whether we have that config.
     *
     * @param string $name The config name.
     * @return bool
     */
    public function has($name) {
        if (array_key_exists($name, $this->excludedkeys)) {
            return false;
        } if ($this->checkallowed && !array_key_exists($name, $this->allowedkeys)) {
            return false;
        }
        return parent::has($name);
    }

    /**
     * Set a value.
     *
     * @param string $name Name of the config.
     * @param mixed $value The value.
     */
    public function set($name, $value) {
        $this->validate($name);
        return parent::set($name, $value);
    }

    /**
     * Set many.
     *
     * @param array $values Keys are config names, and values are values.
     */
    public function set_many(array $values) {
        $disallowed = array_intersect_key($values, $this->excludedkeys);
        if ($this->checkallowed) {
            $disallowed = array_merge($disallowed, array_diff_key($values, $this->allowedkeys));
        }
        if (!empty($disallowed)) {
            throw new coding_exception('Invalid keys found: ' . implode(', ', array_keys($disallowed)));
        }
        return parent::set_many($values);
    }

    /**
     * Validate whether we can read the key.
     *
     * @param string $name The key.
     * @return void
     */
    public function validate($name) {
        if (array_key_exists($name, $this->excludedkeys)) {
            throw new coding_exception('Invalid key: ' . $name);
        } else if ($this->checkallowed && !array_key_exists($name, $this->allowedkeys)) {
            throw new coding_exception('Invalid key: ' . $name);
        }
    }
}
